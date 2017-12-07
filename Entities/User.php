<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 18-10-2017
 * Time: 12:42
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/AuthProcedures.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Logic/Validation.php');

/**
 * Class User
 * The user instance with Authentication functionality.
 * @author Nikolaj Kæmpe
 */
class User{

    /**
     * @var string, the requested username.
     * @author Nikolaj Kæmpe.
     */
    private $username;

    /**
     * @var string, the password hashed with a unique salt and internal pepper using BCrypt.
     * @author Nikolaj Kæmpe.
     */
    private $hashedPassword;

    /**
     * @var string, the requested password.
     * @author Nikolaj Kæmpe.
     */
    private $password;

    /**
     * @var string, a unique value for each user, which is used to hash the password.
     * @author Nikolaj Kæmpe.
     */
    private $salt;

    /**
     * The primary constructor of the User.
     * If the Object is not valid after parsing the input an error is returned.
     * Only the 'username' and 'password' will be used, the other fields will be set internally by other methods.
     * @param $json, is a Map, typically the body of a HTTP request.
     * @throws Exception if the object is not valid after parsing the input.
     * @author Nikolaj Kæmpe.
     */
    public function constructFromHashMap($json)
    {
        $data = json_decode($json, true);
        if (empty($data)) ResponseService::ResponseBadRequest("Invalid Request-Body");
        foreach ($data AS $key => $value) $this->{$key} = $value;

        $this->failOnInvalidModel();
    }

    /**
     * Returns the hashedPassword of the User.
     * @return string representing the hashedPassword.
     * @author Nikolaj Kæmpe.
     */
    public function getHashedPassword()
    {
        return $this->hashedPassword;
    }

    /**
     * Returns the salt of the User.
     * @return string representing the salt.
     * @author Nikolaj Kæmpe.
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Parses a new UUID-value to the 'Salt' field.
     * Parses a new value to the 'hashedPassword' field by supplying the
     * new 'Salt' and internal 'Pepper' values to BCrypt along with the inputted password.
     * @author Nikolaj Kæmpe.
     */
    public function saltAndHashPassword(){
        $validation = new Validation();
        $this->salt = uniqid();
        $this->hashedPassword = $validation->hashPassword($this->password,$this->salt);
    }

    /**
     * Tries to create a User on the database with the username, salt & hashedPassword.
     * If a valid username and password is supplied a new User is created on the database.
     * Then a new AuthToken is created on the database, by calling the method 'tryLogin', and returned.
     * The followings events will result in an error with an appropriate error message.
     * 1. The UserObject or IP is invalid.
     * 2. The Username is already in use.
     * 3. All Events that can occur in 'tryLogin'.
     * @param $ip, is a string representing the IP of the HTTP request.
     * @return AuthToken, the authToken created for the User and IP.
     * @throws Exception if some of the mentioned events occur.
     * @author Nikolaj Kæmpe.
     */
    public function createUser($ip){
        $this->failOnInvalidModel();

        $validation = new Validation();
        if (!$validation->isValidIP($ip)){
            ResponseService::ResponseBadRequest("Invalid IP-Address");
        }

        $this->saltAndHashPassword();
        $procedures = new AuthProcedures();
        $procedures->createUser($this->username,$this->hashedPassword,$this->salt);
        $token = $this->tryLogin($ip);

        return $token;
    }

    /**
     * Tries to login the User with the supplied IP.
     * If the user have supplied the correct username and password
     * a new AuthToken is created on the database and returned.
     * The followings events will result in an error with an appropriate error message.
     * 1. The UserObject or IP is invalid.
     * 2. The User is currently banned from the supplied IP.
     * 3. The User dos'ent exists.
     * 4. The supplied password, when hashed appropriately, dos'ent match the hashedPassword
     * on the database for the supplied Username.
     * @param $ipAddress, is a string representing the IP of the HTTP request.
     * @return AuthToken, the authToken created for the User and IP.
     * @throws Exception if some of the mentioned events occur.
     * @author Nikolaj Kæmpe.
     */
    public function tryLogin($ipAddress){
        $this->failOnInvalidModel();

        $procedures = new AuthProcedures();
        $validation = new Validation();

        if (!$validation->isValidIP($ipAddress)){
            ResponseService::ResponseBadRequest("Invalid IP-Address");
        }

        $this->salt = $procedures->fetchSalt($this->username);
        $this->hashedPassword = $validation->hashPassword($this->password,$this->salt);
        $token = $procedures->loginUser($this->username,$ipAddress,$this->hashedPassword);

        return $token;
    }

    public static function getPicture($token){
        return AuthProcedures::getProfilePicture($token);
    }

    public static function getPictureFromId($token,$user_id){
        echo "Get from ID";
        return AuthProcedures::getProfilePictureFromId($token,$user_id);

    }

    public static function updatePicture($token,$picture){
        AuthProcedures::setProfilePicture($token,$picture);
    }

    /**
     * Returns a string representing a JSon-object constructed from the UserObject's fields.
     * @return string representing a JSon-object constructed from the UserObject's fields.
     * @author Nikolaj Kæmpe.
     */
    public function toJson(){
        $this->password = null;
        return json_encode(get_object_vars($this));
    }

    /**
     * Uses the Validation class to verify the 'username' & 'password' fields.
     * If either of them is invalid, false is returned. Otherwise true is returned.
     * @return bool representing whether the object is valid or not.
     * @author Nikolaj Kæmpe.
     */
    private function failOnInvalidModel(){
        $validation = new Validation();

        if (!$validation->isValidUsername($this->username)
            || !$validation->isValidPassword($this->password) ) {
            ResponseService::ResponseBadRequest("Invalid Request-Body");
        }
    }

}