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
            //static::$db = new PDO('mysql:host:80.255.6.114',"test","iHateDoingLongPasswords");

            static::$db = new PDO('mysql:host:localhost',"api","5VNM3gZ3uzVLdqsqv2A0");
            static::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return static::$db;
    }
}

