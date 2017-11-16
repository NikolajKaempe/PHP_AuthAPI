<?php

include_once('DatabaseConnection.php');

class CommentsRepository{

    public function getCommentsOfPost($token, $post_id, $amount, $offset){}
        $commentsArray = array();

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL websecurity.comment_get_from_post(:authtoken, :post_id, :amount, :offset ,@result)");
            $stmt->bindParam('authtoken', $authtoken, PDO::PARAM_STR );
            $stmt->bindParam('post_id', $post_id, PDO::PARAM_STR);
            $stmt->bindParam('amount', $amount, PDO::PARAM_STR);
            $stmt->bindParam('offset', $offset, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @result")->fetch(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach (@$result as $row){
                    array_push(
                        $commentsArray, 
                        new Comment(
                            $row['id'], 
                            $row['user_id'], 
                            $row['post_id'], 
                            $row['content'], 
                            $row['created_timestamp']
                        )
                    );
                }
            }
        }
        catch (PDOException $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }
        catch (Exception $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }

        return $commentsArray;
    }

    //---------------------------------------------------------------------

    public function createComment($token, $post_id, $content){}
        $newCommentId;

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL websecurity.comment_create(:authtoken ,:post_id, :content ,@comment_id)");
            $stmt->bindParam('authtoken', $authtoken, PDO::PARAM_STR );
            $stmt->bindParam('post_id', $post_id, PDO::PARAM_STR);
            $stmt->bindParam('content', $content, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @comment_id")->fetch(PDO::FETCH_ASSOC);

            //@TODO - if post is not create ?????

            if(!empty($result)){
               $newCommentId = $result["@comment_id"];
            }
        }
        catch (PDOException $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }
        catch (Exception $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }

        return  $newCommentId;
    }

    //---------------------------------------------------------------------
    //---------------------------------------------------------------------
    //---------------------------------------------------------------------
}

?>