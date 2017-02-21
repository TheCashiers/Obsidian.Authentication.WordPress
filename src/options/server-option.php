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

        /*Allow user to login with a user unbinded with Obsidian user*/
        public $allow_login_unbind_user_pasword_mode;

        /*Resource Owner Password Credential Grant Information*/
        public $password_mode_request_url;
        //public $password_mode_intercept;

        /*Implict Grant Information*/
        public $token_mode_request_url;
        
        /*Authorization Code Grant Information*/
        public $code_mode_code_request_url;
        public $code_mode_token_request_url;

        public static function get_server_by_name($name)
        {
            if(!is_string($name)) return null;
            $servers = json_decode(get_option("obsidian_servers"));
            if($servers==null) return null;
            foreach ($servers as $value)
                if($value->server_name==$name)
                    return $value;
            return null;
        }
    }
?>