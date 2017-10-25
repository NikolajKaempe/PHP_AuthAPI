<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 24-10-2017
 * Time: 13:55
 */

class DatabaseConnection{

    private $db;

    public function getConnection(){
        if (!empty($this->db)) return $this->db;
        try{
            $this->db = new PDO('mysql:host:localhost',"root","");
        }catch (Exception $e){
            throw $e;
        }
        // TODO CHANGE TO AN APPROPRIATE DB-USER AND NOT ROOT!!
        return $this->db;
    }
}

