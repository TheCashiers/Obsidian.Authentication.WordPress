<?php
    class JsonWebToken
    {
        public $iss;
        public $sub;
        public $aud;
        public $exp;
        public $iat;
        public $custom_claims;
        public $alg;
        public $sign_string;
        /*
        *Decode an encoded JWT string
        *
        *@access public
        *@param mixed $data An encoded JWT string
        *@return a decoded jwt
        */
        public static function decode_jwt($data)
        {
            $rtn_jwt = new JsonWebToken();
            if(gettype($data) != "string") return $rtn_jwt;        
            $data_array = explode(".",$data);
            //invalid jwt string
            if(count($data_array) != 3) return $rtn_jwt;
            //decode header
            $jwt_header = json_decode(base64_decode($data_array[0]),true);
            foreach ($jwt_header as $key => $value)
                if($key == "alg") $rtn_jwt->alg = $value;                      
            //decode payload
            $rtn_jwt->custom_claims = array();
            $jwt_payload = json_decode(base64_decode($data_array[1]),true);
            foreach ($jwt_payload as $key => $value)
                switch ($key) {
                    case "iss":$rtn_jwt->iss = $value; break;
                    case "sub":$rtn_jwt->sub = $value; break;
                    case "aud":$rtn_jwt->aud = $value; break;
                    case "exp":
                        $rtn_jwt->exp = date_create();
                        date_timestamp_set($rtn_jwt->exp,$value);
                        break;
                    case "iat":
                        $rtn_jwt->iat = date_create();
                        date_timestamp_set($rtn_jwt->iat,$value);
                        break;                    
                    default:array_push($rtn_jwt->custom_claims,$key,$value);break;
                }
            //set signature
            $rtn_jwt->sign_string = $data_array[2];
            return $rtn_jwt;
        }
    }
?>