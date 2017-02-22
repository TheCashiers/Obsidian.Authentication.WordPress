<?php
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $bool_options = array("disable_internal_auth","allow_unbind_login_with_email");
        foreach($bool_options as $option)
        if(($_POST[$option]=="yes")||($_POST[$option]=="no"))
            update_option("obsidian_".$option,$_POST[$option]);
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
            <tr>
                <th scope="row"><?php _e("Allow unbind user login with same email","obsidian-auth"); ?></th>
                <td>
                    <fieldset>
                        <label><input name="allow_unbind_login_with_email" type="radio" value="yes" <?php if(get_option("obsidian_allow_unbind_login_with_email")=="yes") echo("checked=\"checked\""); ?> ><?php _e("Yes","obsidian-auth");?></label>
                        <label><input name="allow_unbind_login_with_email" type="radio" value="no" <?php if(get_option("obsidian_allow_unbind_login_with_email")=="no") echo("checked=\"checked\""); ?> ><?php _e("No","obsidian-auth");?></label>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>
</form>