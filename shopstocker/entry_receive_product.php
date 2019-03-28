<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../models/ShopStock.php';
include_once '../models/staff.php';
include_once '../models/api_key.php';

$staff = new Staff();
$shopstock = new ShopStock();
$api_key = new Api_key();

$user = $api_key->validate_api_key();
$staff->id = $user;
$shopstock->received_by =   $user;

if($staff->check_role() == 'Shop Stock'){
	if($data = $shopstock->receiverd()){
		http_response_code($shopstock->status_code);
		if($data['status'] == false){
			echo json_encode($data);exit;
		}
			echo json_encode($data);exit;
	}
}else{
	http_response_code(403);
	echo json_encode(['status' => false , 'message' => "You have not Permission to Perform this action"]);exit;
}