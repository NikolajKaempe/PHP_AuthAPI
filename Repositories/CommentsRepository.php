<?php

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/DatabaseConnection.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/SanitizeService.php');

class CommentsRepository{

    public function getCommentsOfPost($token, $post_id, $amount, $offset){
        $commentsArray = array();

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL security.comment_get_from_post(:authtoken, :post_id, :amount, :offset");// ,@result)");
            $stmt->bindParam("authtoken", $token, PDO::PARAM_STR );
            $stmt->bindParam('post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam('amount', $amount, PDO::PARAM_INT);
            $stmt->bindParam('offset', $offset, PDO::PARAM_INT);

            $result = $stmt->execute();

            /*
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @result")->fetch(PDO::FETCH_ASSOC);
            */
            if(!empty($result)){
                foreach (@$result as $row){
                    array_push(
                        $commentsArray, 
                        new Comment(
                            $row['id'], 
                            $row['user_id'], 
                            $row['post_id'], 
                            SanitizeService::SanitizeString($row['content']), 
                            $row['created_timestamp']
                        )
                    );
                }
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
        $newCommentId = 0;

        var_dump($token,$post_id,$content);
        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL security.comment_create(:authtoken ,:post_id, :content");// ,@comment_id)");
            $stmt->bindParam('authtoken', $token, PDO::PARAM_STR );
            $stmt->bindParam('post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam('content', $content, PDO::PARAM_STR);
            $result = $stmt->execute();

            /*
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @comment_id")->fetch(PDO::FETCH_ASSOC);
            */
            if(!empty($result)){
               $newCommentId = $result["@id"];
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

        return  $newCommentId;
    }

    //---------------------------------------------------------------------
    //---------------------------------------------------------------------
    //---------------------------------------------------------------------
}

?>