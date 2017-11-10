<?php

include_once('Entities/AuthToken.php');
include_once('Logic/Validation.php');

$method = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents('php://input');

?>