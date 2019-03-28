<?php 
	require_once '../library/Validator.php';
  class RawStock {
    private $table = 'raw_stock';

    public $id;
    public $product_name;
    public $quantity;
    public $unit;
    public $received_date;
    public $color;
	public $comment;
	public $added_by;
	public $is_admin;
	
	public function add()
    {
		$validator = new Validator();
		$errors = [];
		$message = null;
		$_POST = (array) json_decode(file_get_contents("php://input", true));
		
		if(isset($_POST['product_name']) && (trim($_POST['product_name']) != '') ) {
			$product_name=$_POST['product_name'];
		}
		else {
			$message = 'Required field missing';
			$errors['product_name'] = "Product Name Is Require";
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
		if(isset($_POST['unit']) && (trim($_POST['unit']) != '') ) {
				$unit=$_POST['unit'];
		}
		else {
			$message = 'Required field missing';
			$errors['unit'] = "Unit Is Require";
		}
		if(isset($_POST['color']) && (trim($_POST['color']) != '') ) {
				$color = $_POST['color'];
		}else {
			$message = 'Required field missing';
			$errors['color'] = "Color Is Require";
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
				'product_name' => html_escape($product_name),
				'quantity' => $quantity,
				'unit' => html_escape($unit),
				'color' => html_escape($color),
				'comment' => html_escape($comment),
				'added_by' => $this->added_by
			];				
			$added = DB::insert($this->table,$shop_data);
			if($added){
				$raw_stock_id = DB::insertId();
				$this->status_code = 201;
				return array('status' => true,'raw_stock_id' => $raw_stock_id, 'message' => 'Raw Stock Successfully Added');
			}
		}
		
				
	}
	public function get(){
		
		$validator = new Validator();
		$errors = [];
		$message = null;
		$_POST = (array) json_decode(file_get_contents("php://input", true));
		//var_dump($_POST);die;
		if(isset($_POST['month']) && (trim($_POST['month']) != '') ) {
			if($validator->regex_match($_POST['month'],'/^1[0-2]|[1-9]$/')){
				$month = $_POST['month'];
			}else{
				$errors['month'] = "Invalid month Formate Month Look Like mm Or m ";
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
			//echo "SELECT id, product_name as name , quantity, unit, DATE_FORMAT(date(received_date), '%d/%m/%Y') , color, comment FROM $this->table WHERE  YEAR(received_date) = %i  AND MONTH(received_date) = %i ",$year,$month;die;
			//$raw_stock = DB::query("SELECT id, product_name as name , quantity, unit, DATE_FORMAT(date(received_date), '%d/%m/%Y') as date, color, comment FROM $this->table WHERE  YEAR(received_date) = %i  AND MONTH(received_date) = %i ",$year,$month);
			if(!$this->is_admin){
				$raw_stock = DB::query("SELECT id, product_name as name , quantity, unit, DATE_FORMAT(date(received_date), '%d/%m/%Y') as date, color, comment FROM $this->table WHERE  YEAR(received_date) =  ".$_POST['year']." AND MONTH(received_date) = ".$_POST['month']." AND added_by = ".$this->added_by);
			}else{
				$raw_stock = DB::query("SELECT id, product_name as name , quantity, unit, DATE_FORMAT(date(received_date), '%d/%m/%Y') as date, color, comment FROM $this->table WHERE  YEAR(received_date) =  ".$_POST['year']." AND MONTH(received_date) = ".$_POST['month']);
				//var_dump($raw_stock);die;
			}
			if($raw_stock ){
				$this->status_code = 200;
				return array('status' => true,'rawStock' => $raw_stock);
			}else{
				$this->status_code = 200;
				return array('status' => true,'rawStock' => []);
			}
		
		}
					
		
	}
	public function update()
    {
		
	}
	public function delete()
    {
		
	}
  }