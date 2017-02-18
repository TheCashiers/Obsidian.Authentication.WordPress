<di class="wrap">
    <h1>Obsidian Authentication Plugin Options</h1>
    <form method="post" action="options.php">
        <?php settings_fields( "obsidian-client-setting-group" ); ?>
        <?php do_settings_sections( "obsidian-client-setting-group" ); ?>
        <table class="form-table">
            <tbody>
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