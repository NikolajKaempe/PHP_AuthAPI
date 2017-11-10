<?php

include_once('./Services/RequestService.php');
include_once('./Services/ResponseService.php');
include_once('./Repositories/PostsRepository.php');

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

        // RETURN POST BY SPECIFIC USER
        if($postsByUser){

            // get posts by user
            $posts = $postRepository->getPostsByUser($token, $_GET['username']);

            echo json_encode( $posts, JSON_UNESCAPED_UNICODE );
            die;
        }
        
        // RETURN ALL POSTS
        if($postsAmount){

            $posts = $postRepository->getPosts($token, $_GET['amount']);

            echo json_encode( $posts, JSON_UNESCAPED_UNICODE );
            die;
        }
            
    break;

    //--------------------------------------------------------------------------
    // CREATE POST
    //--------------------------------------------------------------------------
    case 'POST':

        $sRequestBody = file_get_contents('php://input');
        $jRequestBody = RequestService::ParseRequestBody($sRequestBody);

        if($jRequestBody === 'corrupted'){
            ResponseService::ResponseBadRequest();
        }
        else{
            ResponseService::ResponseOk("Ok Request");
        }

    break;
}

?>