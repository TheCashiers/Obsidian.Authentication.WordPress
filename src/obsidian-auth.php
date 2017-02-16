<?php
/*
Plugin Name: Obsidian Authentication Plugin
Plugin URI:  https://github.com/ZA-PT/Obsidian.Authentication.WordPress
Description: A WordPress plugin provided a convenient to access Obsidian-based Authentication Server.
Version:     0.0.1
Author:      ZA-PT
Author URI:  http://www.za-pt.org
License:     Apache License 2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0.html
*/
require("./obsidian-auth-config.php");
require("./Authentication/token-resource-owner-credential.php");
require("./Authentication/jwt.php");
if($obsidian_auth_config_auth_mode=="password")
    add_filter("authenticate","obsidian_client_auth_signon_passwordmode_handler",30,3);

/*
*Password Mode Handler
*/
function obsidian_client_auth_signon_passwordmode_handler($user,$username,$password)
{
    if($username==""&&$password=="") return null;
    $password_cred = new TokenResourceOwnerCredential($obsidian_auth_config_password_uri);
    //authenticate in an Obsidian-based Server
    $password_cred->authenticate($obsidian_auth_config_client_id,$obsidian_auth_config_client_secret,$username,$password,$obsidian_auth_config_login_scope);
    if($password_cred->access_token=="") return null;
    $jwt = JsonWebToken::decode_jwt($password_cred->access_token);
    //get userinfo
    $token_username = array_search("http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name",$jwt->custom_claims);
    $token_email = array_search("http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress",$jwt->custom_claims);
    //login
    if(strcasecmp($token_username,$username)==false) return null;
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
?>