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
$readystock->added_by = $staff->id = $user;
/*if($staff->check_role() == 'Raw Stock' || $staff->check_role() == 'Admin'  || $staff->check_role() == 'Shop Stock' || $staff->check_role() == 'Distributor'){*/
	
	if($staff->check_role() == 'Admin'){
		$readystock->is_admin = true;
	}else{
		$readystock->is_admin = false;
	}
	
	if($data = $readystock->get()){
		//http_response_code($readystock->status_code);
		if($data['status'] == false){
			echo json_encode($data);exit;
		}
			echo json_encode($data);exit;
	}
/*}else{
	
	echo json_encode(['status' => false , 'message' => "You do not have the permission to perform this action!"]);exit;
}*/