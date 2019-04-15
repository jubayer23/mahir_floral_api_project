<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/Database.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';

$staff = new Staff();


if($data = $staff->signin()){
	http_response_code($staff->status_code);
	if($data['status'] == false){
		echo json_encode($data);exit;
	}
		echo json_encode($data);exit;
	}else{
		http_response_code(503);
		echo json_encode(['status' => false,"message" => "Unable To Signup Please Try Later"]);exit;
	}