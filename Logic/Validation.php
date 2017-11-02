<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 19-10-2017
 * Time: 15:07
 */

class Validation{

    private $pepper = "IfSaltIsNotEnough,MakeSureToUseSomePepperAsWell!!";

    public function hashPassword($password, $salt){
        $cost = [
            'cost' => 6
        ];
        return password_hash($password.$salt.$this->pepper, PASSWORD_BCRYPT, $cost);
    }

    public function comparePassword($password, $hashedPassword,$salt){
        $result = password_verify($password.$salt.$this->pepper,$hashedPassword);
        return $result;
    }

    public  function isValidUsername($username){
        return ($this->isStringLengthBetween(6,32,$username) &&
                $this->containsOnlyStandardChars($username)
                );
    }

    public function isValidPassword($password){
        return ($this->isStringLengthBetween(8,32,$password) &&
            $this->containsNummeric($password) &&
            $this->containsLowerCase($password) &&
            $this->containsUpperCase($password));
    }

    public function isValidHashedPassword($hashedPassword){
        return ($this->isStringLengthBetween(59,60,$hashedPassword));
    }

    public function isValidSalt($salt){

        return ($this->isStringLengthBetween(12,13,$salt));
        
        // lenght = 13
        // A-Za-z0-9

        $isValid = true ;

        return $isValid;
    }

    public function isValidIP($ipaddress){
        $isValid = true ;

        return $isValid;
    }


    public function isValidToken($token){
        // lenght 13
        // A-Za-z0-9

        $isValid = true ;

        return $isValid;
    }

    private function isStringLengthBetween($min, $max, $input){
        return !($min > $max || strlen($input) < $min || strlen($input) > $max);
    }

    private function containsOnlyStandardChars($input){
        return preg_match("/^[a-zA-Z0-9]+$/",$input);
    }

    private function containsNummeric($input){
        return preg_match('/[0-9]+/',$input);
    }

    private function containsUpperCase($input){
        return preg_match('/[A-Z]+/',$input);
    }

    private function containsLowerCase($input){
        return preg_match('/[a-z]+/',$input);
    }
}