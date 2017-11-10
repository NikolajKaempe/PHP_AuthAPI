<?php

include_once('DatabaseConnection.php');

class PostsRepository{


    //--------------------------------------------------------------------------

    public function getPosts($authtoken, $amountOfPostsToReturn){
        
        $postsArray = array();

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL websecurity.post_get_recent(:authtoken ,:amount ,@result)");
            $stmt->bindParam('authtoken', $authtoken, PDO::PARAM_STR );
            $stmt->bindParam('amount', $amountOfPostsToReturn, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @result")->fetch(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach (@$result as $row){
                    
                    // CONSTRUCT POSTS.....
                    // @TODO GET $id, $title, $content, $createdAt from $row
                    array_push($postsArray, new Post($id, $title, $content, $createdAt));
                }
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
    
    public function getPostsByUser($authtoken, $username ){
        
        $postsArray = array();

        try{
            $connection = $this->getDatabaseConnection();
            $stmt = $connection->prepare("CALL websecurity.posts_get_from_wall(:authtoken ,:username ,@result)");
            $stmt->bindParam('authtoken', $authtoken, PDO::PARAM_STR );
            $stmt->bindParam('username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            $result = $connection->query("select @result")->fetch(PDO::FETCH_ASSOC);

            if(!empty($result)){
                foreach (@$result as $row){
                    
                    // CONSTRUCT POSTS.....
                    // @TODO GET $id, $title, $content, $createdAt from $row
                    array_push($postsArray, new Post($id, $title, $content, $createdAt));
                }
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
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------

}