<?php
header("Content-Type: application/json; charset=UTF-8");
require_once ("../Model/User.php");
require_once ("authHandler.php");

class loginController
{
    private $requestMethod;
    function __construct($requestMethod)
    {
        $this->requestMethod=$requestMethod;
    }

    public function requestProcess(){
        $response=null;
        if($this->requestMethod=="POST"){
            $response=$this->login();
        }


        header($response["header"]);
        echo json_encode($response["body"]);
    }

    private  function login(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if($this->validateLoginInput($input)==false){
            return $this->createMessageToClient(403,"Forbidden","wrong username or password!");
        }
        $username=$input["username"];
        $password=$input["password"];
        $result=User::getUserByUsername($username);
        if(is_array($result)==false){
           return $this->createMessageToClient(404,"Not Found","wrong username or password!");
        }
        if(password_verify($password,$result["password"])==false){
            return  $this->createMessageToClient(403,"Forbidden","wrong username or password!");
        }
        $user=new User($result["user_id"],$result["type"],$result["enabled"]);
        $token=authHandler::generateJwtAccessTokenForUser($user);
        $refresh=authHandler::generateJwtRefreshTokenForUser($user);
        setcookie("refreshToken",$refresh,null,null,null,false,true);/// needs to be changed!
        return  $this->createMessageToClient(201,"created",$token);
    }




    private function validateLoginInput($input){
        if(!isset($input["username"]) || !isset($input["password"])){
            return false;
        }
        return true;
    }

    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        $response["body"]=$body;
        return $response;
    }




}