<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// database connection will be here
include_once '../config/Database.php';
include_once '../config/constants.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';


$staff = new Staff();
$api_key = new Api_key();
$user = $api_key->validate_api_key();
//echo $user;
$staff->id = $user;


if($staff->check_role() == ROLE_SHOP_STOCKER || $staff->check_role() == ROLE_RAW_STOCKER || $staff->check_role() == ROLE_ADMIN){
if($staff->check_role() == ROLE_ADMIN){
	$staff->is_admin = true;
}else{
	$staff->is_admin = false;
}
	
if($data = $staff->user_info()){
	//http_response_code($staff->status_code);
	if($data['status'] == false){
		echo json_encode($data);exit;
	}
		echo json_encode($data);exit;
}
}else{
	//http_response_code(200);
	echo json_encode(['status' => false , 'message' => "You do not have the permission to perform this action!"]);exit;
}

