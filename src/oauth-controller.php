<?php
    require_once(ROOT_PATH."/authentication/authorization-code-authentication.php");
    require_once(ROOT_PATH."/authentication/implict-authentication.php");
    require_once(ROOT_PATH."/authentication/client.php");
    class obsidian_oauth_controller
    {
        /*
        * http://hostname/obsidian-auth/token
        *
        * Query String :
        *  - token : Access Token
        */
        public static function code_handler()
        {
            //get accessing server
            $server_name = $_SESSION["obsidian_accessing_server"];
            $action = $_SESSION["obsidian_token_action"];
            $_SESSION["obsidian_accessing_server"]=null;
            $_SESSION["obsidian_token_action"]=null;
            //
            $server = null;
            $servers = json_decode(get_option("obsidian_servers"));
            if($servers==null) wp_redirect(home_url());
            foreach ($servers as $value)
                if($value->server_name==$server_name)
                    $server = $value;
            if($server==null) wp_redirect(home_url());
            //check server mode
            if($server->grant_mode=="password") wp_redirect(home_url());
            $access_token = null;
            if($server->grant_mode=="code")
            {
                //get access token
                $code = $_GET["code"];
                $code_auth = new authorization_code_authentication(new obsidian_client($server->client_id,$server->client_secret));
                $code_auth->token_request_url=$server->code_mode_token_request_url;
                $code_auth->code_redirect_url=home_url()."/obsidian-auth/token";
                $result = $code_auth->get_access_token($code);
                if($result==false||!isset($code_auth->access_token)) wp_redirect(home_url());
                $access_token = $code_auth->access_token;
            }
            if($server->grant_mode=="token")
            {
                $access_token = $_GET["access_token"];
            }
            if(!isset($access_token)) wp_redirect(home_url());
            //decode and get userinfo
            $jwt = json_web_token::decode_jwt($access_token);
            $token_id = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier"];
            $token_username = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"];
            $token_email = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"];
            //if binding user
            if($action=="bind")
            {
                $current_user = wp_get_current_user();
                //if user hasn't login in
                if($current_user->ID==0) wp_redirect(home_url());
                update_user_meta($current_user->ID,"obsidian_server_binding_id_".$server->server_name,$token_id);
                //return to profile.php
                wp_redirect(home_url()."/wp-admin/profile.php");
                exit;
            }
            if($action=="login")
            {
                $users = get_users(array("meta_key"=>"obsidian_server_binding_id_".$server->server_name,"meta_value"=>$token_id));
                $current_user = null;
                if(count($users)>0)
                    $current_user = $users[0];
                elseif($server->allow_create_user=="yes")
                {
                    $userdata = array(
                        "user_login" => str_replace(" ","",$server->server_name)."_".$token_username,
                        "user_pass"  => md5_file(rand().$token_id)."_obsidian",
                        "user_email" => $token_email,
                        "user_nicename" => $token_username,
                        "display_name" => $token_username
                    );
                    $user_id = wp_insert_user($userdata);
                    update_user_meta($user_id,"obsidian_server_binding_id_".$server->server_name,$token_id);
                    $current_user = get_user_by("id",$user_id);
                }
                else
                    wp_redirect(home_url()."/wp-admin/wp-login.php");            
                wp_set_current_user($current_user);
                wp_set_auth_cookie($current_user->ID);
                do_action("wp_login", $current_user->user_login);
                wp_redirect(home_url()."/wp-admin/profile.php");
                exit;
            }
            
        }

        /*
        * http://hostname/obsidian-auth/auth
        *
        * Query String :
        *  - action : login,bind or unbind
        *  - server : Server Name
        */
        public static function auth_code_handler()
        {
            $action = $_GET["action"];
            $server_name = $_GET["server_name"];
            $server = null;
            $servers = json_decode(get_option("obsidian_servers"));
            if($servers==null) wp_redirect(home_url());
            foreach ($servers as $value)
                if($value->server_name==$server_name)
                    $server = $value;
            if($server==null) wp_redirect(home_url());
            //
            $client = new obsidian_client($server->client_id,$server->client_secret);
            if(($action=="login")||($action=="bind"))
            {
                $_SESSION["obsidian_accessing_server"]=$server_name;
                $_SESSION["obsidian_token_action"]=$action;
                $url = home_url();
                if($server->grant_mode == "code")
                {
                    $auth_code = new authorization_code_authentication($client);
                    $auth_code->code_request_url = $server->code_mode_code_request_url;
                    $auth_code->code_redirect_url = home_url()."/obsidian-auth/token";
                    $url = $auth_code->generate_code_url(explode(" ",$server->scope_login));
                }
                if($server->grant_mode == "token")
                {
                    $token_auth = new implict_authentication($client);
                    $token_auth->redirect_url = home_url()."/obsidian-auth/token";
                    $token_auth->request_url = $server->token_mode_request_url;
                    $url = $token_auth->generate_token_url(explode(" ",$server->scope_login));
                }
                if($server->grant_mode == "password")
                {
                    $current_user = wp_get_current_user();
                    //if user hasn't login in
                    if($current_user->ID==0) wp_redirect(home_url());
                    $password_auth = new resource_owner_password_credential_authentication($server->password_mode_request_url,$client);
                    $result = $password_auth->authenticate($current_user->user_login,$_GET["password"],explode(" ",$server->scope_login));
                    if($result!=false&&isset($password_auth->access_token))
                    {
                        $jwt = json_web_token::decode_jwt($password_auth->access_token);
                        $token_id = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier"];
                        update_user_meta($current_user->ID,"obsidian_server_binding_id_".$server->server_name,$token_id);
                    }
                    wp_redirect(home_url()."/wp-admin/profile.php");
                    exit;
                }
                wp_redirect($url);
                exit;
            }
            if($action=="unbind")
            {
                $current_user = wp_get_current_user();
                //if user hasn't login in
                if($current_user->ID==0) wp_redirect(home_url());
                delete_user_meta($current_user->ID,"obsidian_server_binding_id_".$server->server_name);
                wp_redirect(home_url()."/wp-admin/profile.php");   
                exit;          
            }
        }
    }
?>