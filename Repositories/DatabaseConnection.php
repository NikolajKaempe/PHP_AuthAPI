<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 24-10-2017
 * Time: 13:55
 */

class DatabaseConnection{

    private static $db = null;

    public static function getConnection(){

        if (!isset(static::$db)) {
            // TODO CHANGE TO AN APPROPRIATE DB-USER AND NOT ROOT!!
            static::$db = new PDO('mysql:host:localhost',"root","");
        }
        return static::$db;
    }
}

