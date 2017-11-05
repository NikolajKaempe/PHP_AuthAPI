<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 19-10-2017
 * Time: 15:07
 */

/**
 * Class Validation
 * The instance containing all the validation methods.
 * @author Nikolaj Kæmpe.
 */
class Validation{

    /**
     * @var string, a static value used when hashing password for each user.
     * @author Nikolaj Kæmpe.
     */
    private $pepper = "IfSaltIsNotEnough,MakeSureToUseSomePepperAsWell!!";

    /**
     * This method is used to hash a password, with a unique salt and the static pepper, using BCrypt
     * @param $password, is a string representing the password supplied by the user.
     * @param $salt, is a string, representing a unique value generated for each user.
     * @return string, representing the newly created hashedPassword.
     * @author Nikolaj Kæmpe.
     */
    public function hashPassword($password, $salt){
        $cost = [
            'cost' => 6
        ];
        return password_hash($password.$salt.$this->pepper, PASSWORD_BCRYPT, $cost);
    }

    /**
     * Verifies that the supplied password, when hashed with the correct salt and pepper,
     * matches the hashedPassword supplied and returns true if correct otherwise false is returned.
     * @param $password, a string representing the password supplied by the User.
     * @param $hashedPassword, a string representing the hashedPassword stored on the database.
     * @param $salt, a string representing a unique value for each user, used in hashing the password.
     * @return bool, representing whether the supplied password, when hashed with the correct salt and pepper,
     * matches the hashedPassword.
     * @author Nikolaj Kæmpe.
     */
    public function comparePassword($password, $hashedPassword,$salt){
        $result = password_verify($password.$salt.$this->pepper,$hashedPassword);
        return $result;
    }

    /**
     * Verifies that the input parameter is between 6-32 characters and only contains
     * letters and numbers - if it does not true is returned otherwise false is returned.
     * @param $username, a string representing the username supplied by the user.
     * @return bool, representing whether the supplied username is valid or not.
     * @author Nikolaj Kæmpe.
     */
    public  function isValidUsername($username){
        return ($this->isStringLengthBetween(4,32,$username) &&
                $this->containsLettersAndNumbersOnly($username)
                );
    }

    /**
     * Verifies that the input parameter is between 8-32 characters and contains
     * at least 1 Uppercase letter, 1 Lowercase letter and a number.
     * @param $password, a string representing the password supplied by the user.
     * @return bool, representing whether the supplied password is valid or not.
     * @author Nikolaj Kæmpe.
     */
    public function isValidPassword($password){
        return ($this->isStringLengthBetween(6,32,$password) &&
            $this->containsNumeric($password) &&
            $this->containsLowerCase($password) &&
            $this->containsUpperCase($password));
    }

    /**
     * Verifies that the input parameter is between 59-60 characters.
     * @param $hashedPassword, a string representing the hashed version of the users password
     * @return bool, representing whether the supplied hashedPassword is valid or not.
     * @author Nikolaj Kæmpe.
     */
    public function isValidHashedPassword($hashedPassword){
        return ($this->isStringLengthBetween(59,60,$hashedPassword));
    }

    /**
     * Verifies that the input parameter is between 12-13 characters.
     * @param $salt, a string representing a unique value for the specific user.
     * @return bool, representing whether the supplied salt is valid or not.
     * @author Nikolaj Kæmpe.
     */
    public function isValidSalt($salt){

        return ($this->isStringLengthBetween(12,13,$salt));
        // TODO ADD ADDITIONAL CHECKS?
    }

    /**
     * Verifies that the input parameter is between 7-20 characters.
     * @param $ipAddress, a string representing the IPAddress of the HTTP-request
     * @return bool, representing whether the supplied IP address is valid or not.
     * @author Nikolaj Kæmpe.
     */
    public function isValidIP($ipAddress){
        return true;

        //return ($this->isStringLengthBetween(7,20,$ipAddress));
        // TODO ADD ADDITIONAL CHECKS?
    }

    /**
     * Verifies that the input parameter is between 127-128 characters.
     * @param $token, a string representing a unique value for a User that is logged in.
     * @return bool, representing whether the supplied token is valid or not.
     * @author Nikolaj Kæmpe.
     */
    public function isValidToken($token){
        return ($this->isStringLengthBetween(127,128,$token));
        // TODO ADD ADDITIONAL CHECKS?
    }

    /**
     * A private method to verifies that the length of a string is between the 'max' & 'min' values.
     * @param $min, an integer representing the minimum length of the string.
     * @param $max, an integer representing the maximum length of the string.
     * @param $input, a string representing the input that is to be tested.
     * @return bool, representing whether the length of the input is between the min and max values.
     */
    private function isStringLengthBetween($min, $max, $input){
        return !($min > $max || strlen($input) < $min || strlen($input) > $max);
    }

    /**
     * A private method to verify that the input only contains characters from a-Z, A-Z & 0-9.
     * @param $input, a string representing the input that is to be tested.
     * @return bool, representing whether the supplied input is valid or not.
     */
    private function containsLettersAndNumbersOnly($input){
        return preg_match("/^[a-zA-Z0-9]+$/",$input)? true: false;
    }

    /**
     * A private method to verify that the input contains at least one numeric character.
     * @param $input, a string representing the input that is to be tested.
     * @return bool, representing whether the supplied input is valid or not.
     */
    private function containsNumeric($input){
        return preg_match('/[0-9]+/',$input)? true : false;
    }

    /**
     * A private method to verify that the input contains at least one Uppercase character.
     * @param $input, a string representing the input that is to be tested.
     * @return bool, representing whether the supplied input is valid or not.
     */
    private function containsUpperCase($input){
        return preg_match('/[A-Z]+/',$input)? true: false;
    }

    /**
     * A private method to verify that the input contains at least one Lowercase character.
     * @param $input, a string representing the input that is to be tested.
     * @return bool, representing whether the supplied input is valid or not.
     */
    public function containsLowerCase($input){
        return preg_match('/[a-z]+/',$input)? true : false;
    }
}