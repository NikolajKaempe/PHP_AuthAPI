<?php

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/DatabaseConnection.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');

class CommentsRepository{

    public function getCommentsOfPost($token, $post_id, $amount, $offset){
        $commentsArray = [];
        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL security.comment_get_from_post(:auth_token, :post_id, :amount, :offset)");
            $stmt->bindParam("auth_token", $token, PDO::PARAM_STR );
            $stmt->bindParam('post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam('amount', $amount, PDO::PARAM_INT);
            $stmt->bindParam('offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                $commentsArray = $this->makeCommentsFromResultSet($result);
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

        return $commentsArray;
    }

    //---------------------------------------------------------------------

    public function createComment($token, $post_id, $content){
        $comment = new Comment();
        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL security.comment_create(:auth_token,:post_id, :content)");
            $stmt->bindParam('auth_token', $token, PDO::PARAM_STR );
            $stmt->bindParam('post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam('content', $content, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach (@$result as $row){
                    $comment = $this->makeCommentFromRow($row);
                }
            }
        }
        catch (PDOException $e){
            if ($e->getCode() == 45000) {
                ResponseService::ResponseBadRequest($e->errorInfo[2]);
            }elseif ($e->getCode() == 23000) {
                ResponseService::ResponseBadRequest("Invalid Post");
            }else{
                ResponseService::ResponseInternalError();
            }
        }
        catch (Exception $e){
            ResponseService::ResponseInternalError();
        }

        return  $comment;
    }

    private function getDatabaseConnection(){
        return DatabaseConnection::getConnection();
    }
    //---------------------------------------------------------------------
    //---------------------------------------------------------------------
    //---------------------------------------------------------------------

    private function makeCommentsFromResultSet($result){
        $commentsArray = [];
        foreach (@$result as $row){
            array_push($commentsArray,$this->makeCommentFromRow($row));
        }
        return $commentsArray;
    }

    private function makeCommentFromRow($row){
        $comment = new Comment();
        $comment->construct(
            $row['id'],
            $row['user_id'],
            $row['username'],
            $row['post_id'],
            $row['content'],
            $row['created_timestamp'],
            $row['updated_timestamp'],
            $row['deleted_timestamp']
        );
        return $comment;
    }
}



?>