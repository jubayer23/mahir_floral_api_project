<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/Database.php';
include_once '../models/ShopStock.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';

$staff = new Staff();
$shopstock = new ShopStock();
$api_key = new Api_key();

$user = $api_key->validate_api_key();
$staff->id = $user;
$shopstock->received_by =   $user;

/*if($staff->check_role() == 'Shop Stock' || $staff->check_role() == 'Admin'){*/
	/*if($staff->check_role() == 'Admin'){
		$shopstock->is_admin = true;
	}else{
		$shopstock->is_admin = false;
	}*/

	if($data = $shopstock->getIncomingShopStock()){
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