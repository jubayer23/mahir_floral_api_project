<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/Database.php';
include_once '../models/ReadyStock.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';

$staff = new Staff();
$readystock = new ReadyStock();
$api_key = new Api_key();

$user = $api_key->validate_api_key();
$staff->id = $user;
$readystock->added_by =   $user;

if($staff->check_role() == 'Raw Stock'){
	if($data = $readystock->add()){
		http_response_code($readystock->status_code);
		if($data['status'] == false){
			echo json_encode($data);exit;
		}
			echo json_encode($data);exit;
	}
}else{
	http_response_code(403);
	echo json_encode(['status' => false , 'message' => "You have not Permission to Perform this action"]);exit;
}