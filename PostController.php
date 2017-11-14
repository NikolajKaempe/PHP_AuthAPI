<?php

include_once('./Services/RequestService.php');
include_once('./Services/ResponseService.php');
include_once('./Services/SanitizeService.php');
include_once('./Repositories/PostsRepository.php');
include_once('./Entities/PostModel.php');

$method = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents('php://input');


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
switch ($method){

    //--------------------------------------------------------------------------
    // GET POSTS
    //--------------------------------------------------------------------------
    case 'GET':

        $postsAmount = isset($_GET['amount']) && !empty($_GET['amount']);
        $postsByUser = isset($_GET['username']) && !empty($_GET['username']);

        if($postsAmount){

            if(!is_numeric($_GET['amount'])){
                ResponseService::ResponseBadRequest("Bad Request");
            }

            $postsAmount = $_GET['amount'];
        }

        // RETURN POST BY SPECIFIC USER
        if($postsByUser){
            $posts = $postRepository->getPostsByUser($token, $_GET['username']);
            ResponseService::ResponseJSON($posts);
        }

        
        // RETURN RECENT POSTS BY VARIOUS USERS
        if($postsAmount){

            $posts = $postRepository->getPosts($token, $_GET['amount']);
             ResponseService::ResponseJSON($posts);
        }

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
        }

        //@TODO - WHAT RESPONSE IN CASE F POST IS NOT CREATED 
        // END OF CREATE POST
        break;

}


?>