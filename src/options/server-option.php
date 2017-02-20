<?php
    /*Represent an Obsidian-based Authentication Server*/
    class server_option
    {
        /*Server Basic Information*/

        public $server_name;
        /*
        * Possible Value:
        * - password : Resource Owner Password Credential Grant
        * - token : Implict Grant
        * - code : Authorization Code Grant
        */
        public $grant_mode;
        public $client_id;
        public $client_secret;
        public $scope_login;

        /*Allow plugin to login with a user not existing in WordPress*/
        public $allow_create_user;

        /*Resource Owner Password Credential Grant Information*/
        public $password_mode_request_url;
        public $password_mode_intercept;

        /*Implict Grant Information*/
        public $token_mode_request_url;
        
        /*Authorization Code Grant Information*/
        public $code_mode_code_request_url;
        public $code_mode_token_request_url;
    }
?>