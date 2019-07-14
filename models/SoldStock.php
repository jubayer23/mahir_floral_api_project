<?php 
	require_once '../library/Validator.php';
include_once '../config/constants.php';
  class SoldStock {
    private $table = 'sold_stock';

    public $id;
    public $product_id;
    public $quantity;
	public $comment;
	public $sell_date;
	public $seller_by;
	public $is_admin;
	
	
	//raw_stock user entry 
	//shop stock accept it..
 	public function add()
    {
		//product_name, price, quantity, unit, date, color, comment, user_id
		$validator = new Validator();
		$errors = [];
		$message = null;
		$_POST = (array) json_decode(file_get_contents("php://input", true));
		
		if(isset($_POST['shop_stock_id']) && (trim($_POST['shop_stock_id']) != '') ) {
			if($validator->integer($_POST['shop_stock_id'])){
				
				$shop_exist = DB::query("SELECT * FROM shop_stock WHERE id = %i ",$_POST['shop_stock_id']);
				
				if(!$shop_exist){
					$message = "Shop stock item does not exist";
					$errors['shop_stock_id'] = "Shop Stock not exist";
				}else{
					$shop_exist = _row_array($shop_exist);
					$shop_stock_id=$_POST['shop_stock_id'];
				}
				
			}else{
				$errors['shop_stock_id'] = "Shop Stock Require Integer";
			}	
		}
		else {
			$message = 'Required field missing';
			$errors['shop_stock_id'] = "Shop Stock Is Require";
		}
		
		if(isset($_POST['quantity']) && (trim($_POST['quantity']) != '') ) {
			if($validator->integer($_POST['quantity'])){

				$shop_stock = DB::query("SELECT * FROM shop_stock WHERE id = %i AND quantity >= %i ",$_POST['shop_stock_id'],$_POST['quantity']);
				if(!$shop_stock){
					$message = "Quantity exceed. You cannot sell more then existed quantity!";
					$errors['shop_stock_id'] = "Quantity exceed. You cannot sell more then existed quantity!";
				}else{
					$shop_stock = _row_array($shop_stock);
					$quantity=$_POST['quantity'];
				}
				
			}else{
				$errors['quantity'] = "Quantity Require Integer";
			}
			
		}
		else {
			$message = 'Required field missing';
			$errors['quantity'] = "Quantity Is Require";
		}
		
		if(isset($_POST['comment']) && (trim($_POST['comment']) != '') ) {
				$comment = $_POST['comment'];
		}else {
			$comment = '';
		}
		
		if(!empty($errors)){
			$this->status_code = 400;
			return ['status' => false , 'message'  => $message , 'errors'  => $errors];

		}else{

			$sold_data = [
				'product_id' => $shop_stock['product_id'],
				'shop_stock_id' => $_POST['shop_stock_id'],
				'quantity' => $quantity,
				'comment' => html_escape($comment),
				'sell_date' => date('Y-m-d H:i:s'),
				'seller_by' => $this->seller_by
			];
			//quantity subtract from ready stock
			//add in shop_stock
			//delivery_status 0
			
			//when shop stock user received 
//print_r($shop_stock);die;			
			$added = DB::insert($this->table,$sold_data);
			if($added){
				$is_update = DB::query("UPDATE shop_stock SET quantity= quantity - %i WHERE id=%i", $quantity, $shop_stock_id);
				$this->status_code = 201;
				return array('status' => true, 'message' => 'Successfully Stock Sold');
			}
		}
		
				
	}
	public function get()
    {
		$validator = new Validator();
		$errors = [];
		$message = null;
		$_POST = (array) json_decode(file_get_contents("php://input", true));
		
		if(isset($_POST['filter_by_shop_id']) && (trim($_POST['filter_by_shop_id']) != '') ) {
			if($validator->integer($_POST['filter_by_shop_id'])){
				
				//$shop_exist = DB::query("SELECT * FROM shop WHERE id = %i ",$_POST['filter_by_shop_id']);
				//var_dump($shop_stock['shop_id'],$this->seller_by);die;
				/*if(!$this->is_admin){
					$is_shop_owner = DB::query("SELECT * FROM user_shop WHERE shop_id = %i AND user_id = %i ",$_POST['filter_by_shop_id'],$this->seller_by);
					if(!$is_shop_owner){
					$errors['filter_by_shop_id'] = "Only Owner Can Request";
					}else{
						$is_shop_owner = _row_array($is_shop_owner);
						$filter_by_shop_id=$_POST['filter_by_shop_id'];
					}
				}else{
					$is_shop_owner = DB::query("SELECT * FROM user_shop WHERE shop_id = %i AND user_id = %i ",$_POST['filter_by_shop_id'],$this->seller_by);
				
				}*/
				
				
			}else{
				$errors['filter_by_shop_id'] = "Shop Require Integer";
			}	
		}
		else {
			$message = 'Required field missing';
			$errors['filter_by_shop_id'] = "Shop Is Require";
		}
		if(isset($_POST['month']) && (trim($_POST['month']) != '') ) {
			if($validator->regex_match($_POST['month'],'/^1[0-2]|[1-9]$/')){
				$month = $_POST['month'];
			}else{
				$errors['month'] = "Invalid month Formate Year Look Like mm";
			}
		}else {
			$message = 'Required field missing';
			$errors['month'] = "Month is Require";
		}
		
		if(isset($_POST['year']) && (trim($_POST['year']) != '') ) {
			if($validator->regex_match($_POST['year'],'/^[0-9]{4}$/')){
				$year = $_POST['year'];
			}else{
				$errors['year'] = "Invalid Year Formate Year Look Like yyyy";
			}
		}else {
			$message = 'Required field missing';
			$errors['year'] = "Year is Require";
		}
		
		if(!empty($errors)){
			$this->status_code = 400;
			return ['status' => false , 'message'  => $message , 'errors'  => $errors];

		}else{
			
			$soldStocks = DB::query("
			SELECT DISTINCT  S.id, D.name AS shop_name, R.product_name, R.price, R.unit, S.quantity, DATE_FORMAT( DATE(B.sell_date), '%d/%m/%Y' ) AS sold_date, S.comment
			FROM `shop_stock` S 
			JOIN shop D ON D.id = S.shop_id 
			JOIN ready_stock R ON R.id = S.product_id 
			JOIN sold_stock B ON B.shop_stock_id
			WHERE  YEAR(date) = ".$_POST['year']."  AND MONTH(date) = ".$_POST['month']			
			);//

			if($soldStocks ){
				$this->status_code = 200;
				return array('status' => true,'soldStocks' => $soldStocks);
			}else{
				return array('status' => true,'soldStocks' => $soldStocks);
			}
			
		}
		
	}
	
	/*public function get()
    {
		$validator = new Validator();
		$errors = [];
		$message = null;
		$_POST = (array) json_decode(file_get_contents("php://input", true));
		if(isset($_POST['month']) && (trim($_POST['month']) != '') ) {
			if($validator->regex_match($_POST['month'],'/^[0-9]{2}$/')){
				$month = $_POST['month'];
			}else{
				$errors['month'] = "Invalid month Formate Year Look Like mm";
			}
		}else {
			$message = 'Required field missing';
			$errors['month'] = "Month is Require";
		}
		
		if(isset($_POST['year']) && (trim($_POST['year']) != '') ) {
			if($validator->regex_match($_POST['year'],'/^[0-9]{4}$/')){
				$year = $_POST['year'];
			}else{
				$errors['year'] = "Invalid Year Formate Year Look Like yyyy";
			}
		}else {
			$message = 'Required field missing';
			$errors['year'] = "Year is Require";
		}
		if(!empty($errors)){
			$this->status_code = 400;
			return ['status' => false , 'message'  => $message , 'errors'  => $errors];

		}else{
			// deliver_to get from shop id 
			//name get from readyStock product_name
			$deliveredStocks = DB::query("
			SELECT S.id,D.name as deliver_to,R.product_name,R.price,R.color,S.quantity,S.delivery_status,DATE_FORMAT(date(S.delivery_date), '%d/%m/%Y') as delivery_date,DATE_FORMAT(date(S.received_date), '%d/%m/%Y') as received_date,S.comment
			FROM `shop_stock` S 
			JOIN shop D On D.id = S.shop_id 
			JOIN ready_stock R On R.id = S.product_id
			WHERE  YEAR(date) = ".$_POST['year']."  AND MONTH(date) = ".$_POST['month']			
			);//

			if($deliveredStocks ){
				$this->status_code = 200;
				return array('status' => true,'deliveredStocks' => $deliveredStocks);
			}
		
		}
		
	}
	public function getShopStock()
    {
		$validator = new Validator();
		$errors = [];
		$message = null;
		$_POST = (array) json_decode(file_get_contents("php://input", true));
		if(isset($_POST['month']) && (trim($_POST['month']) != '') ) {
			if($validator->regex_match($_POST['month'],'/^[0-9]{2}$/')){
				$month = $_POST['month'];
			}else{
				$errors['month'] = "Invalid month Formate Year Look Like mm";
			}
		}else {
			$message = 'Required field missing';
			$errors['month'] = "Month is Require";
		}
		
		if(isset($_POST['year']) && (trim($_POST['year']) != '') ) {
			if($validator->regex_match($_POST['year'],'/^[0-9]{4}$/')){
				$year = $_POST['year'];
			}else{
				$errors['year'] = "Invalid Year Formate Year Look Like yyyy";
			}
		}else {
			$message = 'Required field missing';
			$errors['year'] = "Year is Require";
		}
		if(!empty($errors)){
			$this->status_code = 400;
			return ['status' => false , 'message'  => $message , 'errors'  => $errors];

		}else{
			// deliver_to get from shop id 
			//name get from readyStock product_name
			$deliveredStocks = DB::query("
			SELECT S.id,D.name as shop_name,R.product_name,R.price,R.color,R.unit,S.quantity,DATE_FORMAT(date(S.delivery_date), '%d/%m/%Y') as delivery_date,DATE_FORMAT(date(S.received_date), '%d/%m/%Y') as received_date,S.comment
			FROM `shop_stock` S 
			JOIN shop D On D.id = S.shop_id 
			JOIN ready_stock R On R.id = S.product_id
			WHERE  YEAR(date) = ".$_POST['year']."  AND MONTH(date) = ".$_POST['month']			
			);//

			if($deliveredStocks ){
				$this->status_code = 200;
				return array('status' => true,'deliveredStocks' => $deliveredStocks);
			}
		
		}
		
	}
	public function receiverd()
    {
		$validator = new Validator();
		$errors = [];
		$message = null;
		$_POST = (array) json_decode(file_get_contents("php://input", true));
		
		if(isset($_POST['shop_stock_id']) && (trim($_POST['shop_stock_id']) != '') ) {
			if($validator->integer($_POST['shop_stock_id'])){
				
				$shop_stock = DB::query("SELECT * FROM $this->table WHERE id = %i AND delivery_status = 0 ",$_POST['shop_stock_id']);
				if(!$shop_stock){
					
					$errors['shop_stock_id'] = "Shop Stock not exist Or Already Received";
				}else{
					$shop_stock = _row_array($shop_stock);
					$is_shop_owner = DB::query("SELECT * FROM user_shop WHERE shop_id = %i AND user_id = %i ",$shop_stock['shop_id'],$this->received_by);
					if($is_shop_owner){
						$shop_stock_id=$_POST['shop_stock_id'];
					}else{
						$errors['shop_stock_id'] = "Only  Shop Owner Can accept";
					}
					
				}
			}else{
				$errors['shop_stock_id'] = "Shop Stock Require Integer";
			}	
		}
		else {
			$message = 'Required field missing';
			$errors['shop_stock_id'] = "Shop Stock Is Require";
		}
		if(!empty($errors)){
			$this->status_code = 400;
			return ['status' => false , 'message'  => $message , 'errors'  => $errors];

		}else{
			DB::update($this->table, array(
				'received_by' => 1,
				'received_date' => date('Y-m-d H:i:s'),
				'delivery_status' => 1
			 ), "id=%i", $shop_stock_id);
			return array('status' => true, 'message' => 'Successfully Shop Stock Updated');

		}
		
	}*/
  }