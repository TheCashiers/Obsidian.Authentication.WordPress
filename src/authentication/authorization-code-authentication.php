<?php
	require_once(ROOT_PATH."/helper/webrequest.php");
    class authorization_code_authentication
    {
        public $code_request_url;
        public $token_request_url;
        public $client;
        public $scope;
        public $code_redirect_url;
        public $expire_in;
        public $access_token;
        public $refresh_token;
        public $authrentication_token;
        public function __construct($client)
        {
            $this->client = $client;
        }
        /* generate query url in an Obsidian-based server to get authorization code*/
        public function generate_code_url($scope)
        {
            return $this->code_request_url."?response_type=code&client_id=".$this->client->client_id."&redirect_uri=".urlencode($this->code_redirect_url)."&scope=".join(" ",$scope);
        }
        /* get access token from an Obsidian-based server*/
        public function get_access_token($authorization_code)
        {
            //init postdata
            $post_data = array(
                "client_id"=>$this->client->client_id,
                "client_secret"=>$this->client->client_secret,
                "code"=>$authorization_code,
                "redirect_uri"=>$this->code_redirect_url,
                "grant_type"=>"authorization_code"
            );
            //post to Obsidian
            $result_array = web_request_helper::post_json($this->token_request_url,$post_data);
            if($result_array==null) return false;
            //parse result          
            foreach ($result_array as $key => $value)
                switch ($key) {
                    case "expire_in":$this->expire_in = date_create();date_timestamp_set($this->expire_in,$value);break;
                    case "scope":$this->scope = explode(" ",$value);break;
                    case "access_token":$this->access_token = $value;break;
                    case "refresh_token":$this->refresh_token = $value;break;
                    case "authrentication_token":$this->authrentication_token = $value;break;
                    default:break;
                }

            return true;
        }
    }
?>