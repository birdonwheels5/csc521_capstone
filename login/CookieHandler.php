<?php
  
include "Cookie.php";

class CookieHandler 
{
    //Random 256-bit key
    private $SECRET_KEY = "B5A75BD864EBA5837F9727ED73884";
    
    // Cookie attributes
    private $cookie_name = "compsecp1";
    private $cookie_directory = "/";
    private $hmac_hash = "";
    private $is_valid = false;
    private $exists = false;
    
    
    public function __construct()
    {
        
    }

    private function generate_cookie($cookie) 
    {
        $session_id = $cookie->get_session_id();
        
        // Generate hash
        $key = hash_hmac( 'sha256', $cookie->get_uuid() . $cookie->get_expiration() . $session_id, $this->SECRET_KEY );
        $this->hmac_hash = hash_hmac( 'sha256', $cookie->get_uuid() . $cookie->get_expiration() . $session_id, $key );
        
        $cookie_plaintext = ($cookie->get_uuid() . '|' . $cookie->get_session_id() . '|' . $this->hmac_hash . '|' . $cookie->get_expiration());

        return $cookie_plaintext;
    }
    
    public function set_cookie($cookie_name, $cookie) 
    {
        $cookie_plaintext = $this->generate_cookie($cookie);

        if (!setcookie($cookie_name, $cookie_plaintext, $cookie->get_expiration(), $this->cookie_directory)) 
        {
            //echo("ERROR: Unable to create cookie");
            return false;
        }
        return true;
    }
    
    public function delete_cookie($cookie_name)
    {
        // Sets a new cookie that expires immediately after being placed
        setcookie($cookie_name, "", 1, $this->cookie_directory);
    }
    
    // Check if cookie exists
    public function cookie_exists($cookie_name)
    {
        if (empty($_COOKIE[$cookie_name]))
        {
            $this->exists = false;
        }
        else
        {
            $this->exists = true;
        }
    }
    
    // This function will create a new cookie object with values found
    // from the global cookie variable
    // It will return the cookie to the caller
    public function get_cookie($cookie_name)
    {
        $cookie_plaintext = $_COOKIE[$cookie_name];
        
        // Username will be in $array[0], hashed password in $array[1]
        // MAC in $array[2] and cookie expiration in $array[3]
        $array = array();
        $array = explode('|', $cookie_plaintext);
        
        // The reason we have "::" is because normally you cannot have
        // multiple constructors, but we can use the factory pattern to do it
        // Note: When using the factory pattern, you don't use the "new" keyword!
        $cookie = Cookie::retrieve($array[0], $array[1], $array[2], $array[3]);
        
        return $cookie;
    }
    
    // Verifies that a cookie hash not been tampered with and is valid
    public function validate_cookie($cookie, $session_id)
    {
        $expiration = $cookie->get_expiration();
        
        if (time() > $expiration)
        {
            $this->is_valid = false;
        }

        $key = hash_hmac( 'sha256', $cookie->get_uuid() . $cookie->get_expiration() . $session_id, $this->SECRET_KEY );
        $hash = hash_hmac( 'sha256', $cookie->get_uuid() . $cookie->get_expiration() . $session_id, $key );
        
        $hmac = $cookie->get_hmac_hash();
        
        if (hash_equals($hmac, $hash))
        {
            $this->is_valid = true;
        }
        else
        {
             $this->is_valid = false;
        }
    }
    
    public function get_cookie_name()
    {
        return $this->cookie_name;
    }
    
    public function get_exists()
    {
        return $this->exists;
    }
    
    public function get_validity()
    {
        return $this->is_valid;
    }
    
    /*
     * These functions are not used
     * 
    public function get_cookie_directory()
    {
        return $this->cookie_directory;
    }
    
    public function get_hmac_hash()
    {
        return $this->hmac_hash;
    }*/
}
