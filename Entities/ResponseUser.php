<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 24-10-2017
 * Time: 13:09
 */

class ResponseUser{

    private $username, $role;

    public function __construct($username, $role){
        $this->username = $username;
        $this->role = $role;
    }

    public function toJson(){
        return json_encode(get_object_vars($this));
    }
}