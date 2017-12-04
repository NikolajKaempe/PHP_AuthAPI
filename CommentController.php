<?php

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/RequestService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/Comment.php');


RequestService::enableCORS();
RequestService::TokenCheck();

$token = RequestService::GetToken();
$requestHttpMethod = $_SERVER['REQUEST_METHOD'];
var_dump($_SERVER["HTTP_REFERER"]);
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
        $sRequestBody = file_get_contents('php://input');
        createComment($token,$sRequestBody);
        break;
}

// ++ //
function createComment($token, $input){
    $comment = new Comment();
    $comment->constructFromHashMap($input);
    $comment = $comment->createComment($token);
    ResponseService::ResponseJSON($comment->toJson());

}

?>