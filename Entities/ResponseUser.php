<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 24-10-2017
 * Time: 13:09
 */

/**
 * Class ResponseUser
 * The user instance with that should be returned to an API.
 * @author Nikolaj Kæmpe
 */
class ResponseUser{

    /**
     * @var string, a unique value for each User.
     * @author Nikolaj Kæmpe.
     */
    private $username;

    /**
     * @var string, a value representing the role of the User.
     * @author Nikolaj Kæmpe.
     */
    private $role;

    /**
     * This constructor should be used when instantiating the object from database fields.
     * @param $username, is a string representing the Username on the database.
     * @param $role, a string representing the role of the User.
     * @author Nikolaj Kæmpe.
     */
    public function construct($username, $role){
        $this->username = $username;
        $this->role = $role;
    }

    /**
     * Returns the username of the User.
     * @return string representing the username.
     * @author Nikolaj Kæmpe.
     */
    public function getUsername(){
        return $this->username;
    }

    /**
     * Returns a string representing a JSon-object constructed from the ResponseUserObject's fields.
     * @return string representing a JSon-object constructed from the ResponseUserObject's fields.
     * @author Nikolaj Kæmpe.
     */
    public function toJson(){
        return json_encode(get_object_vars($this));
    }
}