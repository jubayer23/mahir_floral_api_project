<?php
/**
 * Created by PhpStorm.
 * User: jubayer
 * Date: 7/4/2019
 * Time: 5:26 PM
 */
class Notification{
    private $title;
    private $message;
    private $image_url;
    private $action;
    private $action_destination;
    private $shop_id;
    private $shop_name;
    private $data;

    function __construct(){

    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function setMessage($message){
        $this->message = $message;
    }

    public function setImage($imageUrl){
        $this->image_url = $imageUrl;
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function setActionDestination($actionDestination){
        $this->action_destination = $actionDestination;
    }

    public function setPayload($data){
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getShopId()
    {
        return $this->shop_id;
    }

    /**
     * @param mixed $shop_id
     */
    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
    }

    /**
     * @return mixed
     */
    public function getShopName()
    {
        return $this->shop_name;
    }

    /**
     * @param mixed $shop_name
     */
    public function setShopName($shop_name)
    {
        $this->shop_name = $shop_name;
    }



    public function getNotificatin(){
        $notification = array();
        $notification['title'] = $this->title;
        $notification['message'] = $this->message;
        $notification['image'] = $this->image_url;
        $notification['action'] = $this->action;
        $notification['action_destination'] = $this->action_destination;
        $notification['shop_id'] = $this->shop_id;
        $notification['shop_name'] = $this->shop_name;
        return $notification;
    }
}
?>