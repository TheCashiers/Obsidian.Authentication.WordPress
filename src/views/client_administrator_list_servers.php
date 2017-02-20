<?php require_once(ROOT_PATH."/options/server-option.php");?>
<div class="wrap">
    <h1>
        <?php _e("Authentication Servers List","obsidian-auth") ?>
        <a class="add-new-h2" href="<?php echo admin_url("admin.php?page=obsidian_add_server"); ?>"><?php _e("Add new server", "obsidian-auth" ) ;?></a>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col"><?php _e("Server Name","obsidian-auth"); ?></th>
                    <td scope="col"><?php _e("Grant Mode","obsidian-auth"); ?></td>
                    <td scope="col"><?php _e("Client ID","obsidian-auth"); ?></td>
                    <td scope="col"><?php _e("Client Secret","obsidian-auth"); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $servers = json_decode(get_option("obsidian_servers"));
                    if(($servers == null) || (count($servers)==0))
                    {
                        echo("<tr><th span=\"4\">".__("No Server","obsidian-auth")."</th></tr>");
                    }
                    else
                        foreach($servers as $server)
                        {
                            echo("<tr>");
                            ?>
                            <th>
                                <?php echo($server->server_name); ?>
                                <div class="row-action">
                                    <span class="edit">
                                        <a href="<?php echo(admin_url()."admin.php?page=obsidian_add_server&server_name=".$server->server_name); ?>"><?php _e("Edit","obsidian-auth"); ?></a> | 
                                    </span>
                                    <span class="delete">
                                        <a href="<?php echo(admin_url()."admin.php?page=obsidian_add_server&server_name=".$server->server_name."&action=delete"); ?>"><?php _e("Delete","obsidian-auth"); ?></a>
                                    </span>
                                </div>
                            </th>
                            <?php
                            $db_grant_mode = array(
                                    "no"=>__("Disabled","obsidian-auth"),
                                    "password"=>__("Resource Owner Password Credentials Grant","obsidian-auth"),
                                    "token"=>__("Implicit Grant","obsidian-auth"),
                                    "code"=>__("Authorization Code Grant","obsidian-auth")
                                    );
                            echo("<td>".$db_grant_mode[$server->grant_mode]."</td>");
                            echo("<td>".$server->client_id."</td>");
                            echo("<td>".$server->client_secret."</td>");
                            echo("</tr>");
                        }
                ?>
            </tbody>
        </table>
    </h1>
</div>