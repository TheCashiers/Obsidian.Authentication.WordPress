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
require_once(ROOT_PATH."/options/client-administrator-option-page.php");
require_once(ROOT_PATH."/options/option-page.php");
require_once(ROOT_PATH."/authentication/client.php");

//enable session
if(!session_id()) session_start();

/*setup hook for plugin installation*/
register_activation_hook(__FILE__,"obsidian_hook_handler::register_activation_hook_handler");
register_deactivation_hook(__FILE__,"obsidian_hook_handler::register_deactivation_hook_handler");

/*setup hook for Resource Owner Password Credential Mode*/
add_filter("authenticate","obsidian_hook_handler::authenticate_handler",30,3);

/*setup hook into 'init' action to enable Authorization Code Mode and Implict Mode*/
add_filter("init","obsidian_hook_handler::init_handler");

/*enable option pages*/
obsidian_option_page::enable_all();
?>