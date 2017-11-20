<?php

include_once('Services/RequestService.php');
include_once('Services/ResponseService.php');
include_once('Services/SanitizeService.php');
include_once('Repositories/PostsRepository.php');
include_once('Entities/PostModel.php');

$requestHttpMethod = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents('php://input');

$POSTS_DEFAULT_AMOUNT = 50;
$POSTS_DEFAULT_OFFSET = 30;

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

// CREATE PostsRepository
$postRepository = new PostsRepository();

// HANDLE REQUEST
switch ($requestHttpMethod){

    //--------------------------------------------------------------------------
    // GET POSTS
    //--------------------------------------------------------------------------
    case 'GET':

        RequestService::validateNumericUrlParam('user_id');
        $user_id = $_GET['user_id']; 
        $postsAmount = RequestService::isNumericUrlParamDefined('amount') ? $_GET['amount'] : $POSTS_DEFAULT_AMOUNT;
        $offset      = RequestService::isNumericUrlParamDefined('offset') ? $_GET['offset'] : $POSTS_DEFAULT_OFFSET;

        // RETURN POST BY SPECIFIC USER
        if($postsByUser){
            $posts = $postRepository->getPostsByUser(
                $token,
                $user_id,
                $postsAmount,
                $offset
            );
            ResponseService::ResponseJSON($posts);
        }

        // RETURN RECENT POSTS BY ALL USERS
        $posts = $postRepository->getPosts(
            $token, 
            $postsAmount,
            $offset
        );
        ResponseService::ResponseJSON($posts);

    // END OF GET POSTS      
    break;

    //--------------------------------------------------------------------------
    // CREATE POST
    //--------------------------------------------------------------------------
    case 'POST':

        // Convert JSON to PHP object
        $sRequestBody = file_get_contents('php://input');
        $jRequestBody = RequestService::ParseRequestBody($sRequestBody);

        // IF BAD JSON
        if($jRequestBody === 'corrupted'){
            ResponseService::ResponseBadRequest("Bad Request");
        }

        // Check if RequestBody Contains required data
        $Validation  = new Validation();
        $isValidData = $Validation->hasAllProperties(
            $jRequestBody, Post::getRequiredProperties()
        );
        
        // If RequestBody Doesn't contain required fields
        if(!$isValidData){
            ResponseService::ResponseBadRequest("Bad Request");
        }

        // SANITIZE Post Properties
        $sanizedRequestBody = SanitizeService::SanitizeObjectsProperties(
            $jRequestBody, Post::getRequiredProperties());
        
        
        // SAVE POST TO DB
        $newPostId = $postRepository->createPost(
            $token, 
            $sanizedRequestBody->title,
            $sanizedRequestBody->content
        );

        if(isset($newPostId)){
            ResponseService::ResponseCreated("Post Successfully Created");
        }else{
            ResponseService::ResponseInternalError("Internal Server Error");
        }
        
        // END OF CREATE POST
        break;

}

?>