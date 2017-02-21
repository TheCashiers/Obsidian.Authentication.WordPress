<?php          
    $servers = json_decode(get_option("obsidian_servers"));
    foreach($servers as $server)
    {
        if(($server->grant_mode=="token")||($server->grant_mode=="code"))
        {
        ?>
            <p><a class="button button-primary button-large" href="<?php echo(home_url()."/obsidian-auth/auth?server_name=".$server->server_name."&action=login");?>" style="margin-bottom:16px;float:none;"><?php printf(__("Login with %s","obsidian-auth"),$server->server_name); ?></a></p>
        <?php
        }
    }
?>