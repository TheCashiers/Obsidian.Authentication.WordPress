<?php
    require_once(ROOT_PATH."/authentication/authorization-code-authentication.php");
    require_once(ROOT_PATH."/authentication/implict-authentication.php");
    require_once(ROOT_PATH."/authentication/client.php");
    require_once(ROOT_PATH."/options/server-option.php");
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
            $user_id = $_SESSION["obsidian_editing_user_id"];
            $_SESSION["obsidian_accessing_server"]=null;
            $_SESSION["obsidian_token_action"]=null;
            $_SESSION["obsidian_editing_user_id"]=null;
            //admin can modify other user setting
            $isadmin = in_array(wp_get_current_user()->roles,array("administrator"));
            if(!isadmin&&($user_id!=(wp_get_current_user()->ID)))
            {
                wp_redirect(home_url());
                exit;
            }
            $server = server_option::get_server_by_name($server_name);
            if($server==null) wp_redirect(home_url());
            switch ($server->grant_mode) {
                case "password":
                    wp_redirect(home_url());
                    break;
                case "token":
                    $access_token = $_GET["access_token"];
                    break;
                case "code":
                    $code = $_GET["code"];
                    $code_auth = new authorization_code_authentication(new obsidian_client($server->client_id,$server->client_secret));
                    $code_auth->token_request_url=$server->code_mode_token_request_url;
                    $code_auth->code_redirect_url=home_url()."/obsidian-auth/token";
                    $result = $code_auth->get_access_token($code);
                    if($result==false||!isset($code_auth->access_token)) wp_redirect(home_url());
                    $access_token = $code_auth->access_token;
                    break;
            }
            if(!isset($access_token)) wp_redirect(home_url());
            //decode and get userinfo
            $jwt = json_web_token::decode_jwt($access_token);
            $token_id = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier"];
            $token_username = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"];
            $token_email = $jwt->custom_claims["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"];
            //if binding user
            switch ($action) {
                case "bind":
                    $current_user = get_user_by("id",$user_id);
                    //if user hasn't login in
                    if($current_user->ID==0) wp_redirect(home_url());
                    update_user_meta($current_user->ID,"obsidian_server_binding_id_".$server->server_name,$token_id);
                    //return to profile.php
                    wp_redirect(home_url()."/wp-admin/user-edit.php?user_id=".$current_user->ID);
                    exit;
                    break;
                case "login":
                    $users = get_users(array("meta_key"=>"obsidian_server_binding_id_".$server->server_name,"meta_value"=>$token_id));
                    //if obsidian_allow_unbind_login_with_email is yes,search user with specified emailaddress
                    if(count($users)>0) $current_user = $users[0];
                    if(($current_user==null)&&(get_option("obsidian_allow_unbind_login_with_email")=="yes")) $current_user = get_user_by("email",$token_email);
                    if(($current_user==null)&&($server->allow_create_user=="yes"))
                    {
                        $userdata = array(
                            "user_login" => str_replace(" ","",$server->server_name)."_".$token_username."_".rand(),
                            "user_pass"  => md5_file(rand().$token_id)."_obsidian",
                            "user_email" => $token_email,
                            "user_nicename" => $token_username,
                            "display_name" => $token_username
                        );
                        $user_id = wp_insert_user($userdata);
                        update_user_meta($user_id,"obsidian_server_binding_id_".$server->server_name,$token_id);
                        $current_user = get_user_by("id",$user_id);
                    }
                    wp_set_current_user($current_user);
                    wp_set_auth_cookie($current_user->ID);
                    do_action("wp_login", $current_user->user_login);
                    wp_redirect(home_url()."/wp-admin/profile.php");
                    exit;
                    break;
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
            //admin can modify other user setting
            $isadmin = in_array(wp_get_current_user()->roles,array("administrator"));
            if((!isadmin)&&(($_GET["user_id"])!=(wp_get_current_user()->ID)))
            {
                wp_redirect(home_url());
                exit;
            }
            $user_id = $_GET["user_id"];
            $action = $_GET["action"];
            $server_name = $_GET["server_name"];
            $server = server_option::get_server_by_name($server_name);
            if($server==null) wp_redirect(home_url());
            $client = new obsidian_client($server->client_id,$server->client_secret);
            switch ($action) {
                case "unbind":
                    $current_user = get_user_by("id",$user_id);
                    //if user hasn't login in
                    if($current_user->ID==0) wp_redirect(home_url());
                    delete_user_meta($current_user->ID,"obsidian_server_binding_id_".$server->server_name);
                    wp_redirect(home_url()."/wp-admin/user-edit.php?user_id=".$current_user->ID);
                    exit;
                    break;
                case "login":
                case "bind":
                    $_SESSION["obsidian_accessing_server"]=$server_name;
                    $_SESSION["obsidian_token_action"]=$action;
                    $_SESSION["obsidian_editing_user_id"]=$user_id;
                    switch ($server->grant_mode) {
                        case "code":
                            $auth_code = new authorization_code_authentication($client);
                            $auth_code->code_request_url = $server->code_mode_code_request_url;
                            $auth_code->code_redirect_url = home_url()."/obsidian-auth/token";
                            $url = $auth_code->generate_code_url(explode(" ",$server->scope_login));
                            wp_redirect($url);
                            exit;
                            break;
                        case "token":
                            $token_auth = new implict_authentication($client);
                            $token_auth->redirect_url = home_url()."/obsidian-auth/token";
                            $token_auth->request_url = $server->token_mode_request_url;
                            $url = $token_auth->generate_token_url(explode(" ",$server->scope_login));
                            wp_redirect($url);
                            exit;
                            break;
                        case "password":
                            $current_user = get_user_by("id",$user_id);
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
                            wp_redirect(home_url()."/wp-admin/user-edit.php?user_id=".$current_user->ID);
                            exit;
                        default:
                            # code...
                            break;
                    }
                    break;
            }
        }
    }
?>