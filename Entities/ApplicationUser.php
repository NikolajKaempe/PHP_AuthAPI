<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 18-10-2017
 * Time: 12:42
 */

include_once('/../Repositories/StoredProcedures.php');
include_once('/../Repositories/StoredQueries.php');
include_once('/../Logic/Validation.php');
include_once('ResponseUser.php');


class ApplicationUser{

private $username, $password, $hashedPassword;
private $salt;

    public function constructFromHashMap($json)
    {
        $data = json_decode($json, true);
        if (empty($data)) throw new Exception("Object Not Valid");
        foreach ($data AS $key => $value) $this->{$key} = $value;
        if (!$this->isValidAppUser()) throw new Exception("Object Not Valid");
    }

    public function constructPasswordUser($hashedPassword,$salt){
        $this->hashedPassword = $hashedPassword;
        $this->salt = $salt;
        if (!$this->isValidPswUser()) throw new Exception("Object Not Valid");
    }

    public function getUsername(){ // TODO Delete
        return $this->username;
    }

    public function getHashedPassword()
    {
        return $this->hashedPassword;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function saltAndHashPassword(){
        $validation = new Validation();
        $this->salt = uniqid();
        $this->hashedPassword = $validation->hashPassword($this->password,$this->salt);
    }

    public function createUser($ip){
        if (!$this->isValidAppUser()) throw new Exception("Object Not Valid");
        $procedures = new StoredProcedures();

        $this->saltAndHashPassword();
        try{
            $procedures->createUser($this->username,$this->hashedPassword,$this->salt);
        }
        catch (Exception $e){
            throw $e;
        }

        $token = $this->tryLogin($ip);
        return $token;
    }

    public function tryLogin($ipAddress){

        $procedures = new StoredProcedures();
        $queries = new StoredQueries();
        $validation = new Validation();

        try{

            if (!$validation->isValidIP($ipAddress)){
                throw new Exception("Invalid IP-Address");
            }

            if ($procedures->isUserBanned($this->username,$ipAddress) == true){
                throw new Exception("User ".$this->username." is currently banned.");
            }
            if ($procedures->doUserExists($this->username) == false) {
                $procedures->addFailedLoginAttempt($this->username,$ipAddress);
                throw new Exception("Incorrect username or password");
            }

            $databaseUser = $queries->fetchDatabaseUser($this->username);

            if ($validation->comparePassword($this->password,$databaseUser->hashedPassword,$databaseUser->salt) === false){
                $procedures->addFailedLoginAttempt($this->username,$ipAddress);
                throw new Exception("Incorrect username or password");
            }else{
                $procedures->removeFailedLoginAttempt($this->username,$ipAddress);
                $token = $procedures->loginUser($this->username,$ipAddress);
            }
        }catch (Exception $e){
            throw $e;
        }
        return $token;
    }

    public function toJson(){
        return json_encode(get_object_vars($this));
    }

    private function isValidAppUser(){
        $validation = new Validation();

        if (!$validation->isValidUsername($this->username)
            || !$validation->isValidPassword($this->password) ) {
            $isValid =  false;
        }else{
            $isValid = true;
        }

        return $isValid;
    }

    private function isValidPswUser(){
        $validation = new Validation();
        if (!$validation->isValidHashedPassword($this->hashedPassword)
            || !$validation->isValidSalt($this->salt) ) {
            $isValid =  false;
        }else{
            $isValid = true;
        }
        return $isValid;
    }

}