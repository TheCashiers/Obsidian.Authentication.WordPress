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
define("ROOT_PATH",__DIR__);
require_once(ROOT_PATH."/Authentication/token-resource-owner-credential.php");
require_once(ROOT_PATH."/Authentication/jwt.php");
require_once(ROOT_PATH."/obsidian-auth-config.php");



if(ObsidianAuthConfig::$auth_mode=="password")
    add_filter("authenticate","obsidian_client_auth_signon_passwordmode_handler",30,3);

/*
*Password Mode Handler
*/
function obsidian_client_auth_signon_passwordmode_handler($user,$username,$password)
{
    if($username==""&&$password=="") return null;
    $password_cred = new TokenResourceOwnerCredential(ObsidianAuthConfig::$password_uri);
    //authenticate in an Obsidian-based Server
    $password_cred->authenticate(ObsidianAuthConfig::$client_id,ObsidianAuthConfig::$client_secret,$username,$password,ObsidianAuthConfig::$login_scope);
    if($password_cred->access_token=="") return null;
    $jwt = JsonWebToken::decode_jwt($password_cred->access_token);
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
?>