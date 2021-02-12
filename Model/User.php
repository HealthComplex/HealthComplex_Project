<?php


class User
{
    private $userId;
    private $type;
    function __construct($userId,$type)
    {
        $this->userId=$userId;
        $this->type=$type;
    }


    public function getType()
    {
        return $this->type;
    }


    public function getUserId()
    {
        return $this->userId;
    }



    public static function getUserById($id){
        $query="SELECT * FROM `user` WHERE `user_id`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("i",$id);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return $result->fetch_assoc();
        }else{
            return "not Found!";
        }
    }

    public static function getUserByUsername($username){
        $query="SELECT `user_id`,`username`,`password`,`type` FROM `user` WHERE `username`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$username);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return $result->fetch_assoc();
        }else{
            return "not Found!";
        }
    }

    public static function createUser($input){ /// input is array
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


    public static function hasUserWithUsername($username){
        $query="SELECT * FROM `user` WHERE `username`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$username);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return true;
        }else{
            return false;
        }
    }

    public static function hasUserWithEmail($email){
        $query="SELECT * FROM `user` WHERE `email`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$email);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return true;
        }else{
            return false;
        }
    }

    public static function hasUserWithPhoneNumber($phoneNumber){
        $query="SELECT * FROM `user` WHERE `phoneNumber`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$phoneNumber);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return true;
        }else{
            return false;
        }
    }






}