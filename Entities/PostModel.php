<?php

class Post{

    var $id;
    var $title;
    var $content;
    var $createdAt;
    var $updatedAt;
    var $deletedAt;
    
    public function __construct($id, $title, $content, $createdAt, $updatedAt, $deletedAt){
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
    }

    public static function getRequiredProperties(){
        return ["title", "content"];
    }

    public function toJson(){
        return get_object_vars($this);
    }
}

?>