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
        if($this->requestMethod=="POST")
            $response=$this->registerUser();


        header($response["header"]);
        echo json_encode($response["body"]);
    }

    private function registerUser(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result=$this->checkValidation($input);
        if($result!=true) return $result;
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