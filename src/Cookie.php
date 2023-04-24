<?php

namespace BRdev\Cookie;

class Cookie
{
    private $key;
    private $expire;
    private $id;

    public function __construct($key, $expire = 3600)
    {
        $this->key = $key;
        $this->expire = $expire;

        if (!isset($_COOKIE['session_id'])) {
            $this->id = $this->generateId();
            setcookie('session_id', $this->id, time() + $this->expire, '/', '', true, true);
        } else {
            $this->id = $_COOKIE['session_id'];
        }
    }

    public function __get($name)
    {
        if (!empty($_COOKIE[$this->id . '_' . $name])) {
            return $this->get($name);
        }
        return null;
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function all(): ?object
    {
        return (object)$_COOKIE;
    }

    public function has($name)
    {
        return isset($_COOKIE[$this->id . '_' . $name]);
    }

    public function set($name, $value): Cookie
    {
        $cookieValue = $this->encrypt(json_encode((is_array($value) ? (object)$value : $value)));
        setcookie($this->id . '_' . $name, $cookieValue, time() + $this->expire, '/', '', true, true);
        return $this;
    }

    public function get($name)
    {
        if ($this->has($name)) {
            return json_decode($this->decrypt($_COOKIE[$this->id . '_' . $name]), true);
        }
        return null;
    }

    public function delete($name)
    {   
        if($this->has($name)){
            if (setcookie($this->id . '_' . $name, "", -1, '/', '', true, true)) {
                unset($_COOKIE[$this->id . '_' . $name]);
                return true;
            }
        }
        return false;
    }

    public function deleteAll()
    {
        foreach ($_COOKIE as $key => $value) {
            setcookie($key, "", -1, '/', '', true, true);
        }
    }

    private function generateId()
    {
        return bin2hex(random_bytes(16));
    }

    private function encrypt($data)
    {
        $key = $this->key;
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    private function decrypt($data)
    {
        $key = $this->key;
        list($encryptedData, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
    }
}
