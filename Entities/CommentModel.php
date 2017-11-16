<?php

class Comment{

    var $id;
    var $user_id;
    var $post_id;
    var $content;
    var $createdAt;

    
    public function __construct($id, $user_id, $post_id, $content, $createdAt){
            $this->id = $id;
            $this->user_id = $user_id;
            $this->post_id = $post_id;
            $this->content = $content;
            $this->createdAt = $createdAt;
    }

    public static function getRequiredProperties(){
        return ["user_id", "post_id", "content"];
    }
}

?>