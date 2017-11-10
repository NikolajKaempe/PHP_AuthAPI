<?php

class Post{

    var  $id;
    var  $title;
    var  $content;
    var  $createdAt;

    public function __construct($id, $title, $content, $createdAt){
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }
}

?>