<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 19-10-2017
 * Time: 15:40
 */

class StoredQueries{

    public function fetchDatabaseUser($username){
        $user = new ApplicationUser();
        //TODO call fetchDatabaseUser (hashPsw, Salt)
        $user->constructPasswordUser("asdadads","asdasdasdads");
        return $user;
    }
}