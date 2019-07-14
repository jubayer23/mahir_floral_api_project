<?php 
require_once '../library/Validator.php';
include_once '../config/constants.php';
  class Shop {
    private $table = 'shop';

    public $id;
    public $name;
    public $address;
    public $loc_lat;
    public $loc_long;
    public $added_by;
    public $creation_date;
	public $status_code;
	
	public function add()
    {
		$validator = new Validator();
		$errors = [];
		$message = null;
		$_POST = (array) json_decode(file_get_contents("php://input", true));
		if(isset($_POST['shop_name']) && (trim($_POST['shop_name']) != '') ) {
			$shop_name=$_POST['shop_name'];
		}
		else {
			$message = 'Required field missing';
			$errors['shop_name'] = "Shop Name Is Require";
		}
		//, 
		if(isset($_POST['location']) && (trim($_POST['location']) != '') ) {
			$location=$_POST['location'];
		}
		else {
			$message = 'Required field missing';
			$errors['location'] = "Location Is Require";
		}
		if(isset($_POST['latitude']) && (trim($_POST['latitude']) != '') ) {
			if($validator->validate_float($_POST['latitude'])){
				$latitude=$_POST['latitude'];
			}else {
				$message = 'Latitude Require Float';
				$errors['latitude'] = "Latitude Require Float";
			}
		}
		else {
			$message = 'Required field missing';
			$errors['latitude'] = "Latitude Is Require";
		}
		if(isset($_POST['longitude']) && (trim($_POST['longitude']) != '') ) {
			
			if($validator->validate_float($_POST['longitude'])){
				$longitude = $_POST['longitude'];
			}else {
				$message = 'Longitude Require Float';
				$errors['longitude'] = "Longitude Require Float";
			}
		}
		else {
			$message = 'Required field missing';
			$errors['longitude'] = "Longitude Is Require";
		}
		
		if(!empty($errors)){
			return ['status' => false , 'message'  => $message , 'errors'  => $errors];

		}else{

			$shop_data = [
				'name' => html_escape($shop_name),
				'address' => html_escape($location),
				'loc_lat' => $latitude,
				'loc_long' => $longitude
			];				
			$added = DB::insert('shop',$shop_data);
			if($added){
				$shop_id = DB::insertId();
				$this->status_code = 201;
				return array('status' => true,'shop_id' => $shop_id, 'message' => 'Shop Successfully Created');
			}
		}
		
				
	}
	public function getshops()
    {
		$shops = DB::query("SELECT * FROM shop");				
			if($shops){
				$this->status_code = 200;
				return array('status' => true,'shops' => $shops);
			}
		
	}
	public function update()
    {
		
	}
	public function delete()
    {
		
	}
  }