<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/Database.php';
include_once '../models/RawStock.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';

$staff = new Staff();
$rawstock = new RawStock();
$api_key = new Api_key();

$user = $api_key->validate_api_key();
$rawstock->added_by = $staff->id = $user;
if($staff->check_role() == 'Raw Stock'){
	if($data = $rawstock->add()){
		http_response_code($rawstock->status_code);
		if($data['status'] == false){
			echo json_encode($data);exit;
		}
			echo json_encode($data);exit;
	}
}else{
	echo json_encode(['status' => false , 'message' => "You do not have the permission to perform this action!"]);exit;
}