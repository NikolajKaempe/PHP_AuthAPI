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
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/Post_v2.php');

$method = $_SERVER['REQUEST_METHOD'];
$reqBody = file_get_contents('php://input');

$POSTS_DEFAULT_AMOUNT = 50;
$POSTS_DEFAULT_OFFSET = 0;

RequestService::enableCORS();
$ipAddress = RequestService::fetIP();

RequestService::TokenCheck();
$token = RequestService::GetToken();

$postRepository = new PostsRepository();

switch ($method){
    case 'GET':
        getPosts($token,$POSTS_DEFAULT_AMOUNT,$POSTS_DEFAULT_OFFSET);
        break;
    case 'POST':
        createPost($reqBody,$token);
        break;
    default:
        ResponseService::ResponseNotFound();
        break;
}

function getPosts($token,$defaultAmount,$defaultOffset){
    $postAmount = RequestService::isNumericUrlParamDefined('amount') ? $_GET['amount'] : $defaultAmount;
    $postOffset = RequestService::isNumericUrlParamDefined('offset') ? $_GET['offset'] : $defaultOffset;
    $userId     = RequestService::isNumericUrlParamDefined('user_id')? $_GET['user_id'] : 0;

    $post = new Post_v2();

    if ( $userId === 0){
        $posts = $post->getRecent($token,$postAmount,$postOffset);
    }else{
        $posts = $post->getFromUser($token,$userId,$postAmount,$postOffset);
    }
    ResponseService::ResponseJSON($post->arrayToJson($posts));
}

function createPost($input,$token){
    $post = new Post_v2();
    $post->constructFromHashMap($input);
    $post->createPost($token); // TODO
    ResponseService::ResponseJSON($post->idToJson());
}

