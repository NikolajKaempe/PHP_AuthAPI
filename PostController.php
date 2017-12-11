<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 22-11-2017
 * Time: 13:40
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/RequestService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/SanitizeService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/PostsRepository.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/Post.php');


$request = (empty($_SERVER['PATH_INFO']))? "/" : $_SERVER['PATH_INFO'] ;
$method = $_SERVER['REQUEST_METHOD'];
$reqBody = file_get_contents('php://input');

$POSTS_DEFAULT_AMOUNT = 50;
$POSTS_DEFAULT_OFFSET = 0;

RequestService::enableCORS();
$ipAddress = RequestService::fetIP();

RequestService::TokenCheck();
$token = RequestService::GetToken();

$postRepository = new PostsRepository();


switch ($request){
    case "/":
        switch ($method){
            case 'GET':
                getPosts($token,$POSTS_DEFAULT_AMOUNT,$POSTS_DEFAULT_OFFSET);
                break;
            case 'POST':
                createPost($reqBody,$token);
                break;
            case 'PUT':
                $id = RequestService::isNumericUrlParamDefined('post_id')? $_GET['post_id'] : ResponseService::ResponseBadRequest("Invalid Post");
                updatePost($reqBody,$token,$id);
                break;
            case 'DELETE':
                $id = RequestService::isNumericUrlParamDefined('post_id')? $_GET['post_id'] : ResponseService::ResponseBadRequest("Invalid Post");
                deletePost($token,$id);
                break;
            default:
                ResponseService::ResponseNotFound();
                break;
        }
        break;

    case "/Preferences":
        switch ($method) {
            case 'GET':
                getDefaultPostPreferences($token);
                break;

            case "PUT":
                updateDefaultPostPreference($token,$reqBody);
                break;
        }
        break;

    case "/Preferences/":
        switch ($method) {
            case 'GET':
                $id = RequestService::isNumericUrlParamDefined('post_id')? $_GET['post_id'] : ResponseService::ResponseBadRequest("Invalid Post");
                getSpecificPostPreferences($token,$id);
                break;

            case "PUT":
                $id = RequestService::isNumericUrlParamDefined('post_id')? $_GET['post_id'] : ResponseService::ResponseBadRequest("Invalid Post");
                updateSpecificPostPreference($token,$id,$reqBody);
                break;

            case "DELETE":
                $id = RequestService::isNumericUrlParamDefined('post_id')? $_GET['post_id'] : ResponseService::ResponseBadRequest("Invalid Post");
                restorePostPreferenceToDefault($token,$id);
                break;
        }
        break;

    case "/Preferences/Types" :
        getPostPreferenceTypes($token);
        break;

    default:
        ResponseService::ResponseNotFound();
        break;
}


function getPosts($token,$defaultAmount,$defaultOffset){
    $postAmount = RequestService::isNumericUrlParamDefined('amount') ? $_GET['amount'] : $defaultAmount;
    $postOffset = RequestService::isNumericUrlParamDefined('offset') ? $_GET['offset'] : $defaultOffset;
    $userId     = RequestService::isNumericUrlParamDefined('user_id')? $_GET['user_id'] : 0;
    $recent     = RequestService::isParamSet('recent')? true : false;

    $post = new Post();

    if ($recent){
        $posts = $post->getRecent($token,$postAmount,$postOffset);
    }else{
        $posts = $post->getFromUser($token,$userId,$postAmount,$postOffset);
    }

    ResponseService::ResponseJSON($post->arrayToJson($posts));
}

function createPost($input,$token){
    $post = new Post();
    $post->constructFromHashMap($input);
    $post = $post->createPost($token);
    ResponseService::ResponseJSON($post->toJson());
}

function updatePost($input,$token,$id){
    $post = new Post();
    $post->constructFromHashMap($input);
    $post->updatePost($token,$id);
    ResponseService::ResponseOk();
}

function deletePost($token,$id){
    Post::deletePost($token,$id);
    ResponseService::ResponseOk();
}

function getDefaultPostPreferences($token){
    $result = PostsRepository::getDefaultPostPreferences($token);
    ResponseService::ResponseJSON(json_encode($result));
}

function updateDefaultPostPreference($token,$input){
    $typeId = RequestService::getParamFromRequestBody($input,"type_id");
    $levelId = RequestService::getParamFromRequestBody($input,"level_id");

    if ($typeId === ""  || !is_numeric($typeId) ||
        $levelId === ""  || !is_numeric($levelId)){
        ResponseService::ResponseBadRequest("Invalid Request-Body");
    }

    PostsRepository::updateDefaultPostPreference($token,$typeId,$levelId);
    ResponseService::ResponseOk();
}

function getSpecificPostPreferences($token,$postId){
    $result = PostsRepository::getSpecificPostPreferences($token,$postId);
    ResponseService::ResponseJSON(json_encode($result));
}

function updateSpecificPostPreference($token,$postId,$input){
    $typeId = RequestService::getParamFromRequestBody($input,"type_id");
    $levelId = RequestService::getParamFromRequestBody($input,"level_id");

    if ($typeId === ""  || !is_numeric($typeId) ||
        $levelId === ""  || !is_numeric($levelId)){
        ResponseService::ResponseBadRequest("Invalid Request-Body");
    }

    PostsRepository::updateSpecificPostPreference($token,$postId,$typeId,$levelId);
    ResponseService::ResponseOk();
}

function restorePostPreferenceToDefault($token,$postId){
    $result = PostsRepository::restorePostPreferenceToDefault($token,$postId);
    ResponseService::ResponseJSON(json_encode($result));
}

function getPostPreferenceTypes($token){
    $result = PostsRepository::getPostPreferenceTypes($token);
    ResponseService::ResponseJSON(json_encode($result));
}

