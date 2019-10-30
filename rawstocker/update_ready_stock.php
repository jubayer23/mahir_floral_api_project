<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../models/ReadyStock.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';

$staff = new Staff();
$readyStock = new ReadyStock();
$api_key = new Api_key();

$user_id = $api_key->validate_api_key();
$readyStock->added_by = $staff->id = $user_id;

if($staff->check_role() == 'Raw Stock' || $staff->check_role() == 'Admin' ){
    if($data = $readyStock->updateReadyStock()){
        //http_response_code($rawstock->status_code);
        if($data['status'] == false){
            echo json_encode($data);exit;
        }
        echo json_encode($data);exit;
    }
}else{
    echo json_encode(['status' => false , 'message' => "You do not have the permission to perform this action!"]);exit;
}