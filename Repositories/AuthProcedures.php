<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 5-11-2017
 * Time: 23:39
 */

include_once('DatabaseConnection.php');

/**
 * Class AuthProcedures contains methods for all stored procedures
 * used for User authentication and login.
 */
class AuthProcedures{

    /**
     * This method fetches the hashedPassword and salt for a specific user.
     * If no user is found, an empty user is returned.
     * @param $username, a string representing the username supplied by the user.
     * @return ApplicationUser, containing the hashedPassword and salt for the requested User.
     */
    public function fetchSalt($username){
        $salt = "dummy";

        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("call websecurity.fetch_salt(:username, @salt)");

            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("Select @salt")->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach ($result as $row){

                    $salt = $row['@salt'];
                }
            }

        }catch (Exception $e){
            $salt = "dummy";
        }
        return $salt;
    }

    public function fetchOnlineUser($token){
        $user = new ResponseUser();

        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("call websecurity.fetch_online_user(:token,@username, @role)");

            $stmt->bindParam(":token", $token);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("Select @username, @role")->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach ($result as $row){

                    $user->construct($row['@username'],$row['@role']);
                }
            }

        }catch (Exception $e){
            $user = new ResponseUser();
        }
        return $user;
    }

    public function loginUser($username,$ipAddress,$hashedPassword){
        $authToken = new AuthToken();

        try{
            $connection = $this->getDatabaseConnection();

            $stmt = $connection->prepare("CALL websecurity.login_user(:username,:ipAddress, :hashedPassword,@token,@timeAlive)");
            $stmt->bindParam('username', $username, PDO::PARAM_STR );
            $stmt->bindParam('ipAddress', $ipAddress, PDO::PARAM_STR);
            $stmt->bindParam('hashedPassword', $hashedPassword, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->errorCode() == 45000){
                throw new Exception($stmt->errorInfo()[2]);
            }

            $stmt->closeCursor();
            $result = $connection->query("Select @token, @timeAlive")->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach (@$result as $row){
                    $authToken->construct($row['@token'],$row['@timeAlive']);
                }
            }

        }
        catch (PDOException $e){
            $authToken = new AuthToken();
        }
        catch (Exception $e){
            throw $e;
        }


        return $authToken;
    }

    public function createUser($username, $hashedPassword, $salt){
        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("Call websecurity.create_user(:username, :hashedPassword, :salt)");
            $stmt->bindParam('username', $username);
            $stmt->bindParam('hashedPassword', $hashedPassword);
            $stmt->bindParam('salt', $salt);
            $stmt->execute();

            if ($stmt->errorCode() == 45000){
                throw new Exception($stmt->errorInfo()[2]);
            }elseif ($stmt->errorCode() == 23000){
                throw new Exception('Username already in use');
            }
        }
        catch (PDOException $e){
            throw new Exception("Internal Server Error");
        }catch (Exception $e){
            throw $e;
        }
    }

    private function getDatabaseConnection(){
        return DatabaseConnection::getConnection();
    }
}