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


?>