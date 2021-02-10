<?php


class loginController
{
    private $requestMethod;
    function __construct()
    {
    }

    public  function login(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if($this->validateLoginInput($input)==false){
            return $this->createMessageToClient(403,"Forbidden","not allowed!");
        }
        $username=$input["username"];
        $password=$input["password"];
        $result=User::getUserByUsername($username);
        if(is_array($result)==false){
           return $this->createMessageToClient(404,"Not Found","User Not Found!");
        }
        if(password_verify($password,$result["password"])==false){
            return  $this->createMessageToClient(403,"Forbidden","Not Allowed!");
        }
        ///// continue generating jwt!


    }




    private function validateLoginInput($input){
        if(!isset($input["username"]) || !isset($input["password"])){
            return false;
        }
        return true;
    }

    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        header($response["header"]);
        $response["body"]=$body;
        return json_encode($response["body"]);
    }




}