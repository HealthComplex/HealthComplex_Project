<?php

require_once ('../libs/php-jwt-master/src/BeforeValidException.php');
require_once ('../libs/php-jwt-master/src/ExpiredException.php');
require_once ('../libs/php-jwt-master/src/SignatureInvalidException.php');
require_once ('../libs/php-jwt-master/src/JWT.php');
use \Firebase\JWT\JWT;

date_default_timezone_set('Asia/Tehran');
CONST keys="92?VH2WMrx";



class authHandler
{
    private $requestMethod;
    private $expectedType;

    function __construct($requestMethod,$expectedType)
    {
        $this->requestMethod=$requestMethod;
        $this->expectedType=$expectedType;
    }

    public function requestProcess(){
        if($this->requestMethod=="GET"){
            $response=$this->validateToken();
        }
        header($response["header"]);
        echo json_encode($response["body"]);
    }

    public static function generateJwtTokenForUser(User $user){
        $issued_at = time();
        $expiration_time = $issued_at + (604800);
        $payload=array(
            "start"=>$issued_at,
            "expire"=>$expiration_time,
            "data"=>array(
                "user_id"=>$user->getUserId(),
            )
        );
        return JWT::encode($payload,keys);
    }

    private function validateToken(){
        if(isset($_COOKIE["token"])==false){
            return $this->createMessageToClient(403,"Access denied!","forbidden!");
        }
        try {
            $token=$_COOKIE["token"];
            $decoded = JWT::decode($token, keys, array('HS256'));
            if((time()-$decoded->expire)>604800){
                unset($_COOKIE["token"]);
                return $this->createMessageToClient(403,"Access denied!","forbidden!");
            }
            $result=User::getUserById($decoded->data->user_id);
            if($result["type"]!=$this->expectedType){
                return $this->createMessageToClient(403,"Access denied!" ,"forbidden");
            }
            return $this->createMessageToClient(200,"ok","access granted!");
        }
        catch (Exception $exception){
            unset($_COOKIE["token"]);
            return $this->createMessageToClient(403,"Access denied!","forbidden!");
        }
    }


    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        $response["body"]=$body;
        return $response;
    }


}