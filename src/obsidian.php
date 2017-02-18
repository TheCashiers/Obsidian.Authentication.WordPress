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
require_once(ROOT_PATH."/hook-handler.php");
require_once(ROOT_PATH."/views/view-controller.php");

$auth_mode = get_option("obsidian_auth_grant_mode");
/*setup hook for plugin installation*/
register_activation_hook(__FILE__,"obsidian_hook_handler::register_activation_hook_handler");
register_deactivation_hook(__FILE__,"obsidian_hook_handler::register_deactivation_hook_handler");

/*setup hook for Resource Owner Password Credential Mode*/
if($auth_mode=="password")
    add_filter("authenticate","obsidian_hook_handler::authenticate_handler",30,3);

/*setup hook for client option page*/
if(is_admin())
    add_action("admin_menu", "obsidian_client_create_page");
function obsidian_client_create_page()
{
    add_options_page("Obsidian Authentication Plugin Option", "Obsidian Server Options", "administrator", "obsidian_client_menu", "view_controller::obsidian_client_administration_create_page");
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