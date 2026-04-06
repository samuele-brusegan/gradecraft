<?php
/*
 * Script per generare server_key.php da caricare manualmente sul server.
 * Eseguire: php scripts/generate_server_key.php
 */

$key = bin2hex(random_bytes(32));
$content = "<?php\nreturn '{$key}';\n";

// Salva localmente
$localPath = __DIR__ . '/../config/server_key.php';
file_put_contents($localPath, $content);
echo "File generato: {$localPath}\n";
echo "Chiave: {$key}\n\n";

// Ora carica la chiave anche nella sessione corrente per il debug
echo "Ora carica il file config/server_key.php nel deployment del server,\n";
echo "oppure crea manualmente un file server_key.php con questo contenuto:\n\n";
echo $content;
