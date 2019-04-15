<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/Database.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';

$staff = new Staff();
//$api_key = new Api_key();
//$user = $api_key->validate_api_key();
//if($staff->check_role() == 'Admin'){
	if($data = $staff->timesheet()){
		http_response_code($staff->status_code);
		if($data['status'] == false){
			echo json_encode($data);exit;
		}
			echo json_encode($data);exit;
	}
//}