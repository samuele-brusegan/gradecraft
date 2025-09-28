<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */


class ApiController {
    public function classeviva($data="", $isLogin=false, $return=false): array|null {

        $body = json_decode(file_get_contents('php://input'), true);
        if ($data != "") $body = $data;

        $cvvApi = CVV_API;
        // $user = USR;

        // $resp = ["status" => "000", "message" => "API call not started (Commented row?)", "error" => "1"];

        if ($isLogin) $resp = $cvvApi->login();
        else {
            $resp = $cvvApi->genericQuery($body['request'], $body['extraInput'], $body['isPost']);
//            $resp = [];
        }


        if ($return) {
            if (isset($resp['error'])) {
                http_response_code(401); // Unauthorized o altro errore
                return $resp;
            } else {
                http_response_code(200);
                if ($body['cvvArrKey'] != "" && $body['cvvArrKey'] != null && isset($resp[$body['cvvArrKey']])) {
                    return $resp[$body['cvvArrKey']];
                } else {
                    return $resp;
                }
            }
        } else {
            if (isset($resp['error'])) {
                http_response_code(401); // Unauthorized o altro errore
                echo json_encode($resp);
            } else {
                http_response_code(200);
                if ($body['cvvArrKey'] != "" && $body['cvvArrKey'] != null && isset($resp[$body['cvvArrKey']])) {
                    echo json_encode($resp[$body['cvvArrKey']]);
                } else {
                    echo json_encode($resp);
                }
            }
        }
        return null;
        /*if (isset($resp['error'])) {
            http_response_code(401); // Unauthorized o altro errore
            if ($return) return $resp; else echo json_encode($resp);
        } else {
            http_response_code(200);
            if ($body['cvvArrKey'] != "" && $body['cvvArrKey'] != null && isset($resp[$body['cvvArrKey']])) {
                if ($return) return $resp[$body['cvvArrKey']];
                else echo json_encode($resp[$body['cvvArrKey']]);
            } else {
                if ($return) return $resp; else echo json_encode($resp);
            }
        }*/
    }

    public function login(): void {
        function addLoginInCookies(): void {
            $current_usr = $_SESSION['classeviva_username'];
            $current_pwd = $_SESSION['classeviva_password'];
            $current_tkn = $_SESSION['classeviva_auth_token'];
            $current_exp = $_SESSION['classeviva_session_expiration_date'];

            if (isset($_COOKIE['users'])) {
                $users = $_COOKIE['users'];
                $users = json_decode($users, true);

                $flag = false;
                foreach ($users as $user) {
                    if ($user['username'] == $current_usr) {

                        if ($current_pwd != $user['password']) $user['password'] = $current_pwd;
                        if ($current_tkn != $user['token'])    $user['token']    = $current_tkn;
                        if ($current_exp != $user['exp'])      $user['exp']      = $current_exp;
                        $flag = true;
                    }
                }
                if (!$flag) {
                    $users[] = [
                        'username' => $current_usr,
                        'password' => $current_pwd,
                        'token'    => $current_tkn,
                        'exp'      => $current_exp
                    ];
                }
            } else {
                $users[] = [
                    'username' => $current_usr,
                    'password' => $current_pwd,
                    'token'    => $current_tkn,
                    'exp'      => $current_exp
                ];
            }
            setcookie('users', json_encode($users), -1, '/');

//            echo "<pre>"; print_r($_COOKIE); echo "</pre>";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Ottieni i dati
            $usr = $_POST['usr'];
            $pwd = $_POST['pwd'];

            //Convalida i dati
            if ($usr == "" || $pwd == "") { $error = "Username e password sono richiesti."; }

            //Se i dati hanno problemi rimanda alla view
            if (isset($error)) { require_once BASE_PATH . '/app/views/home.php'; }
            //Altrimenti chiama il model
            else {
                $cvvApi = CVV_API;
                $cvvApi->createUser($usr, $pwd);
                $result = $cvvApi->login();

                addLoginInCookies();

                $loginResponse = $result;
//                echo json_encode($result);
                header('Location: /');
                require_once BASE_PATH . '/app/views/home.php';
            }
        }
    }
}