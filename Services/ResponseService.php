<?php
class ResponseService
{
    
    // Responds with 200 status and message
    public static function ResponseOk($msg)
    {
        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json');
        http_response_code(200);
        echo '{"messsage":"'.$msg.'"}';
        die;
    }

    // Responds with 401 status and message
    public static function ResponsenotAuthorized($msg)
    {
        header("HTTP/1.1 401");
        header('Content-Type: application/json');
        http_response_code(401);
        echo '{"messsage":"'.$msg.'"}';
        die;
    }
}
?>