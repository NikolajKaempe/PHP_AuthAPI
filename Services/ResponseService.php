<?php
class ResponseService
{
    
    // Responds with 200 status and message
    public static function ResponseOk($msg = "undefined")
    {
        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json');
        http_response_code(200);
        if($msg != "undefined"){
            echo '{"message":"'.$msg.'"}';
        }
        die;
    }

    // Responds with 200 status and data
    public static function ResponseJSON($data)
    {
        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json');
        http_response_code(200);
        echo $data;
        die;
    }

    // Responds with 201 status and message
    public static function ResponseCreated($msg = "undefined")
    {
        header("HTTP/1.1 201");
        header('Content-Type: application/json');
        http_response_code(201);
        if($msg != "undefined"){
            echo '{"message":"'.$msg.'"}';
        }
        die;
    }

    // Responds with 400 status and message
    public static function ResponseBadRequest($msg = "undefined")
    {
        header("HTTP/1.1 400");
        header('Content-Type: application/json');
        http_response_code(400);

        if($msg != "undefined"){
            echo '{"message":"'.$msg.'"}';
        }

        die;
    }

    // Responds with 401 status and message
    public static function ResponsenotAuthorized($msg = "Access Denied")
    {
        header("HTTP/1.1 401");
        header('Content-Type: application/json');
        http_response_code(401);

        if($msg != "undefined"){
            echo '{"message":"'.$msg.'"}';
        }

        die;
    }

    // Responds with 404 status and message
    public static function ResponseNotFound($msg = "Not Found")
    {
        header("HTTP/1.1 404");
        header('Content-Type: application/json');
        http_response_code(404);

        if($msg != "undefined"){
            echo '{"message":"'.$msg.'"}';
        }

        die;
    }

    // Responds with 500 status and message
    public static function ResponseInternalError($msg = "undefined")
    {
        header("HTTP/1.1 500");
        header('Content-Type: application/json');
        http_response_code(500);

        if($msg != "undefined"){
            echo '{"message":"'.$msg.'"}';
        }

        die;
    }

    // Responds with 501 status and message
    public static function ResponseNotImplemented($msg = "undefined")
    {
        header("HTTP/1.1 501");
        header('Content-Type: application/json');
        http_response_code(501);

        if($msg != "undefined"){
            echo '{"message":"'.$msg.'"}';
        }

        die;
    }

}
?>