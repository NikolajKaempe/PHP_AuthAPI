<?php


include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/AuthToken.php');

class RequestService
{

    //--------------------------------------------------------------------------
    // CHECK IF CLIENT's REQUEST CONTAINS TOKEN
    public static function TokenCheck()
    {
        $headers = apache_request_headers();

        // IF NO Authorization HEADER -> response NOT AUTHORIZED
        if(!isset($headers['Authorization'])){
             ResponseService::ResponseNotAuthorized("Access Denied");
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

    //--------------------------------------------------------------------------
    // CONVERT JSON STRING TO PHP OBJECT
    public static function ParseRequestBody($string)
    {
        $objects = json_decode($string);
			return (json_last_error() == 0) ? $objects : "corrupted";
    }

    //--------------------------------------------------------------------------
    // ALLOW CORS
    public static function enableCORS()
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    //--------------------------------------------------------------------------
    // VALIDATE NUMERIC PARAM

    public static function validateNumericUrlParam($paramName){

        $isParam = isset($_GET[$paramName]) && !empty($_GET[$paramName] && is_numeric($_GET[$paramName]));

        if(!$isParam){
            ResponseService::ResponseBadRequest("Bad Request");
        }
    }

    //--------------------------------------------------------------------------
    // IS PARAM IS SET
    public static function isParamSet($paramName){
        return isset($_GET[$paramName]) && !empty($_GET[$paramName]);
    }

    //--------------------------------------------------------------------------
    // IS PARAM NUMERIC
    public static function isNumeric($paramName){
        return is_numeric($_GET[$paramName]);
    }

    // CHECK IF URL PARAM DEFINED && IT IS A NUMBER
    public static  function isNumericUrlParamDefined($paramName){
        return RequestService::isParamSet($paramName) && RequestService::isNumeric($paramName);
    }

    public static function fetIP(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
} 

    //--------------------------------------------------------------------------
    // CHECK IF Authorization header value contains TOKEN
    function checkIfTokenExist($authHeaderValue)
    {
        // remove possible whitespaces
        $authHeaderValue = trim($authHeaderValue);

        // If auth Header contains NO TOKEN
        if( strpos( $authHeaderValue, "Token=" ) === false ) {
            ResponseService::ResponseNotAuthorized("Token is missing");
        }
        
        $token = RequestService::GetToken();

        new AuthToken($token);

    }


    
?>