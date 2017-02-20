<?php
    class implict_authentication
    {
        public $client;
        public $redirect_url;
        public $request_url;
        public function __construct($client)
        {
            $this->client = $client;
        }
        /* generate query url in an Obsidian-based server to get authorization code*/
        public function generate_token_url($scope)
        {
            return $this->request_url."?response_type=token&client_id=".$this->client->client_id."&redirect_uri=".urlencode($this->redirect_url)."&scope=".join(" ",$scope);
        }
    }
?>