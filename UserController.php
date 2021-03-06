<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 17-10-2017
 * Time: 12:33
 */

include_once('Entities/AuthToken.php');
include_once('Entities/ApplicationUser.php');

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['PATH_INFO'];
$reqBody = file_get_contents('php://input');
$ipAddress = fetIP();

switch ($request){
    case '/Register':
        //echo $ipAddress;
        register($reqBody,$ipAddress);
        break;
    case '/Login':
        tryLogin($reqBody,$ipAddress);
        break;
    case '/RefreshToken':
        tryLogin($reqBody,$ipAddress);
        break;
    case '/Logout':
        header("HTTP/1.0 501 Not Implemented");
        http_response_code(501);
        echo json_encode('Not Implemented');
        break;
    default:
        header("HTTP/1.0 404 Not Found");
        http_response_code(404);
        echo "<h1> 404 Not Found</h1>";
        break;
}

function register($input,$ip){
    $User = new ApplicationUser();
    try{
        $User->constructFromHashMap($input);
    }catch (Exception $e){
        header("HTTP/1.1 400 Bad Request");
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode("Invalid Request Body");
        return;
    }

    try{
        $authToken = $User->createUser($ip);
        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json');
        http_response_code(200);
        echo $authToken->toJson();
        return;
    }catch (Exception $e){
        if ($e->getMessage() == "Internal Server Error") {
            header("HTTP/1.1 500 Internal Server Error");
            http_response_code(500);
        }else{
            header("HTTP/1.1 400 Bad Request");
            http_response_code(400);
        }
        header('Content-Type: application/json');
        echo  json_encode($e->getMessage());
        return;
    }
}

function tryLogin($input, $ip){
    $User = new ApplicationUser();
    try{
        $User->constructFromHashMap($input);
    }catch (Exception $e){
        header("HTTP/1.1 400 Bad Request");
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode($e->getMessage());
        return;
    }

    try{
        $authToken = $User->tryLogin($ip);
        if (empty($authToken)){
            header("HTTP/1.1 500 Internal Server Error");
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode("Internal Server Error");
            return;
        }else{
           // header("HTTP/1.1 200 OK");
            //header('Content-Type: application/json');
            http_response_code(200);
            echo $authToken->toJson();
            return;
        }
    }catch (Exception $e){
        if ($e->getMessage() == "Internal Server Error") {
            header("HTTP/1.1 500 Internal Server Error");
            http_response_code(500);
        }else{
            header("HTTP/1.1 400 Bad Request");
            http_response_code(400);
        }
        header('Content-Type: application/json');
        echo  json_encode($e->getMessage());
        return;
    }
}

function fetIP(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

?>