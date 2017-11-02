<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 17-10-2017
 * Time: 12:33
 */

include_once('/../Repositories/StoredQueries.php');
include_once('/../Logic/Validation.php');

/**
 * Class AuthToken
 * An object that contains a unique token & a time it expires
 * and a method to retrieve the ResponseUser associated to specific token.
 */
class AuthToken{

    /**
     * @var string, a unique value created on the database.
     * @author Nikolaj Kæmpe.
     */
    private $token;

    /**
     * @var string, a 'timeStamp' representing the time the token expires.
     * @author Nikolaj Kæmpe.
     */
    private $timeAlive;

    /**
     * This constructor should be used when instantiating the object from database fields.
     * @param $token, is a string representing a unique value associated with the User that is logged in.
     * @param $timeAlive, a timeStamp representing the time the token expires.
     * @author Nikolaj Kæmpe.
     */
    public function construct($token,$timeAlive){
        $this->token = $token;
        $this->timeAlive= $timeAlive;
    }

    /**
     * This constructor should be used when instantiating the object from a HTTP request body
     * If the Object is not valid after parsing the input an error is returned.
     * Only the 'token' will be used, the other fields will be set internally by other methods.
     * @param $json, is a Map, typically the body of a HTTP request.
     * @throws Exception if the object is not valid after parsing the input.
     * @author Nikolaj Kæmpe.
     */
    public function constructFromHashMap($json)
    {
        $data = json_decode($json, true);

        if (empty($data)) throw new Exception("Object Not Valid");
        foreach ($data AS $key => $value) $this->{$key} = $value;
        if (!$this->isObjectValid()) throw new Exception("Object Not Valid");
    }

    /**
     * Returns a string representing the token of the object.
     * @return string representing the token.
     * @author Nikolaj Kæmpe.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Returns a string representing a timestamp of when the token becomes invalid.
     * @return string representing the time the token becomes invalid.
     * @author Nikolaj Kæmpe.
     */
    public function getTimeAlive()
    {
        return $this->timeAlive;
    }

    /**
     * Uses the method called 'fetchOnlineUser' in the StoredQueries class
     * to create a ResponseUser Object from the supplied token.
     * If the token dos'ent matches a online user on the database, an empty ResponseUser object is returned.
     * @return ResponseUser, created with User data that matches the supplied token.
     * @author Nikolaj Kæmpe.
     */
    public function fetchUser(){
        $queries = new StoredQueries();
        $User = $queries->fetchOnlineUser($this->token);
        return $User;
    }

    /**
     * Returns a string representing a JSon-object constructed from the AuthTokenObject's fields.
     * @return string representing a JSon-object constructed from the AuthTokenObject's fields.
     * @author Nikolaj Kæmpe.
     */
    public function toJson(){
        return json_encode(get_object_vars($this));
    }

    /**
     * Uses the Validation class to verify the 'token' field.
     * If invalid false is returned. Otherwise true is returned.
     * @return bool representing whether the object is valid or not.
     * @author Nikolaj Kæmpe.
     */
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