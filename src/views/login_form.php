<?php          
    $servers = json_decode(get_option("obsidian_servers"));
    //for password mode,create combobox
    $o_html = "<p><label for=\"obsidian_auth_server\">".__("Authentication Server","obsidian-auth")."<br/><select class=\"input\" name=\"obsidian_auth_server\" id=\"obsidian_auth_server\">";
    $op_html = "";
    $owp_html = "";
    foreach($servers as $server)
        if($server->grant_mode=="password")
            $op_html.="<option value=\"".$server->server_name."\">".$server->display_name."</option>";
    if(get_option("obsidian_disable_internal_auth")=="no") $owp_html = "<option value=\"wp_internal\">".__("WordPress Internal Authentication","obsidian-auth")."</option>";
    if($op_html!="") echo($o_html.$owp_html.$op_html."</select></label></p>");
    //for token or code mode,show buttons bottom
    foreach($servers as $server)
    {
        if(($server->grant_mode=="token")||($server->grant_mode=="code"))
        {
        ?>
            <p><a class="button button-primary button-large" href="<?php echo(home_url()."/obsidian-auth/auth?server_name=".$server->server_name."&action=login");?>" style="margin-bottom:16px;float:none;width:100%;text-align:center;"><?php printf(__("Login with %s","obsidian-auth"),$server->display_name); ?></a></p>
        <?php
        }
    }
?>