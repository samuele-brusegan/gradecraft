<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

include_once 'collegamenti.php';
include_once 'apiMethods.php';
$c = new Collegamenti();

class User {
    //--------------
    //private $base_url = 'https://web.spaggiari.eu/rest/v1';
    public string $uid;
    public string $pwd;
    public string $ident = '';
    public string $token = '';
    public bool $is_logged_in = false;
    private string $user_agent = 'CVVS/std/4.1.7 Android/10';
    private string $api_key = 'Tg1NWEwNGIgIC0K';
    public $last_login_response = null;
    public string $expDt;
    public string $reqDt;


    /**
     * Initializes a new instance of the class and assigns the provided username and password.
     *
     * @param string|null $username The username for authentication. Defaults to null if not provided.
     * @param string|null $password The password for authentication. Defaults to null if not provided.
     * @return void
     */
    public function __construct(string $username=null, string $password=null) {
        $this->uid = $username;
        $this->pwd = $password;
    }


    /**
     * Sends an HTTP request to the specified URL using cURL, with predefined headers for authentication and content type.
     *
     * @param string $url The URL to which the request is sent.
     * @return mixed Returns the decoded JSON response if the HTTP status code is 200. Otherwise, returns an associative array containing the status code, raw response message, URL, headers, and response body.
     */
    private function sendRequest(string $url, bool $isPost=false, string $request=null): mixed {
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
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode == 200) {

            global $methods;
            if ($request != null) {
                foreach ($methods as $key => $value) {
                    if ($value['url'] == $request && isset($value['particularDataParse']) && $value['particularDataParse'] == 'PDF'){
                        return ["pdfData" => $response];
                    }
                }
            }

            return json_decode($response, true);
        }
        return ["error" => "HTTP_CODE_DIFFERS_FROM_200", "status" => $httpcode, "message" => $response, "url" => $url, "headers" => $headers, "body" => $response];
    }

    public function login(): string|false {
        global $c;
        $url = $c -> collegamenti['login'];
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
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->last_login_response = [
            'httpcode' => $httpcode,
            'raw' => $response,
            'decoded' => json_decode($response, true)
        ];
        if ($httpcode == 200) {
            $data = $this->last_login_response['decoded'];
            if (isset($data['ident']) && isset($data['token'])) {
                preg_match('/\d+/', $data['ident'], $matches);
                $this->ident = $matches[0];
                $this->token = $data['token'];
                //print_r($data);
                $this->expDt = $data['expire'];
                $this->reqDt = $data['release'];


                $this->is_logged_in = true;
                return json_encode($data);
            }
        }
        return false;
    }

    public function genericQuery($request, $extraInput = null, $isPost=false) {
        if (!$this->is_logged_in) return ["error" => "NOT_LOGGED_IN", "message" => "Prima di chiamare un API (diversa da login) devi loggarti. Contattare il dev se vedete questo errore", "instr"=>"Per loggarti, chiamare questo stesso file in POST con path = login, nel body passare username e password."];
        global $c;
        if ($extraInput != null) {
            foreach ($extraInput as $key => $value) {
                $c->setGeneric($key, $value);
            }
        }
        $c->setIdent($this->ident);
        $url = $c -> collegamenti[$request];

        return $this->sendRequest($url, $isPost, $request);
    }


}
?>