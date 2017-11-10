<?php


include_once('ResponseService.php');

class RequestService
{

    //--------------------------------------------------------------------------
    // CHECK IF CLIENT's REQUEST CONTAINS TOKEN
    public static function TokenCheck()
    {
        $headers = apache_request_headers();

        // IF NO Authorization HEADER -> response NOT AUTHORIZED
        if(!isset($headers['Authorization'])){
             ResponseService::ResponsenotAuthorized("No Authorization Header");
        } 

        // CHECK IF Authorization HEADER contains Token
        checkIfTokenExist($headers['Authorization']);

    }

    //--------------------------------------------------------------------------
    // GET TOKEN FROM REQUEST
    public static function GetToken()
    {
        $headers = apache_request_headers();
        $token = substr($headers['Authorization'], strlen("Token="));
        return $token;
    }
} 

    //--------------------------------------------------------------------------
    // CHECK IF Authorization header value contains TOKEN
    function checkIfTokenExist($authHeaderValue)
    {
        // remove possible whitespaces
        $authHeaderValue = trim($authHeaderValue);

        if( strpos( $authHeaderValue, "Token=" ) === false ) {
            ResponseService::ResponsenotAuthorized("Token is missing");
        }
    }
?>