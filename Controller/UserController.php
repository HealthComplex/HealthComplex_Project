<?php


class UserController
{

    private $requestMethod;

    function __construct($requestMethod)
    {
        $this->requestMethod=$requestMethod;
    }


    public function requestProcess(){
        if($this->requestMethod=="POST")
            $this->registerUser();

    }

    private function registerUser(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if($this->validateInputForRegister($input)==false){
         return $this->createMessageToClient(403,"Forbidden","not allowed!");
        }
        User::createUser($input);
        return $this->createMessageToClient(201,"created","successfully created!");
    }


//    public static function saveUserInSession($user){
//        if(session_status()==PHP_SESSION_NONE){
//            session_start();
//        }
//        $_SESSION["userObj"]=serialize($user);
//        return session_id();
//    }
//
//    public static function getUserFromSession(){
//        if(session_status()==PHP_SESSION_NONE){
//            session_start();
//        }
//        return unserialize($_SESSION["userObj"]);
//    }

    private function validateInputForRegister($input){
        if(!isset($input["username"]) || !isset($input["password"]) || !isset($input["email"])||
            !isset($input["phoneNumber"]) || !isset($input["firstname"]) || !isset($input["lastname"])
    || !isset($input["city"]) || !isset($input["address"]) || !isset($input["countryCode"])){
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