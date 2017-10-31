<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 19-10-2017
 * Time: 15:39
 */

include_once('DatabaseConnection.php');

class StoredProcedures{


    public function isUserBanned($username, $ipAddress){

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL websecurity.is_user_banned(:username,:ipaddress,@result)");
            $stmt->bindParam('username', $username, PDO::PARAM_STR );
            $stmt->bindParam('ipaddress', $ipAddress, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @result")->fetch(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach (@$result as $row){
                    ($row === '0')? $isBanned = false: $isBanned = true;
                }
            }else{
                $isBanned = true;
            }
        }
        catch (PDOException $e){
            $isBanned = true;
        }
        catch (Exception $e){
            $isBanned = true;
        }

        return $isBanned;
    }

    public function doUserExists($username){

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("Select websecurity.do_user_exist(?) as result");
            $stmt->bindParam(1, $username, PDO::PARAM_STR,28);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach (@$result as $row){
                    ($row === '1')? $exists = true: $exists = false;
                }
            }else{
                $exists = false;
            }
        } catch (PDOException $e){
            $exists = true;
        }catch (Exception $e){
            $exists = false;
        }
        return $exists;
    }

    public function loginUser($username,$ipAddress){
        $authToken = new AuthToken();

        try{
            $connection = $this->getDatabaseConnection();

            $stmt = $connection->prepare("CALL websecurity.login_user(:username,:ipAddress,@token,@timeAlive)");
            $stmt->bindParam('username', $username, PDO::PARAM_STR );
            $stmt->bindParam('ipAddress', $ipAddress, PDO::PARAM_STR);
            $stmt->execute();
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
            $authToken = new AuthToken();
        }


        return $authToken;
    }

    public function createUser($username, $hashedPassword, $salt){
        try{
            //$connection = new PDO('mysql:host:localhost',"root","");
            $connection = DatabaseConnection::getConnection();
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

    public function addFailedLoginAttempt($username,$ipAddress){

        try{
            $connection = $this->getDatabaseConnection();

            $stmt = $connection->prepare("Call websecurity.add_failed_login_attempt(:username,:ipAddress)");
            $stmt->bindParam('username', $username, PDO::PARAM_STR );
            $stmt->bindParam('ipAddress', $ipAddress, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch (PDOException $e){
            throw new Exception("Internal Server Error");
        }
        catch (Exception $e){
            throw new Exception("Internal Server Error");
        }
    }

    public function removeFailedLoginAttempt($username,$ipAddress){

        try{
            $connection = $this->getDatabaseConnection();

            $stmt = $connection->prepare("CALL websecurity.remove_failed_login_attempts(:username,:ipAddress)");
            $stmt->bindParam('username', $username, PDO::PARAM_STR );
            $stmt->bindParam('ipAddress', $ipAddress, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch (PDOException $e){
            throw new Exception("Internal Server Error");
        }
        catch (Exception $e){
            throw new Exception("Internal Server Error");
        }
    }

    private function getDatabaseConnection(){
        return DatabaseConnection::getConnection();
    }
}