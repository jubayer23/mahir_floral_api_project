<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/Database.php';
include_once '../models/ReturnStock.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';

$staff = new Staff();
$returnstock = new ReturnStock();
$api_key = new Api_key();

$user_id = $api_key->validate_api_key();
$staff->id = $user_id;
$returnstock->return_by_id =   $user_id;

/*if($staff->check_role() == 'Shop Stock' || $staff->check_role() == 'Admin'){*/
	/*if($staff->check_role() == 'Admin'){
		$shopstock->is_admin = true;
	}else{
		$shopstock->is_admin = false;
	}*/

	if($data = $returnstock->getReturnStocks()){
		//http_response_code($shopstock->status_code);
		if($data['status'] == false){
			echo json_encode($data);exit;
		}
			echo json_encode($data);exit;
	}
/*}else{
	//http_response_code(403);
	echo json_encode(['status' => false , 'message' => "You do not have the permission to perform this action!"]);exit;
}*/