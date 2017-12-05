<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 05-12-2017
 * Time: 09:37
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/DatabaseConnection.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');

class FriendRepository{

    public static function getAll($token){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.friend_get_all(:auth_token)");
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

    public static function getOnline($token){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.friend_get_online(:auth_token)");
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

    public static function getPendingInvites($token){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.friend_invite_view_pending(:auth_token)");
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

    public static function invite($token,$userId){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.friend_invite(:auth_token ,:user_id)");
            $stmt->bindParam('auth_token', $token, PDO::PARAM_STR );
            $stmt->bindParam('user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e->getCode());
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
    }

    public static function processInvite($token,$userId,$accept){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.friend_invite_process(:auth_token ,:user_id, :accept)");
            $stmt->bindParam('auth_token', $token, PDO::PARAM_STR );
            $stmt->bindParam('user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam('accept', $accept, PDO::PARAM_BOOL);
            $stmt->execute();
        }
        catch (PDOException $e){
            var_dump($e);

            if ($e->getCode() == 45000) {
                ResponseService::ResponseBadRequest($e->errorInfo[2]);
            }elseif ($e->getCode() == "HY000") {
                ResponseService::ResponseBadRequest("Invalid Request-Body");
            }else{
                ResponseService::ResponseInternalError();
            }
        }
        catch (Exception $e){
            var_dump($e);
            ResponseService::ResponseInternalError();
        }
    }



}