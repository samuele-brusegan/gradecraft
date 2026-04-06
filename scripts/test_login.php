#!/usr/bin/env php
<?php
/**
 * Script per testare il login a Classeviva con verbose cURL
 *
 * Uso: php scripts/test_login.php <username> <password> [--compare]
 */

// Bootstrapping minimale
define('BASE_PATH', __DIR__ . '/..');
define('API_DETACH', false);

require_once BASE_PATH . '/config/CryptoHelper.php';
require_once BASE_PATH . '/config/CredentialStore.php';
CryptoHelper::getServerKey();

require_once BASE_PATH . '/app/core/cvv/collegamenti.php';
require_once BASE_PATH . '/app/core/cvv/apiMethods.php';
require_once BASE_PATH . '/app/core/cvv/User.php';
require_once BASE_PATH . '/app/core/cvv/ClassevivaIntegration.php';

// Parse args
$username = $argv[1] ?? null;
$password = $argv[2] ?? null;
$compare = in_array('--compare', $argv);

if (!$username || !$password) {
    echo "Uso: php scripts/test_login.php <username> <password> [--compare]\n";
    exit(1);
}

// Setup globals
$GLOBALS['CVV_URLS'] = new \cvv\Collegamenti();
$year = '';
if (file_exists(BASE_PATH . '/.env')) {
    $envLines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($k, $v) = explode('=', $line, 2);
            if (trim($k) === 'CLASSEVIVA_YEAR') { $year = trim($v); break; }
        }
    }
}
$GLOBALS['CVV_URLS']->setGeneric('year', $year);

echo "=== GradeCraft Login Test ===\n";
echo "CLASSEVIVA_YEAR: '$year'\n";
echo "Username: $username (" . strlen($username) . " chars)\n";
echo "Password length: " . strlen($password) . " chars\n\n";

// Direct login with verbose
$cvv = new \cvv\CvvIntegration();
$cvv->createUser($username, $password);

$useOriginal = true; // Toggle: false for verbose version

if ($useOriginal) {
    echo "[INFO] Login chiamato con versione VERBOSE\n";
}

echo "\n--- Tentativo login ---\n";
$result = $cvv->login();
echo "\n--- Risultato ---\n";
echo json_encode(json_decode($result, true) ?? $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

// Ora test con cURL raw per avere dati extra
echo "=== Test cURL Raw (con verbose) ===\n";
$c = $GLOBALS['CVV_URLS'];
$url = $c->collegamenti['login'];
echo "URL: $url\n\n";

$verbose = fopen('php://temp', 'w+');
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$body = json_encode(['ident' => null, 'pass' => $password, 'uid' => $username], true);
echo "Body: $body\n\n";
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: CVVS/std/4.1.7 Android/10',
    'Content-Type: application/json',
    'Z-Dev-ApiKey: Tg1NWEwNGIgIC0K'
]);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $verbose);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$sentHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
$primaryIp = curl_getinfo($ch, CURLINFO_PRIMARY_IP);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
$effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
$sslVerifyResult = curl_getinfo($ch, CURLINFO_SSL_VERIFYRESULT);

rewind($verbose);
$verboseLog = stream_get_contents($verbose);
fclose($verbose);
curl_close($ch);

echo "--- SENT HEADERS ---\n$sentHeaders\n\n";
echo "--- CURL INFO ---\n";
echo "HTTP Code: $httpCode\n";
echo "Primary IP: $primaryIp\n";
echo "Redirect URL: $redirectUrl\n";
echo "Effective URL: $effectiveUrl\n";
echo "SSL Verify Result: $sslVerifyResult\n\n";
echo "--- VERBOSE LOG ---\n$verboseLog\n";
echo "--- RESPONSE ---\n";
echo substr($response ?? '', 0, 1000) . "\n\n";
echo "=== END ===\n";
?>
