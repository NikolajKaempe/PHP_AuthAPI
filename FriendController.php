<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 05-12-2017
 * Time: 09:32
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/RequestService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/FriendRepository.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/FriendInvite.php');


RequestService::enableCORS();
RequestService::TokenCheck();


$token = RequestService::GetToken();
$request = $_SERVER['PATH_INFO'];
$requestHttpMethod = $_SERVER['REQUEST_METHOD'];
$requestBody = file_get_contents('php://input');

switch ($request){
    case '/All':
        getAllFriends($token);
        break;

    case '/Online':
        getOnlineFriends($token);
        break;

    case '/Invites':
        switch ($requestHttpMethod){
            case 'GET':
                getPendingInvites($token);
                break;

            case 'POST' :
                invite($token,$requestBody);
                break;

            case 'PUT':
                processInvite($token,$requestBody);
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

function getAllFriends($token){
    $friends = FriendRepository::getAll($token);
    ResponseService::ResponseJSON(json_encode($friends));
}

function getOnlineFriends($token){
    $friends = FriendRepository::getOnline($token);
    ResponseService::ResponseJSON(json_encode($friends));
}

function getPendingInvites($token){
    $invites = FriendRepository::getPendingInvites($token);
    ResponseService::ResponseJSON(json_encode($invites));

}

function invite($token,$input){
    $userId = RequestService::getParamFromRequestBody($input,"user_id");
    if ($userId === ""  || !is_numeric($userId)){ ResponseService::ResponseBadRequest("Invalid Request-Body"); }

    FriendRepository::invite($token,$userId);
    ResponseService::ResponseOk();
}

function processInvite($token,$input){
    $friendInvite = new FriendInvite();
    $friendInvite->constructFromHashMap($input);
    $friendInvite->processInvite($token);
    ResponseService::ResponseOk();
}

