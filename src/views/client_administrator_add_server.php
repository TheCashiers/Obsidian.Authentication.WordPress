<?php require_once(ROOT_PATH."/options/server-option.php");?>
<?php
    if(($_SERVER["REQUEST_METHOD"] == "GET") && ($_GET["action"] == "delete")) //delete server
    {
        $servers = json_decode(get_option("obsidian_servers"));
        if($servers!=null)
        {
            $edit_index = count($servers);
            for($i=0;$i<count($servers);$i++)
            {
                if($servers[$i]->server_name == $_GET["server_name"])
                {              
                    $edit_index = $i;
                    break;
                }
            }
            if($edit_index!=count($servers))
            {
                $edit_server = $servers[$edit_index];
                array_splice($servers,$edit_index,1);                
            }
            update_option("obsidian_servers",json_encode($servers));
            echo("<script>window.location=\"".admin_url()."admin.php?page=obsidian_list_servers"."\"</script>");
        }
    }
    if($_SERVER["REQUEST_METHOD"] == "POST") //update server
    {
        $servers = json_decode(get_option("obsidian_servers"));
        $edit_server = new server_option();      
        if($servers!=null)
        {
            $edit_index = count($servers);
            for($i=0;$i<count($servers);$i++)
            {
                if($servers[$i]->server_name == $_GET["server_name"])
                {              
                    $edit_index = $i;
                    break;
                }
            }
            if($edit_index!=count($servers))
            {
                $edit_server = $servers[$edit_index];
                array_splice($servers,$edit_index,1);                
            }                   
        }
        if($edit_server->server_name==null)$edit_server->server_name = $_POST["server_name"];
        $edit_server->grant_mode = $_POST["grant_mode"];
        $edit_server->client_id = $_POST["client_id"];
        $edit_server->client_secret = $_POST["client_secret"];
        $edit_server->scope_login = $_POST["scope_login"];
        $edit_server->allow_create_user = $_POST["allow_create_user"];
        $edit_server->allow_login_unbind_user_pasword_mode = $_POST["allow_login_unbind_user_pasword_mode"];
        $edit_server->password_mode_request_url = $_POST["password_mode_request_url"];
        $edit_server->password_mode_intercept = $_POST["password_mode_intercept"];
        $edit_server->token_mode_request_url = $_POST["token_mode_request_url"];
        $edit_server->code_mode_code_request_url = $_POST["code_mode_code_request_url"];
        $edit_server->code_mode_token_request_url = $_POST["code_mode_token_request_url"];
        if(!$servers)
            $servers = array($edit_server);
        else
            array_push($servers,$edit_server);
        update_option("obsidian_servers",json_encode($servers));
    }
