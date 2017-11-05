<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 17-10-2017
 * Time: 12:33
 */

include_once('Entities/AuthToken.php');
include_once('Entities/ResponseUser.php');
include_once('Logic/Validation.php');


$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['PATH_INFO'];
$input = file_get_contents('php://input');

switch ($request){
    case '/VerifyToken':

        $AuthToken = new AuthToken();
        try{
            $AuthToken->constructFromHashMap($input);
        }catch (Exception $e){
            header("HTTP/1.1 400 Bad Request");
            http_response_code(400);
            echo "Invalid Request Body";
            break;
        }

        $User = $AuthToken->fetchUser();

        if (!empty($User->getUsername())){
            header("HTTP/1.1 200 OK");
            header('Content-Type: application/json');
            http_response_code(200);
            echo $User->toJson();
        }
        else{
            header("HTTP/1.1 400 Bad Request");
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode("Invalid Token");
        }
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        http_response_code(404);
        echo "<h1> 404 Not Found</h1>";
        break;
}

