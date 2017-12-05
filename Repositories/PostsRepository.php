<?php

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/DatabaseConnection.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/SanitizeService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Entities/Post.php');


class PostsRepository{


    //--------------------------------------------------------------------------

    public function getPosts($authToken, $amount, $offset){
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
        $post = new Post();
        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL security.post_create(:auth_token ,:title, :content)");
            $stmt->bindParam('auth_token', $authToken, PDO::PARAM_STR );
            $stmt->bindParam('title', $title, PDO::PARAM_STR);
            $stmt->bindParam('content', $content, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($result)){
                foreach (@$result as $row){
                    $post = makePostFromRow($row);
                }
            }
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
        return $post;
    }

    public static function updatePost($token,$id, $title ,$content){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.post_update(:auth_token, :id, :title, :content)");
            $stmt->bindParam('auth_token', $token, PDO::PARAM_STR );
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->bindParam('title', $title, PDO::PARAM_STR);
            $stmt->bindParam('content', $content, PDO::PARAM_STR);
            $stmt->execute();

        }
        catch (PDOException $e){
            if ($e->getCode() == 45000) {
                ResponseService::ResponseBadRequest($e->errorInfo[2]);
            }elseif ($e->getCode() == 23000) {
                ResponseService::ResponseBadRequest("Invalid Comment");
            }else{
                ResponseService::ResponseInternalError();
            }
        }
        catch (Exception $e){
            ResponseService::ResponseInternalError();
        }
    }


    public static function deletePost($token,$id){
        try{
            $connection = DatabaseConnection::getConnection();
            $stmt = $connection->prepare("CALL security.post_delete(:auth_token,:id)");
            $stmt->bindParam('auth_token', $token, PDO::PARAM_STR );
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
        catch (PDOException $e){
            if ($e->getCode() == 45000) {
                ResponseService::ResponseBadRequest($e->errorInfo[2]);
            }elseif ($e->getCode() == 23000) {
                ResponseService::ResponseBadRequest("Invalid Comment");
            }else{
                ResponseService::ResponseInternalError();
            }
        }
        catch (Exception $e){
            ResponseService::ResponseInternalError();
        }
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
         array_push($postsArray,makePostFromRow($row));
    }

    return $postsArray;
}

function makePostFromRow($row){
    $post = new Post();

    $post->construct(
        $row['id'],
        $row['user_id'],
        $row['username'],
        $row['title'],
        $row['content'],
        $row['created_timestamp'],
        $row['updated_timestamp'],
        $row['deleted_timestamp']
    );
    return $post;
}