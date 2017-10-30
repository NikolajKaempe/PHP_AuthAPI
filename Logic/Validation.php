<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 19-10-2017
 * Time: 15:07
 */

class Validation{

    private $pepper = "IfSaltIsNotEnough,MakeSureToUseSomePepperAsWell!!";

    public function getPepper(){
        return $this->pepper;
    }

    public function hashPassword($password, $salt){
        $cost = [
            'cost' => 6
        ];
        return password_hash($password.$salt.$this->pepper, PASSWORD_BCRYPT, $cost);
    }

    public function comparePassword($password, $hashedPassword,$salt){
        return password_verify($password.$salt.$this->pepper,$hashedPassword);
    }

    public  function isValidUsername($username){
        // A-Za-z0-9
        // lenght 6-32
        $isValid = true ;

        return $isValid;
    }

    public function isValidPassword($password){
        // A-Z + a-z + 0-9
        // lenght 8-64
        $isValid = true ;

        return $isValid;
    }

    public function isValidHashedPassword($hashedPassword){

        // lenght = 60
        // All chars allowed??
        $isValid = true ;

        return $isValid;
    }

    public function isValidSalt($salt){
        // lenght = 13
        // A-Za-z0-9

        $isValid = true ;

        return $isValid;
    }

    public function isValidIP($ipaddress){

    }


    public function isValidToken($token){
        // lenght 13
        // A-Za-z0-9

        $isValid = true ;

        return $isValid;
    }



}