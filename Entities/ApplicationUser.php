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

/**
 * Class ApplicationUser
 * The user instance with Authentication functionality.
 * @author Nikolaj Kæmpe
 */
class ApplicationUser{

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
     * The primary constructor of the ApplicationUser.
     * If the Object is not valid after parsing the input an error is returned.
     * Only the 'username' and 'password' will be used, the other fields will be set internally by other methods.
     * @param $json, is a Map, typically the body of a HTTP request.
     * @throws Exception if the object is not valid after parsing the input.
     * @author Nikolaj Kæmpe.
     */
    public function constructFromHashMap($json)
    {
        $data = json_decode($json, true);
        if (empty($data)) throw new Exception("Object Not Valid");
        foreach ($data AS $key => $value) $this->{$key} = $value;
        if (!$this->isValidAppUser()) throw new Exception("Object Not Valid");
    }

    /**
     * This constructor should be used to create a UserObject
     * with only the hashedPassword and Salt.
     * This Object is used to verify that a password sent by a user,
     * matches the hashedPassword saved on the database, when the pepper and saved salt is added.
     * @param $hashedPassword, is a string representing the HashedPassword saved on the database.
     * @param $salt, is a string representing the Salt saved on the database.
     * @throws Exception if the object is not valid after parsing the input parameters.
     * @author Nikolaj Kæmpe.
     */
    public function constructPasswordUser($hashedPassword,$salt){
        $this->hashedPassword = $hashedPassword;
        $this->salt = $salt;
        if (!$this->isValidPswUser()) throw new Exception("Object Not Valid");
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
        if (!$this->isValidAppUser()) throw new Exception("Object Not Valid");
        $procedures = new StoredProcedures();
        $validation = new Validation();

        if (!$validation->isValidIP($ip)){
            throw new Exception("Invalid IP-Address");
        }

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
        if (!$this->isValidAppUser()) throw new Exception("Object Not Valid");
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

    /**
     * Uses the Validation class to verify the 'hashedPassword' & 'salt' fields.
     * If either of them is invalid, false is returned. Otherwise true is returned.
     * @return bool representing whether the object is valid or not.
     * @author Nikolaj Kæmpe.
     */
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