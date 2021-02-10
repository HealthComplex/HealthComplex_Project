<?php

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

date_default_timezone_set('Asia/Tehran');
CONST keys="92?VH2WMrx";



class authHandler
{

    public static function generateJwtTokenForUser(User $user){
        $issued_at = time();
        $expiration_time = $issued_at + (604800);
        $payload=array(
            "start"=>$issued_at,
            "expire"=>$expiration_time,
            "data"=>array(
                "user_id"=>$user->getUserId(),
                "type"=>$user->getType(),
                "enabled"=>$user->getEnabled()
            )
        );
        return JWT::encode($payload,keys);
    }

    public static function decodeJwtToken(){


    }



}