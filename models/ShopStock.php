<?php 
	require_once '../library/Validator.php';
  class ShopStock {
    private $table = 'shop_stock';

    public $id;
    public $shop_id;
    public $product_id;
    public $quantity;
    public $delivery_by;
    public $received_by;
	public $comment;
	public $delivery_date;	// manual add 
	public $received_date;	// manual update 
	public $delivery_status;// manual update 
	public $status_code;
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
		
		if(isset($_POST['shop_id']) && (trim($_POST['shop_id']) != '') ) {
			if($validator->integer($_POST['shop_id'])){
				
				$shop_exist = DB::query("SELECT * FROM shop WHERE id = %i",$_POST['shop_id']);
				$shop_exist = _row_array($shop_exist);
				if(!$shop_exist){
					$errors['shop_id'] = "Shop not exist";
				}else{
					$shop_id=$_POST['shop_id'];
				}
				
			}else{
				$errors['shop_id'] = "Shop Require Integer";
			}	
		}
		else {
			$message = 'Required field missing';
			$errors['shop_id'] = "Shop Is Require";
		}
	
		
		if(isset($_POST['quantity']) && (trim($_POST['quantity']) != '') ) {
			if($validator->integer($_POST['quantity'])){
				$quantity=$_POST['quantity'];
			}else{
				$errors['quantity'] = "Quantity Require Integer";
			}
			
		}
		else {
			$message = 'Required field missing';
			$errors['quantity'] = "Quantity Is Require";
		}
			if(isset($_POST['product_id']) && (trim($_POST['product_id']) != '') ) {
			if($validator->integer($_POST['product_id'])){
				
				$product_exist = DB::query("SELECT * FROM ready_stock WHERE id = %i AND quantity >= %i ",$_POST['product_id'],$quantity);
				$product_exist = _row_array($product_exist);
				if(!$product_exist){
                    $message = 'Product not exist Or Less Quantity';
					$errors['product_id'] = "Product not exist Or Less Quantity";
				}else{
					$product_id=$_POST['product_id'];
				}
			}else{
				$errors['product_id'] = "Shop Require Integer";
			}	
		}
		else {
			$message = 'Required field missing';
			$errors['product_id'] = "Product Is Require";
		}
		
		if(isset($_POST['comment']) && (trim($_POST['comment']) != '') ) {
				$comment = $_POST['comment'];
		}else {
			$message = 'Required field missing';
			$errors['comment'] = "Comment Is Require";
		}
		
		if(!empty($errors)){
			$this->status_code = 400;
			return ['status' => false , 'message'  => $message , 'errors'  => $errors];

		}else{

			$shop_data = [
				'shop_id' => $shop_id,
				'product_id' => $product_id,
				'quantity' => $quantity,
				'delivery_status' => 0,
				'delivery_by' => $this->delivery_by,
				'comment' => html_escape($comment),
				'delivery_date' => date('Y-m-d H:i:s')
			];
			//quantity subtract from ready stock
			//add in shop_stock
			//delivery_status 0
			
			//when shop stock user received  			
			$added = DB::insert($this->table,$shop_data);
			if($added){
			    $shop_stock_id = DB::insertId();

				$is_update = DB::query("UPDATE ready_stock SET quantity= quantity - %i WHERE id=%i", $quantity, $product_id);
				$this->status_code = 201;

				return array('status' => true,'shop_stock_id' => $shop_stock_id, 'message' => 'Ready To Deliver Shop Stocker');
			}
		}
		
				
	}
	
	public function getDeliveredStocks()
    {
		$validator = new Validator();
		$errors = [];
		$message = null;
		$_POST = (array) json_decode(file_get_contents("php://input", true));
		if(isset($_POST['month']) && (trim($_POST['month']) != '') ) {
			if($validator->regex_match($_POST['month'],'/^1[0-2]|[1-9]$/')){
				$month = $_POST['month'];
			}else{
				$errors['month'] = "Invalid month Formate Month Look Like m";
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
		
		if(isset($_POST['filter_by_shop_id']) && (trim($_POST['filter_by_shop_id']) != '') ) {
			if($validator->integer($_POST['filter_by_shop_id'])){
				
				if(!$this->is_admin){
					$is_shop_owner = DB::query("SELECT * FROM user_shop WHERE shop_id = %i AND user_id = %i ",$_POST['filter_by_shop_id'],$this->received_by);
					if(!$is_shop_owner){
					$errors['filter_by_shop_id'] = "Only Owner Can Request";
					}else{
						$filter_by_shop_id=$_POST['filter_by_shop_id'];
					}
				}else{
					$shop_exist = DB::query("SELECT * FROM user_shop WHERE shop_id = %i ",$_POST['filter_by_shop_id']);
					if(!$shop_exist){
					$errors['filter_by_shop_id'] = "Shop not Exist";
					}else{
						//$is_shop_owner = _row_array($is_shop_owner);
						$filter_by_shop_id=$_POST['filter_by_shop_id'];
					}

				}

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
			// deliver_to get from shop id 
			//name get from readyStock product_name
			$shopStocks = DB::query("
			SELECT S.id,D.name as shop_name,R.product_name,R.price,R.color,R.unit,S.quantity,DATE_FORMAT(date(S.delivery_date), '%d/%m/%Y') as delivery_date,DATE_FORMAT(date(S.received_date), '%d/%m/%Y') as received_date,S.delivery_status,S.comment
			FROM `shop_stock` S 
			JOIN shop D On D.id = S.shop_id 
			JOIN ready_stock R On R.id = S.product_id
			WHERE  YEAR(date) = ".$_POST['year']."  AND MONTH(date) = ".$_POST['month']." AND S.shop_id = ".$_POST['filter_by_shop_id']
			);//

			if($shopStocks ){
				$this->status_code = 200;
				return array('status' => true,'shopStocks' => $shopStocks);
			}else{
                $this->status_code = 400;
                $message = "no data";
                $shopStocks = [];
                return ['status' => false ,  'shopStocks'  => $shopStocks];
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
		
	}
  }