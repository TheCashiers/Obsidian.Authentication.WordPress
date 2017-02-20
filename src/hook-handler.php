<?php
    require_once(ROOT_PATH."/oauth-controller.php");
    class obsidian_hook_handler
    {
        /* Handler for Installation*/

        /*
        * Called when plugin is activated
        */
        public static function register_activation_hook_handler()
        {
            add_option("obsidian_servers",null);
        }

        /*
        * Called when plugin is deactivated
        */
        public static function register_deactivation_hook_handler()
        {
            delete_option("obsidian_servers");
        }

        /* Handler for authenticate*/

        /*
        * Called when user is login
        */
        public static function authenticate_handler($user,$username,$password)
        {
            //get all Obsidian-based servers from database;
            $servers = json_decode(get_option("obsidian_servers"));
            if($servers == null) return $user;
            foreach ($servers as $value) {
                //if server grant is not password mode
                if(($value->grant_mode)!="password") continue;
                //no input
                if($username==""&&$password=="") return $user;
                //intercept
                if(!is_wp_error($user)&&$value->password_mode_intercept=="no") return $user;
                //authenticate in a server
                $client = new obsidian_client($value->client_id,$value->client_secret);
                $password_auth = new resource_owner_password_credential_authentication($value->password_mode_request_url,$client);
                $result = $password_auth->authenticate($username,$password,explode(" ",$value->scope_login));
                //check result
                if($result==false||!isset($password_auth->access_token)) return null;
                //decode and get userinfo
                $jwt = json_web_token::decode_jwt($password_auth->access_token);
                $token_id = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier"];
                $token_username = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"];
                $token_email = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"];
                //login to WordPress
                $users = get_users(array("meta_key"=>"obsidian_server_binding_id_".$value->server_name,"meta_value"=>$token_id));
                //if user exist,login
                if(count($users)>0)
                    return $users[0];
                else //in Password mode, user must bind their obsidian user before login in.
                    return null;
            }
            //if no server,use WordPress internal login process
            return $user;
        }

        /*Called when user browser a url*/
        public static function init_handler()
        {
            $url = explode("?",$_SERVER["REQUEST_URI"])[0];
            //$url = rtrim($_SERVER["REQUEST_URI"],"?".$_SERVER["QUERY_STRING"]);
            if(strcasecmp($url,"/obsidian-auth/auth")==0)
                obsidian_oauth_controller::auth_code_handler();
            if(strcasecmp($url,"/obsidian-auth/token")==0)
                obsidian_oauth_controller::code_handler();
                        
        }
    }
?>