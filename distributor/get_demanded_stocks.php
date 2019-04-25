<?php
/**
 * Created by PhpStorm.
 * User: jubayer
 * Date: 4/24/2019
 * Time: 3:54 PM
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../models/DemandStock.php';
include_once '../models/Staff.php';
include_once '../models/Api_key.php';

$staff = new Staff();
$demandStock = new DemandStock();
$api_key = new Api_key();

$user = $api_key->validate_api_key();
$staff->id = $user;
$demandStock->demanded_by =   $user;

if($staff->check_role() == 'Shop Stock' || $staff->check_role() == 'Admin' || $staff->check_role() == 'Distributor'){
    if($data = $demandStock->getDemandedStocks()){
        //http_response_code($shopstock->status_code);
        if($data['status'] == false){
            echo json_encode($data);exit;
        }
        echo json_encode($data);exit;
    }
}else{
    //http_response_code(403);
    echo json_encode(['status' => false , 'message' => "You have not Permission to Perform this action"]);exit;
}