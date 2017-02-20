<?php
    require_once(ROOT_PATH."/views/view-controller.php");
    /*
    * Create client-side administration option page
    */
    class client_administrator_option_page
    {
        /*
        * Enable Option Page
        */
        public function enable_page()
        {
            add_action("admin_menu", array($this,"create_page"));
        }
        /*
        * Create option page
        */
        public function create_page()
        {
            add_menu_page( __("Authentication Servers List","obsidian-auth"), __("Obsidian Options","obsidian-auth"), "administrator", "obsidian_list_servers","view_controller::client_administrator_list_servers");
            add_submenu_page( "obsidian_list_servers", __("Add new server","obsidian-auth"), __("Add new server","obsidian-auth") , "administrator", "obsidian_add_server","view_controller::client_administrator_add_server");
        }
    }
?>