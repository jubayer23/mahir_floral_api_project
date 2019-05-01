<?php
/**
 * Created by PhpStorm.
 * User: jubayer
 * Date: 4/23/2019
 * Time: 3:41 PM
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

if($staff->check_role() == 'Shop Stock'){
    if($data = $demandStock->makeDemand()){
        //http_response_code($shopstock->status_code);
        if($data['status'] == false){
            echo json_encode($data);exit;
        }
        echo json_encode($data);exit;
    }
}else{
    //http_response_code(403);
    echo json_encode(['status' => false , 'message' => "You do not have the permission to perform this action!"]);exit;
}