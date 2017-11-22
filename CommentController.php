<?php

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/RequestService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/Comment_v2.php');
//include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/SanitizeService.php');
//include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/CommentModel.php');
//include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/CommentsRepository.php');


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
        //RequestService::validateNumericUrlParam('post_id');
        //$post_id = $_GET['post_id'];
/*++*/  $post_id = RequestService::isNumericUrlParamDefined('post_id')? $_GET['post_id'] : ResponseService::ResponseBadRequest("Invalid Post");
        $amount = RequestService::isNumericUrlParamDefined('amount') ? $_GET['amount'] : 25;
        $offset = RequestService::isNumericUrlParamDefined('offset') ? $_GET['offset'] : 0;

/*++*/  $comment = new Comment_v2();
/*++*/  $comments = $comment->getCommentsFromPost($token, $post_id, $amount, $offset);
/*++*/  ResponseService::ResponseJSON($comment->arrayToJson($comments));
        //$comments = $commentsRepository->getCommentsOfPost($token, $post_id, $amount, $offset);
        //ResponseService::ResponseJSON($comments);
    // END OF GET COMMENTS      
    break;

    //--------------------------------------------------------------------------
    // POST COMMENTS
    //--------------------------------------------------------------------------
    case 'POST':
    $sRequestBody = file_get_contents('php://input');
    createComment($token,$sRequestBody);

/*
    // Convert JSON to PHP object
    $jRequestBody = RequestService::ParseRequestBody($sRequestBody);

     // IF BAD JSON
    if($jRequestBody === 'corrupted'){
        ResponseService::ResponseBadRequest("Bad Request");
    }

    // Check if RequestBody Contains required data
    $Validation  = new Validation();

    $isValidData = $Validation->hasAllProperties(
        $jRequestBody, Comment::getRequiredProperties()
    );

    // If RequestBody Doesn't contain required fields
    if(!$isValidData){
        ResponseService::ResponseBadRequest("Bad Request");
    }

    // SANITIZE Post Properties
    $sanizedRequestBody = SanitizeService::SanitizeObjectsProperties(
        $jRequestBody, Comment::getRequiredProperties());

    $newCommentId = $commentsRepository->createComment(
        $token,
        $sanizedRequestBody->$post_id,
        $sanizedRequestBody->$content
    );

    if(isset($newPostId)){
        ResponseService::ResponseCreated("Comment Successfully Created");
    }else{
        ResponseService::ResponseInternalError("Internal Server Error");
    }
*/
    // END OF POST COMMENTS 
    break;
}

// ++ //
function createComment($token, $input){
    $comment = new Comment_v2();
    $comment->constructFromHashMap($input);
    $comment->createComment($token);
    ResponseService::ResponseJSON($comment->idToJson());

}

?>