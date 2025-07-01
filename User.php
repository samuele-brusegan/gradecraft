<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

include_once 'collegamenti.php';
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
     * @param $username
     * @param $password
     */
    public function __construct($username=null, $password=null) {
        $this->uid = $username;
        $this->pwd = $password;
    }

    /**
     * @param $url
     * @return mixed|null
     */
    private function sendRequest($url): mixed {
        $headers = [
            'User-Agent: ' . $this->user_agent,
            'Content-Type: application/json',
            'Z-Dev-ApiKey: ' . $this->api_key,
            'Z-Auth-Token: ' . $this->token
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode == 200) {
            return json_decode($response, true);
        }
        return ["status" => $httpcode, "message" => $response, "url" => $url, "headers" => $headers, "body" => $response];
    }

    public function login(): string|false {
        global $c;
        $url = $c -> login;
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

    public function getVoti() {
        if (!$this->is_logged_in) return "Errore: non sei loggato";
        global $c;
        $c->setIdent($this->ident);
        $url = $c -> voti;

        return $this->sendRequest($url);
    }
    public function getSubjects() {
        if (!$this->is_logged_in) return null;
        global $c;
        $c->setIdent($this->ident);
        $url = $c -> materie;

        return $this->sendRequest($url);
    }


}
?>