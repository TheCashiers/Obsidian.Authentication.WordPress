<?php
    class obsidian_client
    {
        public $client_id;
        public $client_secret;
        public function __construct($id,$secret)
        {
            $this->client_id = $id;
            $this->client_secret = $secret;
        }
    }
?>