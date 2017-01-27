<?php

class Cookie
{
        
    // Cookie attributes
    private $uuid = "";
    private $session_id = "";
    private $expiration = 0;
    private $hmac_hash = "";
    
    public function __construct()
    {
        
    }
    
    // Constructor for creating new cookies
    public function create($uuid, $session_id)
    {
        $cookie = new Cookie();
        $cookie->set_uuid($uuid);
        $cookie->set_session_id($session_id);
        $cookie->set_expiration(43200); // 12 hours
        return $cookie;
    }
    
    // Constructor for retrieving placed cookies
    public function retrieve($uuid, $session_id, $hmac_hash, $expiration)
    {
        $cookie = new Cookie();
        $cookie->set_uuid($uuid);
        $cookie->set_session_id($session_id);
        $cookie->set_hmac_hash($hmac_hash);
        $cookie->expiration = $expiration;
        return $cookie;
    }

    private function set_uuid($uuid)
    {
        $this->uuid = $uuid;
    }
    
    private function set_session_id($session_id)
    {
        $this->session_id = $session_id;
    }
    
    // Time to expire in seconds.
    private function set_expiration($time_to_expire)
    {
        $time = time();
        $this->expiration = $time + $time_to_expire;
    }
    
    private function set_hmac_hash($hmac_hash)
    {
        $this->hmac_hash = $hmac_hash;
    }
    
    public function get_uuid()
    {
        return $this->uuid;
    }
    
    public function get_session_id()
    {
        return $this->session_id;
    }
    
    public function get_expiration()
    {
        return $this->expiration;
    }
    
    public function get_hmac_hash()
    {
        return $this->hmac_hash;
    }
}
