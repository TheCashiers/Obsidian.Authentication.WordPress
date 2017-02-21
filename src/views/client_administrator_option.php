<?php
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if(($_POST["disable_internal_auth"]=="yes")||($_POST["disable_internal_auth"]=="no"))
            update_option("obsidian_disable_internal_auth",$_POST["disable_internal_auth"]);
    }
?>
<h1><?php _e("Obsidian Options") ?></h1>
<form method="post">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><?php _e("Disable WordPress internal authentication if a Resource Owner Password Credential mode server is available","obsidian-auth"); ?></th>
                <td>
                    <fieldset>
                        <label><input name="disable_internal_auth" type="radio" value="yes" <?php if(get_option("obsidian_disable_internal_auth")=="yes") echo("checked=\"checked\""); ?> ><?php _e("Yes","obsidian-auth");?></label>
                        <label><input name="disable_internal_auth" type="radio" value="no" <?php if(get_option("obsidian_disable_internal_auth")=="no") echo("checked=\"checked\""); ?> ><?php _e("No","obsidian-auth");?></label>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>
</form>