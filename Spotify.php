<?php

class Spotify {
    
    public $auth_token;
    public $error;
    public $error_description;
    
    private $api_url = 'https://api.spotify.com/';
    private $version = 'v1/';
    private $endpoint;
    private $token_type;
    private $timeout = 30;
    
    private $client_id;
    private $secret;
    
    public function __construct($client_id, $secret)
    {
        $this->client_id = $client_id;
        $this->secret = $secret;
    }
    
    /* 
     * Get artists
     * @parameters: $artists - array of IDs or just one ID
     * @returns JSON
     */
    public function getArtist($artists = array())
    {
        if(empty($artists))
        {
            return 0;
        }
        
        if(count($artists) > 1)
        {
            $this->endpoint = 'artists?ids=' . explode(',', $artists);
        }else
        {
            $this->endpoint = 'artists/' . $artists[0];
        }
        
        return $this->execute();
    }
    
    /* 
     * Get authorization code and token
     * @returns token string
     */
    public function authorize()
    {        
        $ch = curl_init('https://accounts.spotify.com/api/token');
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->client_id . ':' . $this->secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        
        $result = json_decode(curl_exec($ch));
        curl_close($ch);
        
        if(empty($result->access_token))
        {
            $this->error = $result->error;
            $this->error_description = $result->error_description;
            
            return 0;
        }else
        {
            $this->auth_token = $result->access_token;
            $this->token_type = $result->token_type;
            
            return 1;
        }
    }
    
    /* 
     * Execute the curl call
     * @parameters: $method - POST or GET
     * @returns JSON
     */
    private function execute($method = 'GET')
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $this->token_type . ' ' . $this->auth_token));
        curl_setopt($ch, CURLOPT_URL, $this->api_url . $this->version . $this->endpoint . '?auth_token=' . $this->auth_token);
        
        if($method == 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
        }else
        {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
        
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if($status != 200)
        {
            return $status;
        }
        
        return $result;
    }
}