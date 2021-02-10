<?php

header("Content-Type: application/json; charset=UTF-8");
require_once ("../Model/User.php");
require_once ("databaseController.php");
class UserController
{

    private $requestMethod;

    function __construct($requestMethod)
    {
        $this->requestMethod=$requestMethod;
    }


    public function requestProcess(){
        $response=null;
        if($this->requestMethod=="POST")
            $response=$this->registerUser();

        header($response["header"]);
        echo json_encode($response["body"]);
    }

    private function registerUser(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result=$this->checkValidation($input);
        if(is_array($result))return $result;
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

    private function checkValidation($input){
        if(!isset($input["username"]) || !isset($input["password"]) || !isset($input["email"])||
            !isset($input["phoneNumber"]) || !isset($input["firstname"]) || !isset($input["lastname"])
    || !isset($input["city"]) || !isset($input["address"]) || !isset($input["countryCode"])){
            return $this->createMessageToClient(403,"not allowed!","please complete inputs!");
        }
        if(strlen($input["username"])<8 || strlen($input["username"])>20){
            return $this->createMessageToClient(403,"invalid","invalid username!");
        }

        if(strlen($input["password"])<8 || strlen($input["password"])>20){
            return $this->createMessageToClient(403,"invalid","invalid password!");
        }

        if(preg_match('/^[a-zA-Z0-9]{5,}$/', $input["username"]) == false) {
            return $this->createMessageToClient(403,"invalid","the username must have only alphanumeric characters!");
        }
        if(preg_match('/^[a-zA-Z0-9]{5,}$/', $input["password"]) == false) {
            return $this->createMessageToClient(403,"invalid","the password must have only alphanumeric characters!");
        }

        if(is_numeric($input["phoneNumber"]) ==false){
            return $this->createMessageToClient(403,"invalid","please enter valid phone number!");
        }

        if (!filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
            return $this->createMessageToClient(403,"invalid","please enter valid email!");
        }

        if(User::hasUserWithUsername($input["username"])){
            return $this->createMessageToClient(403,"invalid!","this username was registered in the system!");
        }
        if(User::hasUserWithPhoneNumber($input["phoneNumber"])){
            return $this->createMessageToClient(403,"invalid!","this phoneNumber was registered in the system!");
        }
        if(User::hasUserWithEmail($input["email"])){
            return $this->createMessageToClient(403,"invalid!","this email was registered in the system!");
        }
        return true;
    }

    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        $response["body"]=$body;
        return $response;
    }



}