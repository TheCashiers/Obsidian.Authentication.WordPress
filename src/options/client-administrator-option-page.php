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
            add_options_page("Obsidian Authentication Plugin Client Option", "Obsidian Server Options", "administrator", "obsidian_client_menu", "view_controller::client_administration_option_page");
            add_action( "admin_init", array($this,"register_settings" ));
        }
        /*
        *  Register settings
        */
        public function register_settings()
        {
            register_setting("obsidian-client-setting-group","obsidian_auth_grant_mode");
            register_setting("obsidian-client-setting-group","obsidian_auth_client_id");
            register_setting("obsidian-client-setting-group","obsidian_auth_client_secret");
            register_setting("obsidian-client-setting-group","obsidian_auth_password_mode_uri");
            register_setting("obsidian-client-setting-group","obsidian_auth_password_mode_prevent_user");  
        }
    }
?>