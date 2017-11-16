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
    NB! 

    THIS SERVICE IS ONLY FOR DEVELOPMENT PURPOSES

    It can be used to simulate behavior of  [PostController.php] script

    Set of API endpoints that respond with Dummy Posts data 
*/
//------------------------------------------------------------------------------

RequestService::enableCORS();

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

            $dummyPosts = getDummyPosts(15);
            ResponseService::ResponseJSON($dummyPosts);
        }
        
        // RETURN ALL POSTS

        $dummyPosts = getDummyPosts(25);
        ResponseService::ResponseJSON($dummyPosts);


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
        
        ResponseService::ResponseCreated("Post Successfully Created");
        
        break;

}


function getDummyPosts($amountOfPostsToReturn = 10){

    $postsArray = array();

    for ($i=1; $i <= $amountOfPostsToReturn; $i++) { 

        array_push(
            $postsArray, 
            new Post(
                $i, 
                "Post Title #".$i, 
                "Post Content #".$i." ."."Lorem Ipsum is simply dummy text of the printing and typesetting industry", 
                "14.11.2017")
        );
    }

    return $postsArray;
}


?>