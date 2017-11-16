<?php

include_once('DatabaseConnection.php');
include_once('./../Entities/PostModel.php');
include_once('./../Services/SanitizeService.php');

class PostsRepository{


    //--------------------------------------------------------------------------

    public function getPosts($authtoken, $amount, $offset){
        
        $postsArray = array();

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL websecurity.post_get_recent(:authtoken ,:amount, :off_set, @result)");
            $stmt->bindParam('authtoken', $authtoken, PDO::PARAM_STR );
            $stmt->bindParam('amount', $amount, PDO::PARAM_INT);
            $stmt->bindParam('off_set', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @result")->fetch(PDO::FETCH_ASSOC);

            if(!empty($result)){
                $postsArray = makePostsFromResultSet($result);
            }
        }
        catch (PDOException $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }
        catch (Exception $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }

        return $postsArray;

    }

    //--------------------------------------------------------------------------
    
    public function getPostsByUser($authtoken, $user_id, $amount, $offset){
        
        $postsArray = array();

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL websecurity.posts_get_from_wall(:authtoken ,:user_id, :amount, :off_set ,@result)");
            $stmt->bindParam('authtoken', $authtoken, PDO::PARAM_STR );
            $stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam('amount', $amount, PDO::PARAM_INT);
            $stmt->bindParam('off_set', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @result")->fetch(PDO::FETCH_ASSOC);

            if(!empty($result)){
                 $postsArray = makePostsFromResultSet($result);
            }
        }
        catch (PDOException $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }
        catch (Exception $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }

        return $postsArray;
        
    }
    
    //--------------------------------------------------------------------------
    public function createPost($authtoken, $title, $content){

        $newPostId;

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL websecurity.post_create(:authtoken ,:title, :content ,@post_id)");
            $stmt->bindParam('authtoken', $authtoken, PDO::PARAM_STR );
            $stmt->bindParam('title', $title, PDO::PARAM_STR);
            $stmt->bindParam('content', $content, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @post_id")->fetch(PDO::FETCH_ASSOC);

            if(!empty($result)){
               $newPostId = $result["@post_id"];
            }
        }
        catch (PDOException $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }
        catch (Exception $e){
           // @TODO WHAT SHOUL HAPPEN HERE ?
        }

        return  $newPostId;
    }

    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------

}

function makePostsFromResultSet($result){

    $postsArray = [];

     foreach (@$result as $row){
        array_push(
            $postsArray, 
                new Post(
                    $row['id'], 
                    SanitizeService::SanitizeString($row['title']), 
                    SanitizeService::SanitizeString($row['content']), 
                    $row['createdAt']
                )
        );
    }

    return $postsArray;
}