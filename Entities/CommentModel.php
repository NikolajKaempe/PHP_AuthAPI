<?php

class Comment{

    var $id;
    var $user_id;
    var $post_id;
    var $content;
    var $createdAt;
    var $updatedAt;
    var $deletedAt;

    
    public function __construct($id, $user_id, $post_id, $content, $createdAt, $updatedAt, $deletedAt){
            $this->id = $id;
            $this->user_id = $user_id;
            $this->post_id = $post_id;
            $this->content = $content;
            $this->createdAt = $createdAt;
            $this->updatedAt = $updatedAt;
            $this->deletedAt = $deletedAt;
    }

    public static function getRequiredProperties(){
        return ["post_id", "content"];
    }

    public function toJson(){
        return get_object_vars($this);
    }
}

?>