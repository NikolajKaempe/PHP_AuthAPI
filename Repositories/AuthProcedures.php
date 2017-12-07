<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 5-11-2017
 * Time: 23:39
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/DatabaseConnection.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/AuthToken.php');


/**
 * Class AuthProcedures contains methods for all stored procedures
 * used for User authentication and login.
 */
class AuthProcedures{

    /**
     * This method fetches the hashedPassword and salt for a specific user.
     * If no user is found, an empty user is returned.
     * @param $username, a string representing the username supplied by the user.
     * @return User, containing the hashedPassword and salt for the requested User.
     */
    public function fetchSalt($username){
        $salt = "dummy";

        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("call security.auth_fetch_salt(:username, @salt)");

            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("Select @salt")->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach ($result as $row){

                    $salt = $row['@salt'];
                }
            }

        } catch (PDOException $e){
            ResponseService::ResponseNotAuthorized();

        }
        catch (Exception $e){
            ResponseService::ResponseNotAuthorized();
        }
        return $salt;
    }

    public function loginUser($username,$ipAddress,$hashedPassword){

        try{
            $connection = $this->getDatabaseConnection();

            $stmt = $connection->prepare("CALL security.auth_login_user(:username,:ip_address, :hashed_password,@token,@timeAlive)");
            $stmt->bindParam('username', $username, PDO::PARAM_STR );
            $stmt->bindParam('ip_address', $ipAddress, PDO::PARAM_STR);
            $stmt->bindParam('hashed_password', $hashedPassword, PDO::PARAM_STR);
            $stmt->execute();

            $stmt->closeCursor();
            $result = $connection->query("Select @token, @timeAlive")->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach (@$result as $row){
                    $authToken = new AuthToken($row['@token']);
                    $authToken->construct($row['@token'],$row['@timeAlive']);
                }
            }else{
                ResponseService::ResponseNotAuthorized();
            }
        }
        catch (PDOException $e){
            if ($e->getCode() == 45000) {
                ResponseService::ResponseBadRequest($e->errorInfo[2]);
            }else{
                ResponseService::ResponseInternalError();
            }
        }
        catch (Exception $e){
            ResponseService::ResponseInternalError();
        }
        return $authToken;
    }

    public function createUser($username, $hashedPassword, $salt){
        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("Call security.auth_create_user(:username, :hashed_password, :salt)");
            $stmt->bindParam('username', $username);
            $stmt->bindParam('hashed_password', $hashedPassword);
            $stmt->bindParam('salt', $salt);
            $stmt->execute();
        }
        catch (PDOException $e){
            if ($e->getCode() == 23000){
                ResponseService::ResponseBadRequest("Username already in use");
            }else{
                ResponseService::ResponseInternalError();
            }

        }catch (Exception $e){
            ResponseService::ResponseInternalError();
        }
    }

    public static function getProfilePicture($token){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("call security.pictures_get_from_token(:auth_token)");
            $stmt->bindParam(":auth_token", $token);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        } catch (PDOException $e){
            ResponseService::ResponseNotAuthorized();

        }
        catch (Exception $e){
            ResponseService::ResponseNotAuthorized();
        }
        return $result;
    }

    public static function getProfilePictureFromId($token,$user_id){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("call security.pictures_get_from_user(:auth_token, :user_id)");
            $stmt->bindParam(":auth_token", $token);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        } catch (PDOException $e){
            ResponseService::ResponseNotAuthorized();

        }
        catch (Exception $e){
            ResponseService::ResponseNotAuthorized();
        }
        return $result;
    }

    public static function setProfilePicture($token, $base64Picture){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("call security.pictures_update(:auth_token, :picture)");

            $stmt->bindParam(":auth_token", $token);
            $stmt->bindParam(":picture", $base64Picture);
            $stmt->execute();

        } catch (PDOException $e){
            ResponseService::ResponseNotAuthorized();

        }
        catch (Exception $e){
            ResponseService::ResponseNotAuthorized();
        }
    }

    private function getDatabaseConnection(){
        return DatabaseConnection::getConnection();
    }
}