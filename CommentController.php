<?php

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/RequestService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/Comment_v2.php');


//------------------------------------------------------------------------------
/*

    RequestService::TokenCheck() Checks if REQUEST contains:
        Authorization Header
        Valid Token

            if TokenCheck() failed -> 
            Service response to client with Not Authorized 401
            AND the below functions will not be executed
*/
//------------------------------------------------------------------------------
RequestService::enableCORS();
RequestService::TokenCheck();

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------


/*
 *
    The Below Code will be Executed ONLY IF REQUEST CONTAINS A VALID TOKEN
*/

// Get token value from REQUEST
$token = RequestService::GetToken();

// CREATE CommentsRepository
$requestHttpMethod = $_SERVER['REQUEST_METHOD'];


// HANDLE REQUEST
switch ($requestHttpMethod){

    //--------------------------------------------------------------------------
    // GET COMMENTS
    //--------------------------------------------------------------------------
    case 'GET':

        $post_id = RequestService::isNumericUrlParamDefined('post_id')? $_GET['post_id'] : ResponseService::ResponseBadRequest("Invalid Post");
        $amount = RequestService::isNumericUrlParamDefined('amount') ? $_GET['amount'] : 25;
        $offset = RequestService::isNumericUrlParamDefined('offset') ? $_GET['offset'] : 0;

        $comment = new Comment_v2();
        $comments = $comment->getCommentsFromPost($token, $post_id, $amount, $offset);
        ResponseService::ResponseJSON($comment->arrayToJson($comments));
    // END OF GET COMMENTS      
    break;

    //--------------------------------------------------------------------------
    // POST COMMENTS
    //--------------------------------------------------------------------------
    case 'POST':
    $sRequestBody = file_get_contents('php://input');
    createComment($token,$sRequestBody);
    break;
}

// ++ //
function createComment($token, $input){
    $comment = new Comment_v2();
    $comment->constructFromHashMap($input);
    $comment = $comment->createComment($token);
    ResponseService::ResponseJSON($comment->toJson());

}

?>