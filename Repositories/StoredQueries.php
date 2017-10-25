<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 19-10-2017
 * Time: 15:40
 */

include_once('DatabaseConnection.php');

class StoredQueries{

    public function fetchDatabaseUser($username){
        $user = new ApplicationUser();
        $database = new DatabaseConnection();
        $connection = $database->getConnection();

        $sql_query='Select HashedPassword,Salt from websecurity.users where Username=:username';
        $sth=$connection->prepare($sql_query);
        $sth->bindParam(":username", $username);
        $sth->execute();

        $result=$sth->fetchAll();

        if(!empty($result)){
            foreach (@$result as $row){
                $user->constructPasswordUser($row['HashedPassword'],$row['Salt']);
            }
        }
        return $user;
    }
}

