<?php
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
                $token_username = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"];
                $token_email = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"];
                //login to WordPress
                $user_login = get_user_by("login",$token_username);
                //if user exist,login
                if($user_login!=null)
                    return $user_login;
                else //if user doesn't exist,create it
                {
                        $userdata = array(
                        "user_login" => $token_username,
                        "user_pass"  => $password."_obsidian",
                        "user_email" => $token_email
                    );
                    $user_id = wp_insert_user($userdata);
                    if(!is_wp_error($user_id)) return get_user_by("id",$user_id); else return null;
                }
            }
        }
    }
?>