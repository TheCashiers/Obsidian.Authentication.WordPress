<div class="wrap">
    <h1>Obsidian Authentication Plugin Options</h1>
    <form method="post">
        <?php
            require_once(ROOT_PATH."/options/server-option.php");
            if($_SERVER["REQUEST_METHOD"] == "POST")
            {
                $servers = json_decode(get_option("obsidian_servers"));
                $edit_server = new server_option();
                if($servers!=null)
                {
                    $edit_index = count($servers);
                    for($i=0;$i<count($servers);$i++)
                    {
                        if($servers[$i]->server_name == $_POST["obsidian_server_name"])
                        {              
                            $edit_index = $i;
                            break;
                        }
                    }

                    if($edit_index!=count($servers))
                    {
                        $edit_server = $servers[$edit_index];
                        array_splice($servers,$i,1);                
                    }                   
                }
                $edit_server->scope_login = "ob.basic";
                $edit_server->server_name = $_POST["obsidian_server_name"];
                $edit_server->grant_mode = $_POST["obsidian_auth_grant_mode"];
                $edit_server->client_id = $_POST["obsidian_auth_client_id"];
                $edit_server->client_secret = $_POST["obsidian_auth_client_secret"];
                $edit_server->password_mode_request_url = $_POST["obsidian_auth_password_mode_uri"];
                $edit_server->password_mode_intercept = $_POST["obsidian_auth_password_mode_prevent_user"];
                if(!$servers)
                    $servers = array($edit_server);
                else
                    array_push($servers,$edit_server);
                update_option("obsidian_servers",json_encode($servers));
            }

        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="obsidian_server_name">Server Name</label></th>
                    <td><input name="obsidian_server_name" type="text" id="obsidian_server_name"/></td>
                </tr>
                <tr>
                    <th><label for="obsidian_auth_grant_mode">Grant Mode</label></th>
                    <td>
                        <select name="obsidian_auth_grant_mode" id="obsidian_auth_grant_mode">
                            <?php
                                $db_grant_mode = array(
                                        "password"=>"Resource Owner Password Credentials Grant",
                                        "token"=>"Implicit Grant",
                                        "code"=>"Authorization Code Grant"
                                    );
                                foreach($db_grant_mode as $key=>$value){
                                    if($key==get_option("obsidian_auth_grant_mode"))
                                        echo("<option selected=\"selected\" value=\"".$key."\">".$value."</option>");
                                    else
                                        echo("<option value=\"".$key."\">".$value."</option>");
                                }
                            ?>
                        </select>
                    </td>                            
                </tr>
                <tr>
                    <th><label for="obsidian_auth_client_id">Client Id</label></th>
                    <td>
                    <?php 
                        echo("<input name=\"obsidian_auth_client_id\" type=\"text\" id=\"obsidian_auth_client_id\" value=\"".get_option("obsidian_auth_client_id")."\" class=\"regular-text\"/>");
                    ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="obsidian_auth_client_secret">Client Secret</label></th>
                    <td>
                    <?php 
                        echo("<input name=\"obsidian_auth_client_secret\" type=\"text\" id=\"obsidian_auth_client_secret\" value=\"".get_option("obsidian_auth_client_secret")."\" class=\"regular-text\"/>");
                    ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="obsidian_auth_password_mode_uri">Resource Owner Password Credentials Grant Request URL</th>
                    <td>
                    <?php
                        echo("<input name=\"obsidian_auth_password_mode_uri\" type=\"url\" id=\"obsidian_auth_password_mode_uri\" value=\"".get_option("obsidian_auth_password_mode_uri")."\" class=\"regular-text code\"/>");              
                    ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="obsidian_auth_password_mode_prevent_user">Intercept WordPress Login Process in Password Grant</label></th>
                    <td>
                        <label><input type="radio" name="obsidian_auth_password_mode_prevent_user" value="yes" <?php if(get_option("obsidian_auth_password_mode_prevent_user")=="yes") echo("checked=\"checked\""); ?>> <span>Yes</span></label>
                        <br/>
                        <label><input type="radio" name="obsidian_auth_password_mode_prevent_user" value="no"  <?php if(get_option("obsidian_auth_password_mode_prevent_user")=="no") echo("checked=\"checked\""); ?>> <span>No</span></label>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
</div>