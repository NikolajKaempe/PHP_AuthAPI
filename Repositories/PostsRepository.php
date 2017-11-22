<?php

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/DatabaseConnection.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/Post_v2.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/SanitizeService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');


class PostsRepository{


    //--------------------------------------------------------------------------

    public function getPosts($authToken, $amount, $offset){
        var_dump($amount,$offset);

        $postsArray = array();

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL security.post_get_recent(:auth_token ,:amount, :off_set)");//, @result)");
            $stmt->bindParam('auth_token', $authToken, PDO::PARAM_STR );
            $stmt->bindParam('amount', $amount, PDO::PARAM_INT);
            $stmt->bindParam('off_set', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                $postsArray = makePostsFromResultSet($result);
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

        return $postsArray;

    }

    //--------------------------------------------------------------------------
    public function getPostsByUser($authToken, $user_id, $amount, $offset){
        
        $postsArray = array();

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL security.post_get_from_wall(:auth_token ,:user_id, :amount, :off_set)");// ,@result)");
            $stmt->bindParam('auth_token', $authToken, PDO::PARAM_STR );
            $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam('amount', $amount, PDO::PARAM_INT);
            $stmt->bindParam('off_set', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                $postsArray = makePostsFromResultSet($result);
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

        return $postsArray;
        
    }
    
    //--------------------------------------------------------------------------
    public function createPost($authToken, $title, $content){
        $id = 0;
        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL security.post_create(:auth_token ,:title, :content)");// ,@post_id)");
            $stmt->bindParam('auth_token', $authToken, PDO::PARAM_STR );
            $stmt->bindParam('title', $title, PDO::PARAM_STR);
            $stmt->bindParam('content', $content, PDO::PARAM_STR);
            $stmt->execute();
            $id = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e){
            if ($e->getCode() == 45000) {
                ResponseService::ResponseBadRequest($e->errorInfo[2]);
            }elseif ($e->getCode() == 23000){
                ResponseService::ResponseBadRequest("Post already exists");
            }
            else{
                ResponseService::ResponseInternalError();
            }
        }
        catch (Exception $e){
            ResponseService::ResponseInternalError();
        }
        return $id;
    }

    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    private function getDatabaseConnection(){
        return DatabaseConnection::getConnection();
    }
}


function makePostsFromResultSet($result){

    $postsArray = [];

     foreach (@$result as $row){

         $post = new Post_v2();
         $post->construct($row['id'],
             $row['user_id'],
             'Dummy Username',
             SanitizeService::SanitizeString($row['title']),
             SanitizeService::SanitizeString($row['content']),
             $row['created_timestamp'],
             $row['updated_timestamp'],
             $row['deleted_timestamp']);
         array_push($postsArray,$post);
    }

    return $postsArray;
}