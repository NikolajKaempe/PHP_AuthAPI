<?php

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/RequestService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/Comment.php');


RequestService::enableCORS();
RequestService::TokenCheck();

$token = RequestService::GetToken();
$requestHttpMethod = $_SERVER['REQUEST_METHOD'];
$requestBody = file_get_contents('php://input');

switch ($requestHttpMethod){
    case 'GET':
        $post_id = RequestService::isNumericUrlParamDefined('post_id')? $_GET['post_id'] : ResponseService::ResponseBadRequest("Invalid Post");
        $amount = RequestService::isNumericUrlParamDefined('amount') ? $_GET['amount'] : 25;
        $offset = RequestService::isNumericUrlParamDefined('offset') ? $_GET['offset'] : 0;

        $comment = new Comment();
        $comments = Comment::getCommentsFromPost($token, $post_id, $amount, $offset);
        ResponseService::ResponseJSON($comment->arrayToJson($comments));
        break;

    case 'POST':
        createComment($token,$requestBody);
        break;

    case 'PUT':
        $id = RequestService::isNumericUrlParamDefined('comment_id')? $_GET['comment_id'] : ResponseService::ResponseBadRequest("Invalid comment");
        updateComment($token,$requestBody,$id);
        break;

    case 'DELETE' :
        $id = RequestService::isNumericUrlParamDefined('comment_id')? $_GET['comment_id'] : ResponseService::ResponseBadRequest("Invalid comment");
        deleteComment($token,$id);
        break;

    default:
        ResponseService::ResponseNotFound();
        break;
}

function createComment($token, $input){
    $comment = new Comment();
    $comment->constructFromHashMap($input);
    $comment = $comment->createComment($token);
    ResponseService::ResponseJSON($comment->toJson());

}

function updateComment($token, $input,$id){
    $comment = new Comment();
    $comment->constructFromHashMap($input);
    $comment->updateComment($token,$id);
    ResponseService::ResponseOk();
}

function deleteComment($token,$id){
    Comment::deleteComment($token,$id);
    ResponseService::ResponseOk();
}

?>