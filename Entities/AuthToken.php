<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 17-10-2017
 * Time: 12:33
 */

include_once('/../Repositories/StoredProcedures.php');
include_once('/../Logic/Validation.php');

class AuthToken{

    private $token;
    private $timeAlive;

    public function construct($token,$timeAlive){
        $this->token = $token;
        $this->timeAlive= $timeAlive;
    }

    public function constructFromHashMap($json)
    {
        $data = json_decode($json, true);
        foreach ($data AS $key => $value) $this->{$key} = $value;
        if (empty($data)) throw new Exception("Object Not Valid");
        if (!$this->isObjectValid()) throw new Exception("Object Not Valid");
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getTimeAlive()
    {
        return $this->timeAlive;
    }

    public function fetchUser(){
        $procedures = new StoredProcedures();
        $User = $procedures->fetchOnlineUser($this->token);
        return $User;
    }

    public function toJson(){
        return json_encode(get_object_vars($this));
    }

    private function isObjectValid()
    {
        $validation = new Validation();

        if (!$validation->isValidToken($this->token)) {
            $isValid =  false;
        }else{
            $isValid = true;
        }
        return $isValid;
    }
}