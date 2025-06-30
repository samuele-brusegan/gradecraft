<?php

class User {
    public $username;
    public $password;
    private $rawData;

    //--------------
    public $uid;
    private $pwd;
    public $ident = '';
    public $token = '';
    public $is_logged_in = false;
    private $base_url = 'https://web.spaggiari.eu/rest/v1';
    private $user_agent = 'CVVS/std/4.1.7 Android/10';
    private $api_key = 'Tg1NWEwNGIgIC0K';
    public $last_login_response = null;

    /**
     * @param $username
     * @param $password
     */
    public function __construct($username=null, $password=null) {
        $this->username = $username;
        $this->password = $password;
        $this->uid = $username;
        $this->pwd = $password;
    }


    public function setRawData($rawData) {
        $this->rawData = $rawData;
    }

    public function login(): string|false {
        $url = $this->base_url . '/auth/login';
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
                $this->is_logged_in = true;
                return json_encode($data);
            }
        }
        return false;
    }

    public function getVoti() {
        if (!$this->is_logged_in) return null;
        $url = $this->base_url . '/students/' . $this->ident . '/grades';
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
        return null;
    }
}
?>