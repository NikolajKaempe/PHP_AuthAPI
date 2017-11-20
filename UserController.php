<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 17-10-2017
 * Time: 12:33
 */

include_once('Entities/AuthToken.php');
include_once('Entities/User.php');
include_once('Services/RequestService.php');
include_once('Services/ResponseService.php');


$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['PATH_INFO'];
$reqBody = file_get_contents('php://input');
$ipAddress = RequestService::fetIP();

switch ($request){
    case '/Register':
        register($reqBody,$ipAddress);
        break;
    case '/Login':
        tryLogin($reqBody,$ipAddress);
        break;
    case '/RefreshToken':
        tryLogin($reqBody,$ipAddress);
        break;
    case '/Logout':
        ResponseService::ResponseNotImplemented();
        break;
    default:
        ResponseService::ResponseNotFound();
        break;
}

function register($input,$ip){
    $User = new User();
    $User->constructFromHashMap($input);
    $authToken = $User->createUser($ip);
    ResponseService::ResponseJSON($authToken->toJson());
}

function tryLogin($input, $ip){
    $User = new User();
    $User->constructFromHashMap($input);
    $authToken = $User->tryLogin($ip);
    ResponseService::ResponseJSON($authToken->toJson());
}

?>