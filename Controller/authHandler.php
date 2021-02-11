<?php

require_once ('../libs/php-jwt-master/src/BeforeValidException.php');
require_once ('../libs/php-jwt-master/src/ExpiredException.php');
require_once ('../libs/php-jwt-master/src/SignatureInvalidException.php');
require_once ('../libs/php-jwt-master/src/JWT.php');
use \Firebase\JWT\JWT;

date_default_timezone_set('Asia/Tehran');
CONST keys="92?VH2WMrx";

CONST refreshKey="8za37yT@xt7#Mc01";

class authHandler
{
    private $requestMethod;
    private $expectedType;
    private $mode;
    function __construct($requestMethod,$expectedType=null,$mode=null)
    {
        $this->requestMethod=$requestMethod;
        $this->expectedType=$expectedType;
        $this->mode=$mode;
    }

    public function requestProcess(){
        $response=null;
        if($this->requestMethod=="GET" && $this->mode==null){
            $response=$this->validateToken();
        }
        if($this->requestMethod=="GET" && $this->mode!=null){
            $response=$this->refreshAccessToken();
        }
        header($response["header"]);
        echo json_encode($response["body"]);
    }

    public static function generateJwtAccessTokenForUser(User $user){
        $issued_at = time();
        $expiration_time = $issued_at + (900);
        $payload=array(
            "start"=>$issued_at,
            "expire"=>$expiration_time,
            "data"=>array(
                "user_id"=>$user->getUserId(),
            )
        );
        return JWT::encode($payload,keys);
    }

    public static function generateJwtRefreshTokenForUser(User $user){
        $issued_at = time();
        $expiration_time = $issued_at + (604800);
        $payload=array(
            "start"=>$issued_at,
            "expire"=>$expiration_time,
            "data"=>array(
                "user_id"=>$user->getUserId(),
            )
        );
        $id=JWT::encode($payload,refreshKey);
        $db=new authDB();
        $sql="INSERT INTO `refreshtokens` (`refresh_id`,`expires_at`) VALUES ($id,$expiration_time)";
        return $id;
    }

//    private function validateToken(){
//        if(isset($_SERVER['Authorization'])==false){
//            return $this->createMessageToClient(403,"Access denied!","forbidden!");
//        }
//        try {
//            $token=$_SERVER['Authorization'];
//            $decoded = JWT::decode($token, keys, array('HS256'));
//            if((time()-$decoded->expire)>900){
//                unset($_COOKIE["token"]);
//                return $this->createMessageToClient(403,"Access denied!","forbidden!");
//            }
//            $result=User::getUserById($decoded->data->user_id);
//            if($result["type"]!=$this->expectedType){
//                return $this->createMessageToClient(403,"Access denied!" ,"forbidden");
//            }
//            return $this->createMessageToClient(200,"ok","access granted!");
//        }
//        catch (Exception $exception){
//            //unset($_COOKIE["token"]);
//            return $this->createMessageToClient(403,"Access denied!","forbidden!");
//        }
//    }

    private function validateToken(){
        $token=$this->getBearerToken();
        if(is_null($token)){
            return $this->createMessageToClient("403","Access denied!","forbidden");
        }
        try {
            $decoded = JWT::decode($token, keys, array('HS256'));
            if((time()-$decoded->expire)>900){
               return $this->createMessageToClient(403,"Access denied!","token Expired!");
            }
            $result=User::getUserById($decoded->data->user_id);
            if($result["type"]!=$this->expectedType){
               return $this->createMessageToClient(403,"Access denied!" ,"forbidden");
            }
            return $this->createMessageToClient(200,"ok","access granted!");
        }catch (Exception $e){
            return $this->createMessageToClient(403,"Access denied!","forbidden!");
        }
    }

    private function refreshAccessToken(){
        $token=$this->getBearerToken();
        if(is_null($token)){
            return $this->createMessageToClient("404","Not Found!","");
        }
        try {
            $decoded = JWT::decode($token, keys, array('HS256'));
            $refreshToken=$_COOKIE["refreshToken"];
            $db=new authDB();
            $sql="SELECT * FROM `refreshtokens` WHERE `refresh_id`=$refreshToken";
            $result=$db->getConnection()->query($sql);
            if($result->num_rows==0){
                return $this->createMessageToClient("404","Not Found!","");
            }
            $row=$result->fetch_assoc();
            if(time()>$row["expires_at"]){
                $sql="DELETE FROM `refreshtokens` WHERE `refresh_id`=$refreshToken";
                $db->getConnection()->query($sql);
                return $this->createMessageToClient("403","Access denied!","forbidden!");
            }
            $issued_at = time();
            $expiration_time = $issued_at + (900);
            $payload=array(
                "start"=>$issued_at,
                "expire"=>$expiration_time,
                "data"=>array(
                    "user_id"=>$decoded->data->user_id,
                )
            );
            return $this->createMessageToClient(200,"ok",JWT::encode($payload,keys));
        }catch (Exception $e){
            return $this->createMessageToClient("404","Not Found!","");
        }


    }


   private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
   private function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }


    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        $response["body"]=$body;
        return $response;
    }


}