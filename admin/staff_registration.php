<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// database connection will be here
include_once '../config/Database.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';

$staff = new Staff();
$api_key = new Api_key();
$user_id = $api_key->validate_api_key();
//Only Admin can
//get id by access token_get_all
//check role by access token
$staff->id = $user_id;
if($staff->check_role() == 'Admin'){
	if($data = $staff->signup()){
		
		if($data['status'] == false){
			//http_response_code(400);
			echo json_encode( $data);exit;
		}
		//http_response_code(201);
		echo json_encode( $data);exit;

		//echo json_encode(['response' =>['status' => true,'message' => 'Successfully Registration']]);exit;
	}else{
		//http_response_code(503);
		echo json_encode(['status' => false,"message" => "Unable to signup please try again later"]);exit;
	}
}else{
	//http_response_code(403);
	echo json_encode(['status' => false,"message" => "You do not have the permission to perform this action!"]);exit;
}


