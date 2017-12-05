<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 05-12-2017
 * Time: 11:49
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/RequestService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/PreferenceRepository.php');


RequestService::enableCORS();
RequestService::TokenCheck();


$token = RequestService::GetToken();
$request = $_SERVER['PATH_INFO'];
$requestHttpMethod = $_SERVER['REQUEST_METHOD'];
$requestBody = file_get_contents('php://input');

switch ($request){
    case '/User':
        switch ($requestHttpMethod){
            case "GET":
                getUserPreferences($token);
                break;

            case "POST" :
                updateUserPreference($token,$requestBody);
                break;

            default:
                ResponseService::ResponseNotFound();
                break;
        }
        break;

    case '/Types':
        getUserPreferenceTypes($token);
        break;

    default:
        ResponseService::ResponseNotFound();
        break;
}

function getUserPreferences($token){
    $preferences = PreferenceRepository::getUserPreferences($token);
    ResponseService::ResponseJSON(json_encode($preferences));
}

function updateUserPreference($token,$input){
    $typeId = RequestService::getParamFromRequestBody($input,"type_id");
    $levelId = RequestService::getParamFromRequestBody($input,"level_id");

    if ($typeId === ""  || !is_numeric($typeId) ||
        $levelId === ""  || !is_numeric($levelId)){
        ResponseService::ResponseBadRequest("Invalid Request-Body");
    }

    PreferenceRepository::updateUserPreference($token,$typeId,$levelId);
    ResponseService::ResponseOk();
}

function getUserPreferenceTypes($token){
    $preferences = PreferenceRepository::getUserPreferenceTypes($token);
    ResponseService::ResponseJSON(json_encode($preferences));
}

