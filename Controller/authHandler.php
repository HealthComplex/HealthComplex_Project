<?php
require_once ("authDB.php");
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
        if(is_null($this->mode) && $this->requestMethod=="GET"){
            $response=$this->checkCorrectUserType();
        }
        else if(is_null($this->mode)==false && $this->requestMethod=="GET"){
            $response=$this->refreshAccessToken();
        }
        header($response["header"]);
        echo json_encode($response["body"]);
    }

    public static function generateJwtAccessTokenForUser(User $user){
        $issued_at = time();
        $expiration_time = $issued_at + (10);
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
        $expiration_time = $issued_at + (60);
        $payload=array(
            "start"=>$issued_at,
            "expire"=>$expiration_time,
            "data"=>array(
                "user_id"=>$user->getUserId(),
            )
        );
        $id=JWT::encode($payload,refreshKey);
        $db=new authDB();
        $sql="INSERT INTO `refreshtokens` (`refresh_id`,`expires_at`) VALUES ('$id','$expiration_time')";
        $db->getConnection()->query($sql);
        return $id;
    }


    public function checkCorrectUserType(){
        $decoded=$this->validateToken();
        if($decoded=="invalid token!" || $decoded=="expired token!") return $this->createMessageToClient("403","access denied!",$decoded);
        $type=User::getUserById($decoded->data->user_id)["type"];
        if($type!=$this->expectedType){
            return $this->createMessageToClient("403","access denied!","access denied!");
        }
        return $this->createMessageToClient("200","ok","ok");
    }


    public function validateToken(){
        $token=$this->getBearerToken();
        //echo $token;
//        $db=new authDB();
//        $sql="SELECT * FROM `black_list` WHERE `access_id`='$token'";
//        if($db->getConnection()->query($sql)->num_rows!=0) return "invalid token!";
        if(is_null($token)){
            return "invalid token!";
        }
        try {
            $decoded = JWT::decode($token, keys, array('HS256'));
            if((time()-$decoded->expire)>10){
               return "expired token!";
            }
            return $decoded;
        }catch (Exception $e){
            return "invalid token!";
        }
    }

    private function refreshAccessToken(){
        $token=$this->getBearerToken();
        if(is_null($token)){
            return $this->createMessageToClient("404","Not Found!","not found!");
        }
        try {
            $refreshToken=$_COOKIE["refreshToken"];
            $decoded = JWT::decode($token, keys, array('HS256'));
            $db=new authDB();
            $sql="SELECT * FROM `refreshtokens` WHERE `refresh_id`= '$refreshToken' ";
            $result=$db->getConnection()->query($sql);
            if($result->num_rows==0){
                return $this->createMessageToClient("404","Not Found!","not foundt!");
            }
            $row=$result->fetch_assoc();
            if(time()>$row["expires_at"]){
                $sql="DELETE FROM `refreshtokens` WHERE `refresh_id`= '$refreshToken'";
                $db->getConnection()->query($sql);
                unset($_COOKIE["refreshToken"]);
//                $sql="INSERT INTO `black_list` (`access_id`,`expires_at`) VALUES ('$token','$decoded->expire')";
//                $db->getConnection()->query($sql);
                return $this->createMessageToClient("403","Access denied!","forbidden!");
            }
            $issued_at = time();
            $expiration_time = $issued_at + (10);
            $payload=array(
                "start"=>$issued_at,
                "expire"=>$expiration_time,
                "data"=>array(
                    "user_id"=>$decoded->data->user_id,
                )
            );
            return $this->createMessageToClient(200,"ok",JWT::encode($payload,keys));
        }catch (Exception $e){
            return $this->createMessageToClient("404","Not Found!","not found!");
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