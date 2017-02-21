<?php
    class view_controller
    {
        public static function client_administrator_list_servers()
        {
            include_once(ROOT_PATH."/views/client_administrator_list_servers.php");
        }
        public static function client_administrator_add_server()
        {
            include_once(ROOT_PATH."/views/client_administrator_add_server.php");
        }
        public static function client_administrator_option()
        {
            include_once(ROOT_PATH."/views/client_administrator_option.php");
        }
        public static function login_form()
        {
            include_once(ROOT_PATH."/views/login_form.php");
        }
        public static function edit_user_profile()
        {
            include_once(ROOT_PATH."/views/edit_user_profile.php");
        }
    }
?>