<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class CryptoHelper {
    private const CIPHER = 'AES-256-CBC';
    private const KEY_LENGTH = 32; // 256 bit
    private const IV_LENGTH = 16;  // 128 bit
    private const PBKDF2_ITERATIONS = 100000;

    /**
     * Deriva una chiave di 32 byte dalla password usando PBKDF2 (SHA-256, 100k iterazioni).
     */
    public static function deriveKeyFromPassword(string $password, string $salt): string {
        return hash_pbkdf2('sha256', $password, $salt, self::PBKDF2_ITERATIONS, self::KEY_LENGTH, true);
    }

    /**
     * Cifra un testo in chiaro con AES-256-CBC usando la chiave fornita.
     * Restituisce base64(iv + ciphertext).
     */
    public static function encrypt(string $plaintext, string $key): string {
        $iv = random_bytes(self::IV_LENGTH);
        $ciphertext = openssl_encrypt($plaintext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) {
            throw new RuntimeException('Cifratura fallita');
        }
        return base64_encode($iv . $ciphertext);
    }

    /**
     * Decifra un payload base64(iv + ciphertext) con AES-256-CBC.
     */
    public static function decrypt(string $payload, string $key): string {
        $data = base64_decode($payload, true);
        if ($data === false) {
            throw new RuntimeException('Payload non valido');
        }
        if (strlen($data) < self::IV_LENGTH) {
            throw new RuntimeException('Payload troppo corto');
        }
        $iv = substr($data, 0, self::IV_LENGTH);
        $ciphertext = substr($data, self::IV_LENGTH);
        $plaintext = openssl_decrypt($ciphertext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        if ($plaintext === false) {
            throw new RuntimeException('Decifratura fallita');
        }
        return $plaintext;
    }

    /**
     * Restituisce il percorso del file chiave del server.
     */
    private static function getServerKeyPath(): string {
        return BASE_PATH . '/config/server_key.php';
    }

    /**
     * Carica la chiave master del server. Se non esiste, la genera e salva automaticamente.
     * La chiave è memorizzata come hex nel file ma restituita come binaria.
     */
    public static function getServerKey(): string {
        static $serverKey = null;
        if ($serverKey !== null) {
            return $serverKey;
        }

        $path = self::getServerKeyPath();
        if (file_exists($path)) {
            $hexKey = include $path;
            if (is_string($hexKey) && strlen($hexKey) === self::KEY_LENGTH * 2 && ctype_xdigit($hexKey)) {
                $serverKey = hex2bin($hexKey);
                return $serverKey;
            }
        }

        // Genera e salva la chiave
        $rawKey = random_bytes(self::KEY_LENGTH);
        $content = "<?php\nreturn '" . bin2hex($rawKey) . "';\n";

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }

        $result = file_put_contents($path, $content, LOCK_EX);
        if ($result === false) {
            throw new RuntimeException('Impossibile salvare la chiave del server in ' . $path);
        }
        chmod($path, 0600);

        return $rawKey;
    }

    /**
     * Cifra la chiave derivata dall'utente con la chiave del server (wrapped key).
     * Restituisce base64(iv + ciphertext) della chiave utente.
     */
    public static function wrapKey(string $userKey): string {
        $serverKey = self::getServerKey();
        return self::encrypt($userKey, $serverKey);
    }

    /**
     * Decifra il wrapped key con la chiave del server per recuperare la chiave derivata dall'utente.
     */
    public static function unwrapKey(string $wrapped): string {
        $serverKey = self::getServerKey();
        return self::decrypt($wrapped, $serverKey);
    }
}
