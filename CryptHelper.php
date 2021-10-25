<?php

class CryptHelper
{
    private static $key = "ynkphp";

    static function encrypt($msg, $key = null, $iv = null)
    {
        return function_exists('openssl_encrypt') ? self::encrypt_openssl($msg, $key, $iv) : (
            function_exists('mcrypt_encrypt') ? self::encrypt_mcrypt($msg, $key, $iv) : false
        );
    }

    static function decrypt($payload, $key = null)
    {
        return function_exists('openssl_decrypt') ? self::decrypt_openssl($payload, $key) : (
            function_exists('mcrypt_decrypt') ? self::decrypt_mcrypt($payload, $key) : false
        );
    }

    static function encrypt_mcrypt($msg, $key, $iv)
    {
        if (!$key) {
            $key = self::$key;
        }
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        if (!$iv) {
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        }
        $pad = $iv_size - (strlen($msg) % $iv_size);
        $msg .= str_repeat(chr($pad), $pad);
        $encryptedMessage = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $msg, MCRYPT_MODE_CBC, $iv);

        return self::encode($iv . $encryptedMessage);
    }

    static function decrypt_mcrypt($payload, $key)
    {
        if ($key) {
            $key = self::$key;
        }
        $raw = self::decode($payload);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = substr($raw, 0, $iv_size);
        $data = substr($raw, $iv_size);
        $result = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
        $ctrlchar = substr($result, -1);
        $ord = ord($ctrlchar);
        if ($ord < $iv_size && substr($result, -ord($ctrlchar)) === str_repeat($ctrlchar, $ord)) {
            $result = substr($result, 0, -ord($ctrlchar));
        }

        return $result;
    }

    static function encrypt_openssl($msg, $key, $iv)
    {
        if (!$key) {
            $key = self::$key;
        }
        $iv_size = openssl_cipher_iv_length('AES-128-CBC');
        if (!$iv) {
            $iv = openssl_random_pseudo_bytes($iv_size);
        }
        $encryptedMessage = openssl_encrypt($msg, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

        return self::encode($iv . $encryptedMessage);
    }

    static function decrypt_openssl($payload, $key)
    {
        if (!$key) {
            $key = self::$key;
        }
        $raw = self::decode($payload);
        $iv_size = openssl_cipher_iv_length('AES-128-CBC');
        $iv = substr($raw, 0, $iv_size);
        $data = substr($raw, $iv_size);

        return openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }

    static function encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    static function decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
