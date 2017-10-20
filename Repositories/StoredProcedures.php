<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 19-10-2017
 * Time: 15:39
 */

class StoredProcedures{

    public function isUserBanned(){
        $exists = false;
         // TODO Call is_user_banned(username,ipaddress);
        //"{call is_user_banned(?,?,?)}\",username,ipAddress)";
        return $exists;
    }

    public function doUserExists(){
        $exists = true;
        // TODO "{? = call do_user_exist(?)}\",username)"

        return $exists;
    }

    public function loginUser($username,$ipAddress){
        $authToken = new AuthToken();

        return $authToken;
    }

    public function createUser(){
        // TODO Call procedure "createUser(username, hashedPassword, Salt)" catch duplicate Error.
        //StoredProcedures.callStoredProcedure("{call create_user(?,?,?)}",username,hashedPassword,salt);
    }

    public function addFailedLoginAttempt(){

        //TODO "{call add_failed_login_attempt(?,?)}\",username,ipAddress)";
    }

    public function removeFailedLoginAttempt(){

        //TODO "{call remove_failed_login_attempt(?,?)}\",username,ipAddress)";
    }
}