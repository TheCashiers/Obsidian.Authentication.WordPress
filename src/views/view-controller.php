<?php
    class view_controller
    {
        public static function client_administrator_list_servers()
        {
            include_once(ROOT_PATH."/views/client-administrator-list-servers.php");
        }
        public static function client_administrator_add_server()
        {
            include_once(ROOT_PATH."/views/client-administrator-add-server.php");
        }
        public static function client_administrator_option()
        {
            include_once(ROOT_PATH."/views/client-administrator-option.php");
        }
        public static function login_form()
        {
            include_once(ROOT_PATH."/views/login-form.php");
        }
        public static function edit_user_profile()
        {
            include_once(ROOT_PATH."/views/edit-user-profile.php");
        }
    }
?>