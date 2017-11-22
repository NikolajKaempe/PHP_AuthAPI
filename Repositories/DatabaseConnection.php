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
            static::$db = new PDO('mysql:host:localhost',"api","5VNM3gZ3uzVLdqsqv2A0");
            static::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return static::$db;
    }
}

