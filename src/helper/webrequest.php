<?php
    class web_request_helper
    {
        public static function post_json($uri,$post_data)
        {
            $ch = curl_init();
            $post_data_json = json_encode($post_data);
            curl_setopt($ch,CURLOPT_URL,$uri);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Content-Length: " . strlen($post_data_json))
            );
            $output = curl_exec($ch);
            return json_decode($output,true);
        }
    }

?>
