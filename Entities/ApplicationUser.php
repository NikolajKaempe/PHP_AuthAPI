<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 18-10-2017
 * Time: 12:42
 */

include_once('/../Repositories/StoredProcedures.php');
include_once('/../Repositories/StoredQueries.php');

class ApplicationUser{

private $username, $password, $hashedPassword;
private $salt, $pepper = "IfSaltIsNotEnough,MakeSureToUseSomePepperAsWell!!";


    public function constructFromHashMap($json)
    {
        $data = json_decode($json, true);
        if (empty($data)) throw new Exception("Object Not Valid");
        foreach ($data AS $key => $value) $this->{$key} = $value;

        if (!$this->validateAppUser()) throw new Exception("Object Not Valid");
    }

    public function constructPasswordUser($hashedPassword,$salt){
        $this->hashedPassword = $hashedPassword;
        $this->salt = $salt;
        if (!$this->validatePswUser()) throw new Exception("Object Not Valid");
    }



    public function getHashedPassword()
    {
        return $this->hashedPassword;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function createUser($ip){
        if (empty($this->username) || empty($this->password) ) throw new Exception("Object Not Valid");
        $procedures = new StoredProcedures();

        $this->salt = uniqid();
        $this->hashedPassword = $this->hashPassword($this->password,$this->salt);

        try{
            $procedures->createUser();
        }
        catch (Exception $e){
            throw $e;
        }

        $token = $this->tryLogin($ip);
        return $token;
    }

    public function tryLogin($ipAddress){

        $token = new AuthToken();
        $procedures = new StoredProcedures();
        $queries = new StoredQueries();

        try{
            if ($procedures->isUserBanned() == true){
                throw new Exception("User ".$this->username." is currently banned.");
            }
            if ($procedures->doUserExists() == false) {
                $procedures->addFailedLoginAttempt();
                throw new Exception("Incorrect username or password");
            }

            $databaseUser = $queries->fetchDatabaseUser($this->username);
            if ($this->comparePassword($this->password,$databaseUser->hashedPassword,$databaseUser->salt) == false){
                $procedures->addFailedLoginAttempt();
                throw new Exception("Incorrect username or password");
            }else{
                $procedures->removeFailedLoginAttempt();
                $token = $procedures->loginUser();
            }
        }catch (Exception $e){
            throw $e;
        }

        return $token;
    }

    public function toJson(){
        return json_encode(get_object_vars($this));
    }

    private function validateAppUser(){
        // TODO replace with Actual Validation
        if (empty($this->username) || empty($this->password) ) {
            $isValid =  false;
        }else{
            $isValid = true;
        }

        return $isValid;
    }

    private function validatePswUser(){
        // TODO replace with Actual Validation
        if (empty($this->hashedPassword) || empty($this->salt) ) {
            $isValid =  false;
        }else{
            $isValid = true;
        }

        return $isValid;
    }

    private function hashPassword($password, $salt){
        $cost = [
            'cost' => 6
        ];
        return password_hash($password.$salt.$this->pepper, PASSWORD_BCRYPT, $cost);
    }

    public function comparePassword($password, $hashedPassword,$salt){
        return password_verify($password.$salt.$this->pepper,$hashedPassword);
    }

}