<?php
    /*Managing all option pages*/
    class obsidian_option_page
    {
        public static function enable_all()
        {
            self::enable_client_administrator_option_page();
        }
        public static function enable_client_administrator_option_page()
        {
            if(is_admin())
            {
                $c_a_o_p = new client_administrator_option_page();
                $c_a_o_p->enable_page();
            }
        }
    }
?>