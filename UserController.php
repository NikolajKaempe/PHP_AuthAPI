<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 17-10-2017
 * Time: 12:33
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/AuthToken.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/User.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/RequestService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');


$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['PATH_INFO'];
$reqBody = file_get_contents('php://input');
$ipAddress = RequestService::fetIP();
RequestService::enableCORS();




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
    case '/Picture':
        RequestService::TokenCheck();
        $token = RequestService::GetToken();
        switch ($method){
            case 'GET':
                getPicture($token);
                break;
            case 'POST':
                updatePicture($token,$reqBody);
                break;
            default:
                ResponseService::ResponseNotFound();
                break;
        }
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

function getPicture($token){
    if (RequestService::isParamSet("user_id")){
        $picture = RequestService::isNumericUrlParamDefined('user_id')? User::getPictureFromId($token,$_GET['user_id']) : ResponseService::ResponseBadRequest();
    }else{
        $picture = User::getPicture($token);
    }

    ResponseService::ResponseJSON(json_encode($picture));
}

function updatePicture($token,$input){
    $picture = RequestService::getParamFromRequestBody($input,"picture");
    if ($picture === "" || empty($picture)){ ResponseService::ResponseBadRequest("Invalid Request-Body"); }
    User::updatePicture($token,$picture);
}


?>