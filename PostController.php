<?php

include_once('Entities/AuthToken.php');
include_once('Logic/Validation.php');
include_once('./Services/RequestService.php');

$method = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents('php://input');

/*
    Checks if REQUEST contains:
        Authorization Header
        Valid Token

        if on of the check failed -> 
        function response with NotAuthorized 401
        AND the below functions will not be called
*/
RequestService::TokenCheck();

// Get token value from REQUEST
$token = RequestService::GetToken();


?>