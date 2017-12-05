<?php
/**
 * Created by PhpStorm.
 * User: Kaempe
 * Date: 05-12-2017
 * Time: 11:09
 */

include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Services/ResponseService.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/WebSec/Repositories/FriendRepository.php');


class FriendInvite{

    private $user_id;
    private $accept;

    public function constructFromHashMap($json){
        $data = json_decode($json, true);
        if (empty($data)) ResponseService::ResponseBadRequest("Invalid Request-Body");
        foreach ($data AS $key => $value) $this->{$key} = $value;
        $this->failOnInvalidModel();
    }

    public function processInvite($token){
        $this->failOnInvalidModel();
        FriendRepository::processInvite($token,$this->user_id,$this->accept);
    }

    private function failOnInvalidModel(){
        if (!is_numeric($this->user_id) ||
            !is_bool($this->accept) ) {
            ResponseService::ResponseBadRequest("Invalid Request-Body");
        }

    }
}