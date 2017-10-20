<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 17-10-2017
 * Time: 12:33
 */

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
        //TODO Retrieve the online user with the $token - if none exists throw an error
    }

    public function toJson(){
        return json_encode(get_object_vars($this));
    }

    private function isObjectValid()
    {
        // TODO replace with Actual Validation
        if (empty($this->token)) {
            $isValid =  false;
        }else{
            $isValid = true;
        }
        return $isValid;
    }
}