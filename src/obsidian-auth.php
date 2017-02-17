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
require_once(ROOT_PATH."/authentication/resource-owner-password-credential-authentication.php");
require_once(ROOT_PATH."/helper/jwt.php");
require_once(ROOT_PATH."/obsidian-auth-auth.php");
require_once(ROOT_PATH."/obsidian-auth-setup.php");
require_once(ROOT_PATH."/views/obsidian-option-page.php");

$auth_mode = get_option("obsidian_auth_grant_mode");
//register plugin setup hook
register_activation_hook(__FILE__,"obsidian_auth_activation");
register_deactivation_hook(__FILE__,"obsidian_auth_deactivation");

if($auth_mode=="password")
    add_filter("authenticate","obsidian_client_authentication_handler",30,3);

//add admin option page
if(is_admin())
    add_action("admin_menu", "obsidian_client_create_page");
function obsidian_client_create_page()
{
    add_options_page("Obsidian Authentication Plugin Option", "Obsidian Server Options", "administrator", "obsidian_auth_menu", "obsidian_auth_administration_create_page");
	add_action( "admin_init", "register_obsidian_client_settings" );
}
function register_obsidian_client_settings()
{
    register_setting("obsidian-client-setting-group","obsidian_auth_grant_mode");
    register_setting("obsidian-client-setting-group","obsidian_auth_client_id");
    register_setting("obsidian-client-setting-group","obsidian_auth_client_secret");
    register_setting("obsidian-client-setting-group","obsidian_auth_password_mode_uri");
    register_setting("obsidian-client-setting-group","obsidian_auth_password_mode_prevent_user");  
}
?>