?>
<?php $server = server_option::get_server_by_name($_GET["server_name"]);?>
<div class="wrap">
    <h1>
        <?php 
        if($server!=null)
            _e("Edit authentication server","obsidian-auth");
        else
            _e("Add authentication server","obsidian-auth");
        ?>
        <a class="add-new-h2" href="<?php echo admin_url("admin.php?page=obsidian_add_server"); ?>"><?php _e("Add new server", "obsidian-auth" );?></a>
    </h1>
    <form method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="server_name"><?php _e("Server Name","obsidian-auth"); ?></label></th>
                    <td><input name="server_name" id="server_name" type="text" class="regular-text" <?php if($server!=null) echo("value=\"".$server->server_name."\""); ?> <?php if($server!=null) echo("disabled=\"disabled\""); ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="grant_mode"><?php _e("Grant Mode","obsidian-auth"); ?></label></th>
                    <td>
                        <select name="grant_mode" id="grant_mode">
                            <?php
                                $db_grant_mode = array(
                                        "no"=>__("Disabled","obsidian-auth"),
                                        "password"=>__("Resource Owner Password Credentials Grant","obsidian-auth"),
                                        "token"=>__("Implicit Grant","obsidian-auth"),
                                        "code"=>__("Authorization Code Grant","obsidian-auth")
                                    );
                                $selected_grant_mode = $server!=null?$server->grant_mode:"code";
                                foreach($db_grant_mode as $key=>$value){
                                    if($key==$selected_grant_mode)
                                        echo("<option selected=\"selected\" value=\"".$key."\">".$value."</option>");
                                    else
                                        echo("<option value=\"".$key."\">".$value."</option>");
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="client_id"><?php _e("Client ID","obsidian-auth"); ?></label></th>
                    <td><input name="client_id" id="client_id" type="text" class="regular-text" value="<?php if($server!=null) echo($server->client_id); ?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><label for="client_secret"><?php _e("Client Secret","obsidian-auth"); ?></label></th>
                    <td><input name="client_secret" id="client_secret" type="text" class="regular-text" value="<?php if($server!=null) echo($server->client_secret); ?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><label for="scope_login"><?php _e("Login Scope","obsidian-auth"); ?></label></th>
                    <td><input name="scope_login" id="scope_login" type="text" class="regular-text" value="<?php if($server!=null) echo($server->scope_login); ?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e("Allow not existing user to Login","obsidian-auth"); ?></th>
                    <td>
                        <fieldset>
                            <label><input name="allow_create_user" type="radio" value="yes" <?php if($server!=null&&$server->allow_create_user=="yes") echo("checked=\"checked\""); ?> ><?php _e("Yes","obsidian-auth");?></label>
                            <label><input name="allow_create_user" type="radio" value="no" <?php if($server!=null&&$server->allow_create_user=="no") echo("checked=\"checked\""); ?> ><?php _e("No","obsidian-auth");?></label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <h2 class="title"><?php _e("Resource Owner Password Credential Mode","obsidian-auth"); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e("Allow unbind user to login","obsidian-auth"); ?></th>
                    <td>
                        <fieldset>
                            <label><input name="allow_login_unbind_user_pasword_mode" type="radio" value="yes" <?php if($server!=null&&$server->allow_login_unbind_user_pasword_mode=="yes") echo("checked=\"checked\""); ?> ><?php _e("Yes","obsidian-auth");?></label>
                            <label><input name="allow_login_unbind_user_pasword_mode" type="radio" value="no" <?php if($server!=null&&$server->allow_login_unbind_user_pasword_mode=="no") echo("checked=\"checked\""); ?> ><?php _e("No","obsidian-auth");?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e("Intercept other login authentication","obsidian-auth"); ?></th>
                    <td>
                        <fieldset>
                            <label><input name="password_mode_intercept" type="radio" value="yes" <?php if($server!=null&&$server->password_mode_intercept=="yes") echo("checked=\"checked\""); ?> ><?php _e("Yes","obsidian-auth");?></label>
                            <label><input name="password_mode_intercept" type="radio" value="no" <?php if($server!=null&&$server->password_mode_intercept=="no") echo("checked=\"checked\""); ?> ><?php _e("No","obsidian-auth");?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="password_mode_request_url"><?php _e("Request URL","obsidian-auth"); ?></label></th>
                    <td><input name="password_mode_request_url" id="password_mode_request_url" type="text" class="regular-text code" value="<?php if($server!=null) echo($server->password_mode_request_url); ?>"/></td>
                </tr>
            </tbody>
        </table>
        <h2 class="title"><?php _e("Implict Mode","obsidian-auth"); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="token_mode_request_url"><?php _e("Request URL","obsidian-auth"); ?></label></th>
                    <td><input name="token_mode_request_url" id="token_mode_request_url" type="text" class="regular-text code" value="<?php if($server!=null) echo($server->token_mode_request_url); ?>"/></td>
                </tr>
            </tbody>
        </table>
        <h2 class="title"><?php _e("Authorization Code Mode","obsidian-auth"); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="code_mode_code_request_url"><?php _e("Acquire Code Request URL","obsidian-auth"); ?></label></th>
                    <td><input name="code_mode_code_request_url" id="code_mode_code_request_url" type="text" class="regular-text code" value="<?php if($server!=null) echo($server->code_mode_code_request_url); ?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><label for="code_mode_token_request_url"><?php _e("Acquire Token Request URL","obsidian-auth"); ?></label></th>
                    <td><input name="code_mode_token_request_url" id="code_mode_token_request_url" type="text" class="regular-text code" value="<?php if($server!=null) echo($server->code_mode_token_request_url); ?>"/></td>
                </tr>                
            </tbody>            
        </table>
        <?php submit_button(); ?>
    </form>
</div>