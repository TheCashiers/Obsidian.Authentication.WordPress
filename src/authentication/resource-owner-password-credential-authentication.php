<?php
    require_once(ROOT_PATH."/helper/webrequest.php");
    class resource_owner_password_credential_authentication
    {
        private $request_uri;
        public $expire_in;
        public $scope;
        public $access_token;
        public $refresh_token;
        public $authrentication_token;

        public function __construct($request_url)
        {
            $this->request_uri = $request_url;
        }
        /*
        *Authenticate user info in an Obsidian-based server
        */
        public function authenticate($client_id,$client_secret,$username,$password,$scope)
        {
            //init postdata
            $post_data = array(
                "client_id"=>$client_id,
                "client_secret"=>$client_secret,
                "scope"=>join(" ",$scope),
                "username"=>$username,
                "password"=>$password,
                "grant_type"=>"password"
            );
            //post to Obsidian
            $result_array = web_request_helper::post_json($this->request_uri,$post_data);
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