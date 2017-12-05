<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 05-12-2017
 * Time: 12:02
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/DatabaseConnection.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');

class PreferenceRepository{

    public static function getUserPreferences($token){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.preference_user_get_from_user(:auth_token)");
            $stmt->bindParam('auth_token', $token, PDO::PARAM_STR );
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        }
        catch (PDOException $e){
            if ($e->getCode() == 45000) {
                ResponseService::ResponseBadRequest($e->errorInfo[2]);
            }
            else{
                ResponseService::ResponseInternalError();
            }
        }
        catch (Exception $e){
            ResponseService::ResponseInternalError();
        }
        return $result;
    }

    public static function updateUserPreference($token,$typeId,$levelId){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.preference_user_update(:auth_token, :type_id, :level_id)");
            $stmt->bindParam('auth_token', $token, PDO::PARAM_STR );
            $stmt->bindParam('type_id', $typeId, PDO::PARAM_INT );
            $stmt->bindParam('level_id', $levelId, PDO::PARAM_INT );
            $stmt->execute();

        }
        catch (PDOException $e){
            if ($e->getCode() == 45000) {
                ResponseService::ResponseBadRequest($e->errorInfo[2]);
            }
            else{
                ResponseService::ResponseInternalError();
            }
        }
        catch (Exception $e){
            ResponseService::ResponseInternalError();
        }
    }

    public static function getUserPreferenceTypes($token){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.preference_user_get_types(:auth_token)");
            $stmt->bindParam('auth_token', $token, PDO::PARAM_STR );
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        }
        catch (PDOException $e){
            var_dump($e);
            if ($e->getCode() == 45000) {
                ResponseService::ResponseBadRequest($e->errorInfo[2]);
            }
            else{
                ResponseService::ResponseInternalError();
            }
        }
        catch (Exception $e){
            var_dump($e);
            ResponseService::ResponseInternalError();
        }
        return $result;
    }
}