<h2 class=\"title\"><?php _e("Obsidian Binding","obsidian-auth") ?></h2>
<script>
    function bind_password_mode_user(server_name,home,userid)
    {
        var password = document.getElementById("obsidian_auth_server_password_"+server_name).value;
        window.location = home+"/obsidian-auth/auth?server_name="+server_name+"&action=bind&password="+password+"&user_id="+userid;
    }

</script>
<table class="form-table">
    <tbody>
        <?php
            $servers = json_decode(get_option("obsidian_servers"));
            $current_user_id = isset($_GET["user_id"])?$_GET["user_id"]:wp_get_current_user()->ID;
            foreach($servers as $server)
            {
                echo("<tr>");
                if($server->grant_mode!="no")
                {
                    if(get_user_meta($current_user_id,"obsidian_server_binding_id_".$server->server_name)==null)
                    {
                        if($server->grant_mode=="password")
                            printf("<th><a onclick=\"bind_password_mode_user('".$server->server_name."','".home_url()."','".$current_user_id."')\" class=\"button button-primary button-large\" style=\"text-align:center;\" >".__("Bind %s account","obsidian-auth")."</a></th>",$server->display_name);
                        else
                            printf("<p><a class=\"button button-primary button-large\" href=\"".home_url()."/obsidian-auth/auth?server_name=".$server->server_name."&action=bind&user_id=".$current_user_id."\" style=\"margin-bottom:16px;float:none;\" >".__("Bind %s account","obsidian-auth")."</a></p>",$server->display_name);
                    }
                    else
                        printf("<th><a class=\"button button-primary button-large\" href=\"".home_url()."/obsidian-auth/auth?server_name=".$server->server_name."&action=unbind&user_id=".$current_user_id."\" style=\"text-align:center;\" >" . __("Unbind %s account","obsidian-auth") . "</a></th>" , $server->display_name);
                }
                if($server->grant_mode=="password")
                {
                    //echo("<td><label for=\"obsidian_auth_server_password_".$server->server_name."\">".__("Password","obsidian-auth")."</label></td>");
                    printf("<td><input name=\"obsidian_auth_server_password_".$server->server_name."\" id=\"obsidian_auth_server_password_".$server->server_name."\" type=\"text\" class=\"regular-text code\"/></td>");
                }
            }         
        ?>
    </tbody>
</table>