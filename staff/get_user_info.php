<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// database connection will be here
include_once '../config/database.php';
include_once '../models/staff.php';
include_once '../models/api_key.php';

$staff = new Staff();
$api_key = new Api_key();
$user = $api_key->validate_api_key();
$staff->id = $user;

//$staff->fields = array('email,username,first_name,last_name');
/*
$staff->user_info();
if($user->name!=null){
    // create array
    $user_info = array(
        "name" => $user->name,
        "email" => $user->email
    );
    http_response_code(200);
	echo json_encode(['response' =>['status' => true,'data' => $user_info]]);exit;
}
else{
    http_response_code(404);
	echo json_encode(['response'=>['status' => true,"message" => "User does not exist."]]);exit;
}*/
if($staff->check_role() == 'Shop Stock' || $staff->check_role() == 'Raw Stock' || $staff->check_role() == 'Admin'){
if($staff->check_role() == 'Admin'){
	$staff->is_admin = true;
}else{
	$staff->is_admin = false;
}
	
if($data = $staff->user_info()){
	http_response_code($staff->status_code);
	if($data['status'] == false){
		echo json_encode($data);exit;
	}
		echo json_encode($data);exit;
}
}else{
	http_response_code(403);
	echo json_encode(['status' => false , 'message' => "You have not Permission to Perform this action"]);exit;
}

