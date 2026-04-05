<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class CredentialStore {
    private const DATA_DIR = BASE_PATH . '/data';

    /**
     * Genera un nome file sicuro dall'username.
     */
    private static function getFilePath(string $username): string {
        $hash = hash('sha256', $username);
        return self::DATA_DIR . '/credentials_' . $hash . '.enc';
    }

    /**
     * Salva le credenziali cifrate per un utente.
     * La chiave è derivata dalla password tramite PBKDF2, cifrata con AES-256-CBC.
     * La chiave derivata viene poi "wrappata" (cifrata) con la chiave del server
     * per permettere la decifratura automatica al riavvio senza richiedere la password.
     */
    public static function save(string $username, string $password, string $token = '', string $exp = ''): void {
        if (!is_dir(self::DATA_DIR)) {
            mkdir(self::DATA_DIR, 0750, true);
        }

        // Deriva chiave dalla password (PBKDF2, SHA-256, 100k iterazioni)
        $salt = random_bytes(16);
        $userKey = CryptoHelper::deriveKeyFromPassword($password, $salt);

        // Cifra le credenziali con la chiave derivata
        $credentials = json_encode([
            'username' => $username,
            'password' => $password,
            'token'    => $token,
            'exp'      => $exp,
        ]);
        $ciphertext = CryptoHelper::encrypt($credentials, $userKey);

        /* Wrapped key: cifra la chiave derivata con la chiave del server.
         * Serve per poter decifrare le credenziali senza conoscere la password
         * (es. al riavvio del server quando la sessione scade). */
        $wrappedKey = CryptoHelper::wrapKey($userKey);

        $data = json_encode([
            'salt'        => bin2hex($salt),
            'wrapped_key' => $wrappedKey,
            'ciphertext'  => $ciphertext,
        ], JSON_THROW_ON_ERROR);

        $path = self::getFilePath($username);
        $result = file_put_contents($path, $data, LOCK_EX);
        if ($result === false) {
            throw new RuntimeException('Impossibile salvare le credenziali in ' . $path);
        }
        chmod($path, 0600);
    }

    /**
     * Carica e decifra le credenziali di un utente.
     * Usa il wrapped key (decifrato con chiave server) per recuperare la chiave derivata
     * e decifrare le credenziali — non serve la password.
     */
    public static function load(string $username): ?array {
        $path = self::getFilePath($username);
        if (!file_exists($path)) {
            return null;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }

        $data = json_decode($raw, true);
        if ($data === null) {
            return null;
        }

        try {
            // Unwrap: recupera la chiave derivata usando la chiave del server
            $userKey = CryptoHelper::unwrapKey($data['wrapped_key']);

            // Decifra le credenziali
            $plaintext = CryptoHelper::decrypt($data['ciphertext'], $userKey);
            $credentials = json_decode($plaintext, true);

            if ($credentials === null) {
                return null;
            }

            return $credentials;
        } catch (Throwable $e) {
            error_log('CredentialStore::load fallita per ' . $username . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Restituisce la lista di tutti gli username salvati.
     */
    public static function listUsers(): array {
        $users = [];
        $pattern = self::DATA_DIR . '/credentials_*.enc';
        foreach (glob($pattern) as $file) {
            $filename = basename($file); // es: credentials_abc123.enc
            $users[] = preg_replace('/^credentials_/', '', preg_replace('/\.enc$/', '', $filename));
        }
        return $users;
    }

    /**
     * Elimina le credenziali salvate per un utente.
     */
    public static function delete(string $username): void {
        $path = self::getFilePath($username);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Tenta di caricare le credenziali del primo (e spesso unico) utente salvato.
     * Utile per l'autoload quando la sessione è persa ma le credenziali persisted su disco.
     */
    public static function loadStored(): ?array {
        $users = self::listUsers();
        if (empty($users)) {
            return null;
        }
        // Per ogni utente salvato (hash), proviamo a decifrare il primo
        foreach ($users as $userHash) {
            foreach (glob(self::DATA_DIR . '/credentials_*.enc') as $file) {
                if (strpos(basename($file), $userHash) !== false) {
                    $raw = file_get_contents($file);
                    if ($raw === false) continue;
                    $data = json_decode($raw, true);
                    if ($data === null) continue;
                    try {
                        $userKey = CryptoHelper::unwrapKey($data['wrapped_key']);
                        $plaintext = CryptoHelper::decrypt($data['ciphertext'], $userKey);
                        $credentials = json_decode($plaintext, true);
                        if ($credentials !== null) {
                            return $credentials;
                        }
                    } catch (Throwable $e) {
                        error_log('CredentialStore::loadStored fallita: ' . $e->getMessage());
                    }
                }
            }
        }
        return null;
    }
}
