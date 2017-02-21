<?php
    require_once(ROOT_PATH."/oauth-controller.php");
    require_once(ROOT_PATH."/views/view-controller.php");
    require_once(ROOT_PATH."/authentication/resource-owner-password-credential-authentication.php");
    require_once(ROOT_PATH."/options/server-option.php");
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
            //if "obsidian_auth_server" was posted, use specified server.Or use the first password mode server in the database
            //when there is no other password mode server or obsidian_disable_internal_auth is "yes",login with WordPress Internal Authentication
            if(($_POST["obsidian_auth_server"]=="wp_internal")&&(get_option("obsidian_disable_internal_auth")=="no")) return $user;
            $server = server_option::get_server_by_name($_POST["obsidian_auth_server"]);
  
            if($server==null) foreach($servers as $value) if($value->grant_mode=="password"){$server=$value; break;}
            if($server==null) return $user;
            //authenticate in a server
            $client = new obsidian_client($server->client_id,$server->client_secret);
            $password_auth = new resource_owner_password_credential_authentication($server->password_mode_request_url,$client);
            $result = $password_auth->authenticate($username,$password,explode(" ",$server->scope_login));
            if($result==false||!isset($password_auth->access_token)) return null;
            //decode and get userinfo
            $jwt = json_web_token::decode_jwt($password_auth->access_token);
            $token_id = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier"];
            $token_username = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"];
            $token_email = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"];
            //login to WordPress
            $users = get_users(array("meta_key"=>"obsidian_server_binding_id_".$server->server_name,"meta_value"=>$token_id));
            //if user exist,login
            if(count($users)>0)
                return $users[0];
            elseif($server->allow_login_unbind_user_pasword_mode=="yes") //if there is a user with a same name in Obsidian server
            {
                $user_login = get_user_by("login",$token_username);
                //if allow insert user
                if(($user_login==null)&&($server->allow_create_user=="yes"))
                {
                    $userdata = array(
                        "user_login" => str_replace(" ","",$server->server_name)."_".$token_username,
                        "user_pass"  => md5_file(rand().$token_id)."_obsidian",
                        "user_email" => $token_email,
                        "user_nicename" => $token_username,
                        "display_name" => $token_username
                        );
                    $user_login =get_user_by("id",wp_insert_user($userdata));
                }
                if($user_login!=null)
                    update_user_meta($user_login->ID,"obsidian_server_binding_id_".$server->server_name,$token_id);
                return $user_login;
            }            
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
            view_controller::login_form();
        }

        /*Called when render user profile page*/
        public static function edit_user_profile_handler()
        {
            view_controller::edit_user_profile();
        }

        /*Called when render admin menu*/
        public static function admin_menu_handler()
        {
            add_menu_page( __("Authentication Servers List","obsidian-auth"), __("Obsidian Options","obsidian-auth"), "administrator", "obsidian_list_servers","view_controller::client_administrator_list_servers");
            add_submenu_page( "obsidian_list_servers", __("Add new server","obsidian-auth"), __("Add new server","obsidian-auth") , "administrator", "obsidian_add_server","view_controller::client_administrator_add_server");
        }
    }
?>