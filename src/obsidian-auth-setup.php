<?php
    /*
    * Called when plugin is activated
    */
    function obsidian_auth_activation()
    {
        //add option into WordPress database
        add_option("obsidian_auth_grant_mode","password");
        add_option("obsidian_auth_password_mode_uri","");
        add_option("obsidian_auth_login_scope",array("ob.basic"));
        add_option("obsidian_auth_client_id","");
        add_option("obsidian_auth_client_secret","");
        add_option("obsidian_auth_password_mode_prevent_user","no");
    }

    /*
    * Called when plugin is deactivated
    */
    function obsidian_auth_deactivation()
    {
        //delete option from WordPress database
        delete_option("obsidian_auth_grant_mode");
        delete_option("obsidian_auth_password_mode_uri");
        delete_option("obsidian_auth_login_scope");
        delete_option("obsidian_auth_client_id");
        delete_option("obsidian_auth_client_secret");
        delete_option("obsidian_auth_password_mode_prevent_user");
    }
?>