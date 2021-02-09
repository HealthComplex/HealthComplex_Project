<?php


class User
{
    private $userId;
    private $isLoggedIn;
    function __construct($userId,$isLoggedIn)
    {
        $this->userId=$userId;
        $this->isLoggedIn=$isLoggedIn;
    }


    public function getUserId()
    {
        return $this->userId;
    }

    public function getIsLoggedIn()
    {
        return $this->isLoggedIn;
    }

    public static function getUserById($id){
        $query="SELECT * FROM `user` WHERE `user_id`=$id";
        $db=new databaseController();
        $result=$db->getConnection()->query($query);
        if($result->num_rows>0){
            return $result->fetch_assoc();
        }else{
            return "not Found!";
        }
    }

    public static function getUserByUsername($username){
        $query="SELECT * FROM `user` WHERE `username`=$username";
        $db=new databaseController();
        $result=$db->getConnection()->query($query);
        if($result->num_rows>0){
            return $result->fetch_assoc();
        }else{
            return "not Found!";
        }
    }

    public function createUser($input){ /// input is array
        $username=databaseController::makeSafe($input["username"]);
        $email=databaseController::makeSafe($input["email"]);
        $password=password_hash(databaseController::makeSafe($input["password"]),PASSWORD_DEFAULT);
        $firstname=databaseController::makeSafe($input["firstname"]);
        $lastname=databaseController::makeSafe($input["lastname"]);
        $countryCode=databaseController::makeSafe($input["countryCode"]);
        $phoneNumber=databaseController::makeSafe($input["phoneNumber"]);
        $city=databaseController::makeSafe($input["city"]);
        $address=databaseController::makeSafe($input["address"]);

        $query="INSERT INTO `user` (`username`,`email`,`password`,`firstname`,`lastname`,`countryCode`,`phoneNumber`,`city`,
        `address`) VALUES (?,?,?,?,?,?,?,?,?)";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("sssssssss",$username,$email,$password,$firstname,$lastname,$countryCode,$phoneNumber,$city,$address);
        $statement->execute();
    }






}