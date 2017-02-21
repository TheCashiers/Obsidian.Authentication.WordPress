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
                if($result==false||!isset($password_auth->access_token))
                    if($value->password_mode_intercept=="no")
                        continue;
                    else
                        return null;
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
                elseif($value->allow_login_unbind_user_pasword_mode=="yes") //if there is a user with a same name in Obsidian server
                {
                    $user_login = get_user_by("login",$token_username);
                    //if allow insert user
                    if(($user_login==null)&&($value->allow_create_user=="yes"))
                    {
                        $userdata = array(
                            "user_login" => str_replace(" ","",$value->server_name)."_".$token_username,
                            "user_pass"  => md5_file(rand().$token_id)."_obsidian",
                            "user_email" => $token_email,
                            "user_nicename" => $token_username,
                            "display_name" => $token_username
                            );
                        $user_login =get_user_by("id",wp_insert_user($userdata));
                    }
                    if($user_login!=null)
                        update_user_meta($user_login->ID,"obsidian_server_binding_id_".$value->server_name,$token_id);
                    return $user_login;
                }
                else
                    if($value->password_mode_intercept=="no")
                        continue;
                    else
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

        /*Called when render login form*/
        public static function login_form_handler()
        {
            $servers = json_decode(get_option("obsidian_servers"));
            foreach($servers as $server)
            {
                if(($server->grant_mode=="token")||($server->grant_mode=="code"))
                printf(__("<p><a class=\"button button-primary button-large\" href=\"".home_url()."/obsidian-auth/auth?server_name=".$server->server_name."&action=login"."\" style=\"margin-bottom:16px;float:none;\" >".__("Login with %s","obsdian-auth")."</a></p>"),$server->server_name);
            }
        }

        public static function edit_user_profile_handler()
        {
            echo("<h2 class=\"title\">".__("Obsidian Binding","obsidian-auth")."</h2>");
            $servers = json_decode(get_option("obsidian_servers"));
            $current_user = wp_get_current_user();
            foreach($servers as $server)
            {
                if($server->grant_mode!="no")
                    if(get_user_meta($current_user->ID,"obsidian_server_binding_id_".$server->server_name)==null)
                        printf(__("<p><a class=\"button button-primary button-large\" href=\"".home_url()."/obsidian-auth/auth?server_name=".$server->server_name."&action=bind"."\" style=\"margin-bottom:16px;float:none;\" >".__("Bind %s account","obsidian-auth")."</a></p>"),$server->server_name);
                    else
                        printf(__("<p><a class=\"button button-primary button-large\" href=\"".home_url()."/obsidian-auth/auth?server_name=".$server->server_name."&action=unbind"."\" style=\"margin-bottom:16px;float:none;\" >".__("Unbind %s account","obsidian-auth")."</a></p>"),$server->server_name);
            }            
        }
    }
?>