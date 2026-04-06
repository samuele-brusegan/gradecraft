<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

namespace cvv;


include_once 'collegamenti.php';
include_once 'apiMethods.php';

class User {
    public string $uid;
    public string $pwd;
    public string $ident = '';
    public string $token = '';
    public bool $is_logged_in = false;
    private string $user_agent = 'CVVS/std/4.1.7 Android/10';
    private string $api_key = 'Tg1NWEwNGIgIC0K';
    public mixed $last_login_response = null;
    public string $expDt;
    public string $reqDt;
    public string $firstName;
    public string $lastName;
    public string $tokenAP;


    public function __construct(string $username = null, string $password = null) {
        $this->uid = $username;
        $this->pwd = $password;
    }


    private function sendRequest(string $url, bool $isPost = false, string $request = null): mixed {
        if (API_DETACH) return ["error" => "API_DETACH", "message" => "API_DETACHED"];
        $headers = [
            'User-Agent: ' . $this->user_agent,
            'Content-Type: application/json',
            'Z-Dev-ApiKey: ' . $this->api_key,
            'Z-Auth-Token: ' . $this->token
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, $isPost);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        error_log("Classeviva API: URL=$url, HTTP=$httpCode, cURL_error=$curlError, cURL_errno=$curlErrno");
        if ($httpCode != 200) {
            error_log("Classeviva API ERROR response: " . substr($response, 0, 500));
        }

        if ($httpCode == 200) {
            global $methods;
            if ($request != null) {
                foreach ($methods as $value) {
                    if (isset($value['url']) && $value['url'] == $request && isset($value['particularDataParse']) && $value['particularDataParse'] == 'PDF') {
                        return ["pdfData" => $response];
                    }
                }
            }
            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Classeviva JSON decode error: " . json_last_error_msg() . " on response: " . substr($response, 0, 500));
            }
            return $decoded;
        }
        return ["error" => "HTTP_CODE_DIFFERS_FROM_200", "status" => $httpCode, "message" => $response, "url" => $url, "headers" => $headers, "body" => $response, "curl_error" => $curlError, "curl_errno" => $curlErrno];
    }

    public function login(): string|false {
        $c = $GLOBALS['CVV_URLS'];
        $url = $c->collegamenti['login'];
        error_log("User::login() - attempting login, uid={$this->uid}, uid_len=" . strlen($this->uid) . ", pwd_len=" . strlen($this->pwd));
        $headers = [
            'User-Agent: ' . $this->user_agent,
            'Content-Type: application/json',
            'Z-Dev-ApiKey: ' . $this->api_key
        ];
        $body = json_encode([
            'ident' => null,
            'pass' => $this->pwd,
            'uid' => $this->uid
        ]);
        // DEBUG: write cURL debug to file to avoid Docker log truncation
        $verboseFile = '/tmp/curl_login_debug.log';
        $verbose = fopen($verboseFile, 'w');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $sentHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);

        // Write everything to file
        $debug = "=== CURL VERBOSE LOGIN DEBUG ===\n";
        $debug .= "--- REQUEST BODY ---\n" . $body . "\n";
        $debug .= "--- HEADERS ARRAY ---\n" . print_r($headers, true) . "\n";
        $debug .= "--- SENT HEADERS ---\n" . $sentHeaders . "\n";
        $debug .= "--- CURL FULL INFO ---\n" . json_encode(curl_getinfo($ch), JSON_PRETTY_PRINT) . "\n";
        fclose($verbose);
        $debug .= "--- VERBOSE LOG (from file) ---\n" . file_get_contents($verboseFile) . "\n";
        $debug .= "--- RESPONSE ($httpCode) ---\n" . ($response ?? '(empty)') . "\n";
        $debug .= "=== END CURL VERBOSE LOGIN DEBUG ===\n";
        file_put_contents($verboseFile, $debug);

        error_log("CURL LOGIN DEBUG WRITTEN TO /tmp/curl_login_debug.log (HTTP=$httpCode)");

        curl_close($ch);
        $this->last_login_response = [
            'url' => $url,
            'httpcode' => $httpCode,
            'raw' => $response,
            'decoded' => json_decode($response, true)
        ];
        if ($httpCode == 200) {
            $data = $this->last_login_response['decoded'];
            error_log("User::login() response data: " . print_r($data, true));
            error_log("User::login() extracted ident: {$this->ident}, token_len=" . strlen($this->token));
            if (isset($data['ident']) && isset($data['token'])) {
                preg_match('/\d+/', $data['ident'], $matches);
                $this->ident = $matches[0];
                $this->token = $data['token'];
                $this->expDt = $data['expire'];
                $this->reqDt = $data['release'];
                $this->firstName = $data['firstName'];
                $this->lastName = $data['lastName'];


                $this->is_logged_in = true;
                return json_encode($data);
            }
        }
        //Se ci sono problemi
        return json_encode($this->last_login_response);
    }

    public function genericQuery($request, $extraInput = null, $isPost = false) {
        if (!$this->is_logged_in) return ["error" => "NOT_LOGGED_IN", "message" => "Prima di chiamare un API (diversa da login) devi autenticarti. Contattare il dev se vedete questo errore", "instr" => "Per autenticarti, chiamare questo stesso file in POST con path = login, nel body passare username e password."];
        $c = $GLOBALS['CVV_URLS'];
        if ($extraInput != null) {
            foreach ($extraInput as $key => $value) {
                $c->setGeneric($key, $value);
            }
        }
        $c->setIdent($this->ident);
        $url = $c->collegamenti[$request];

        $tmp = $this->sendRequest($url, $isPost, $request);
        return $tmp;
    }


}

?>
