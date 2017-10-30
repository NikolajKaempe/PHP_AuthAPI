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

        try{
            $connection = DatabaseConnection::getConnection();

            $sql_query ='Select HashedPassword,Salt from websecurity.users where Username=:username';
            $sth = $connection->prepare($sql_query);
            $sth->bindParam(":username", $username);
            $sth->execute();

            $result=$sth->fetchAll();

            if(!empty($result)){
                foreach (@$result as $row){
                    $user->constructPasswordUser($row['HashedPassword'],$row['Salt']);
                }
            }
        }catch (Exception $e){
            $user = new ApplicationUser();
        }

        return $user;
    }

    public function fetchOnlineUser($token){
        $User = new ResponseUser();

        try{
            $connection = DatabaseConnection::getConnection();

            $sql_query ='Select Username from websecurity.online_users Where Authtoken = :authtoken And AuthLifetime >= CURRENT_TIMESTAMP';

            $sth = $connection->prepare($sql_query);
            $sth->bindParam(":authtoken", $token );
            $sth->execute();

            $result=$sth->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach (@$result as $row){
                    $User->construct($row['Username'],"User");
                }
            }
        }catch (Exception $e){
            $User = new ApplicationUser();
        }

        return $User;
    }
}



