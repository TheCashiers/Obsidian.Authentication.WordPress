<h2 class=\"title\"><?php _e("Obsidian Binding","obsidian-auth") ?></h2>
<?php
    $servers = json_decode(get_option("obsidian_servers"));
    $current_user = wp_get_current_user();
    foreach($servers as $server)
    {
        if($server->grant_mode!="no")
            if(get_user_meta($current_user->ID,"obsidian_server_binding_id_".$server->server_name)==null)
                printf(__("<p><a class=\"button button-primary button-large\" href=\"".home_url()."/obsidian-auth/auth?server_name=".$server->server_name."&action=bind"."\" style=\"margin-bottom:16px;float:none;\" >".__("Bind %s account","obsidian-auth")."</a></p>"),$server->server_name);
            else
                printf(__("<p><a class=\"button button-primary button-large\" href=\"".home_url()."/obsidian-auth/auth?server_name=".$server->server_name."&action=unbind"."\" style=\"margin-bottom:16px;float:none;\" >".__("Unbind %s account","obsidian-auth")."</a></p>"),$server->server_name);
    }         
?>