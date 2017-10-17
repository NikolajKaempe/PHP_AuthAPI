<?php
/**
 * Created by PhpStorm.
 * User: Kæmpe & Markus
 * Date: 17-10-2017
 * Time: 11:32
 */

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

echo $request;


// Call respective controller or return 404 on default

