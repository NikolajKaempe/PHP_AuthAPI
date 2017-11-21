<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 17-10-2017
 * Time: 12:33
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Logic/Validation.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');


/**
 * Class AuthToken
 * An object that contains a unique token & a time it expires
 * @author Nikolaj Kæmpe.
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
     * This constructor is used when instantiating the object from the 'RequestServer'.
     * @param $token, is a string representing a unique value associated with the User that is logged in.
     * @author Nikolaj Kæmpe.
     */
    public function __construct($token){
        $this->token = $token;
        $this->failOnInvalidModel();
    }


    /**
     * This constructor should be used when instantiating the object from database fields.
     * @param $token, is a string representing a unique value associated with the User that is logged in.
     * @param $timeAlive, a timeStamp representing the time the token expires.
     * @author Nikolaj Kæmpe.
     */
    public function construct($token,$timeAlive){
        $this->token = $token;
        $this->timeAlive= $timeAlive;
        $this->failOnInvalidModel();
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
     * Uses the Validation class to verify the 'token' field.
     * If invalid false is returned. Otherwise true is returned.
     * @return bool representing whether the object is valid or not.
     * @author Nikolaj Kæmpe.
     */
    private function failOnInvalidModel()
    {
        $validation = new Validation();

        if (!$validation->isValidToken($this->token)) {
            ResponseService::ResponseNotAuthorized();
        }
    }

    public function toJson(){
        return json_encode(get_object_vars($this));
    }
}