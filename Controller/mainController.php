<?php
require_once ("loginController.php");
require_once ("UserController.php");
require_once ("authHandler.php");
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestedMethod=$_SERVER["REQUEST_METHOD"];

if(isset($uri[3])){
    $controller=null;
    if($uri[3]=="login"){
        $controller=new loginController($requestedMethod);
        $controller->requestProcess();
    }
    if($uri[3]=="User"){
        $controller=new UserController($requestedMethod);
        $controller->requestProcess();
    }
    if($uri[3]=="authAdmin"){
        $controller=new authHandler("GET","Admin");
        $controller->requestProcess();
    }
    if($uri[3]=="authUser"){
        $controller=new authHandler("GET","User");
        $controller->requestProcess();
    }
    if($uri[3]=="refresh"){
        $controller=new authHandler("GET",null,"1");
        $controller->requestProcess();
    }
}else{

}








