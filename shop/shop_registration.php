<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../models/shop.php';
include_once '../models/staff.php';
include_once '../models/api_key.php';

$staff = new Staff();
$shop = new Shop();
$api_key = new Api_key();

$user = $api_key->validate_api_key();
$staff->id = $user;

if($staff->check_role() == 'Admin'){
	if($data = $shop->add()){
		http_response_code($shop->status_code);
		if($data['status'] == false){
			echo json_encode($data);exit;
		}
			echo json_encode($data);exit;
	}
}else{
	http_response_code(403);
	echo json_encode(['status' => false , 'message' => "You have not Permission to Perform this action"]);exit;
}