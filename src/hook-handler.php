<?php
    class obsidian_hook_handler
    {
        /* Handler for Installation*/

        /*
        * Called when plugin is activated
        */
        public static function register_activation_hook_handler()
        {
            //add option into WordPress database
            add_option("obsidian_auth_grant_mode","password");
            add_option("obsidian_auth_password_mode_uri","");
            add_option("obsidian_auth_login_scope",array("ob.basic"));
            add_option("obsidian_auth_client_id","");
            add_option("obsidian_auth_client_secret","");
            add_option("obsidian_auth_password_mode_prevent_user","no");
        }

        /*
        * Called when plugin is deactivated
        */
        public static function register_deactivation_hook_handler()
        {
            //delete option from WordPress database
            delete_option("obsidian_auth_grant_mode");
            delete_option("obsidian_auth_password_mode_uri");
            delete_option("obsidian_auth_login_scope");
            delete_option("obsidian_auth_client_id");
            delete_option("obsidian_auth_client_secret");
            delete_option("obsidian_auth_password_mode_prevent_user");
        }

        /* Handler for authenticate*/

        /*
        * Called when user is login
        */
        public static function authenticate_handler($user,$username,$password)
        {
            $client_id = get_option("obsidian_auth_client_id");
            $client_secret = get_option("obsidian_auth_client_secret");
            $login_scope = get_option("obsidian_auth_login_scope");
            $password_uri = get_option("obsidian_auth_password_mode_uri");
            $intercept_login = get_option("obsidian_auth_password_mode_prevent_user");
            //intercept
            if(!is_wp_error($user)&&$intercept_login=="no") return $user;
            if($username==""&&$password=="") return $user;
            $password_auth = new resource_owner_password_credential_authentication($password_uri);
            //authenticate in an Obsidian-based Server
            $auth_result = $password_auth->authenticate($client_id,$client_secret,$username,$password,$login_scope);
            //if auth_result is false
            if($auth_result==false) return null;
            if($password_auth->access_token=="") return null;
            $jwt = json_web_token::decode_jwt($password_auth->access_token);
            //get userinfo
            $token_username = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"];
            $token_email = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"];
            //login
            //if(strcasecmp($token_username,$username)==false) return null;
            $user_login = get_user_by("login",$token_username);
            //if user doesn't exist,create it.
            if($user_login!=null)
                return $user_login;
            {
                $userdata = array(
                    "user_login" => $token_username,
                    "user_pass" => $password."_obsidian",
                    "user_email" => $token_email
                );
                $user_id = wp_insert_user($userdata);
                if(!is_wp_error($user_id)) return get_user_by("id",$user_id); else return null;
            }
        }
    }
?>