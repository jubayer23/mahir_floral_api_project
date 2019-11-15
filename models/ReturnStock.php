<?php 
require_once '../library/Validator.php';
require_once '../notifications/SendNotification.php';
include_once '../config/constants.php';
  class ReturnStock {
    private $return_table = 'return_stock';
      private $shop_stock_table = 'shop_stock';

    public $id;
    public $shop_stock_id;
    public $shop_id;
    public $product_id;
    public $quantity;
    public $return_by_id;
    public $received_by_id;
	public $comment;
	public $return_date;	// manual add
	public $received_date;	// manual update 
	public $status;// manual update

	/*
	 * shop_stock_id
	 * quantity
	 * return_date
	 * return_by_id
	 * product_id
	 * status
	 * */
	public function entry_return_stock(){
        $errors = [];
        $message = null;
        $_POST = (array) json_decode(file_get_contents("php://input", true));


        if(isset($_POST['shop_stock_id']) && (trim($_POST['shop_stock_id']) != '') && isset($_POST['quantity']) && (trim($_POST['quantity']) != '')  ) {
            $shop_stock_id = $_POST['shop_stock_id'];


            $shop_stock = DB::query("SELECT * FROM $this->shop_stock_table WHERE id = %i AND quantity >= %i",$_POST['shop_stock_id'], $_POST['quantity']);
            if(!$shop_stock){

                $message = "This shop Stock item does not exist Or Already Returned";
                $errors['shop_stock_id'] = "This shop Stock item does not exist Or Already Received";
            }

        }else {
            $message = 'Required field missing';
            $errors['shop_stock_id'] = "Shop Name Is Require";
        }

        if(isset($_POST['shop_id']) && (trim($_POST['shop_id']) != '') ) {
            $shop_id = $_POST['shop_id'];
        }else {
            $message = 'Required field missing';
            $errors['shop_id'] = "shop_id Is Require";
        }

        if(isset($_POST['quantity']) && (trim($_POST['quantity']) != '') ) {
            $quantity = $_POST['quantity'];
        }else {
            $message = 'Required field missing';
            $errors['quantity'] = "quantity Is Require";
        }



        if(isset($_POST['product_id']) && (trim($_POST['product_id']) != '') ) {
            $product_id = $_POST['product_id'];
        }else {
            $message = 'Required field missing';
            $errors['product_id'] = "product_id Is Require";
        }

        if(isset($_POST['comment']) && (trim($_POST['comment']) != '') ) {
            $comment = $_POST['comment'];
        }else {
            $comment = 'No Comment';
        }


        if(!empty($errors)){

            return ['status' => false , 'message'  => $message , 'errors'  => $errors];

        }else{

            $return_data = [
                'shop_stock_id' => $shop_stock_id,
                'shop_id' => $shop_id,
                'quantity' => $quantity,
                'return_date' => date('Y-m-d H:i:s'),
                'return_by_id' => $this->return_by_id,
                'product_id' => $product_id,
                'status' => 0,
                'comment' => $comment
            ];
            //quantity subtract from ready stock
            //add in shop_stock
            //delivery_status 0

            //when shop stock user received
            $added = DB::insert($this->return_table,$return_data);


            if($added){
                $return_id = DB::insertId();

                $is_update = DB::query("UPDATE shop_stock SET quantity= quantity - %i WHERE id=%i", $quantity, $shop_stock_id);
                //$this->status_code = 201;

                //$title = 'Product Delivery On The Way';
               // $bodyMessage = "Distributor received a product. Received from " . $username . ". Will deliver to " . $shop_name;

               // $sendNotification->sendToTopic($title, $bodyMessage, ROLE_ADMIN, NOTIFICATION_ACTION, NOTIFICATION_DESTINATION_INCOMINGSTOCK, $shop_id , $shop_name);


                return array('status' => true,'return_stock_id' => $return_id, 'message' => 'Stock successfully returned!');
            }

        }
    }


    public function receiveReturnStock($username){
        $errors = [];
        $message = null;
        $_POST = (array) json_decode(file_get_contents("php://input", true));

        if(isset($_POST['return_stock_id']) && (trim($_POST['return_stock_id']) != '') ) {

            $return_stock = DB::query("SELECT * FROM $this->return_table WHERE id = %i AND status = 0 ",$_POST['return_stock_id']);
            if(!$return_stock){

                $message = "This Return Stock item does not exist Or Already Received";
                $errors['return_stock_id'] = "This return_stock item does not exist Or Already Received";
            }
        }else {
            $message = 'Required field missing';
            $errors['return_stock_id'] = "return_stock_id Stock Is Require";
        }


        if(!empty($errors)){
            //$this->status_code = 400;
            return ['status' => false , 'message'  => $message , 'errors'  => $errors];

        }else{

            $return_stock_id = $_POST['return_stock_id'];
            DB::update($this->return_table, array(
                'received_by_id' => $this->received_by_id,
                'received_date' => date('Y-m-d H:i:s'),
                'status' => 1
            ), "id=%i", $return_stock_id);


            //$title = 'Product Received';
            //$bodyMessage = $shop_name. "received a new product. Received by: ";

           // $sendNotification->sendToTopic($title, $bodyMessage, ROLE_ADMIN, NOTIFICATION_ACTION, NOTIFICATION_DESTINATION_SHOPSTOCK, $shop_stock_id, $shop_name);



            return array('status' => true, 'message' => 'Successfully Return Stock Updated');

        }
    }


      public function getReturnStocks(){
          $validator = new Validator();
          $errors = [];
          $message = null;
          $_POST = (array) json_decode(file_get_contents("php://input", true));

          $filter_by_shop_id = '';

          if(isset($_POST['filter_by_shop_id']) && (trim($_POST['filter_by_shop_id']) != '') ) {
              $filter_by_shop_id = $_POST['filter_by_shop_id'];
              /*if($validator->integer($_POST['filter_by_shop_id'])){

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
              }	*/
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
              $returnStocks = DB::query("
			SELECT RS.id,SHP.name as shop_name,RS.product_name,RS.price,RS.color,RS.unit,RTS.quantity,DATE_FORMAT(date(RTS.return_date), '%d/%m/%Y') as return_date,DATE_FORMAT(date(RTS.received_date), '%d/%m/%Y') as received_date,RTS.status
			FROM `return_stock` RTS 
			JOIN shop SHP On RTS.shop_id = SHP.id 
			JOIN ready_stock RS On RS.id = RTS.product_id
			WHERE RTS.status = 0 AND YEAR(RTS.return_date) = ".$_POST['year']."  AND MONTH(RTS.return_date) = ".$_POST['month']
              );//

              if($returnStocks ){
                 // $this->status_code = 200;
                  return array('status' => true,'returnStocks' => $returnStocks);
              }else{
                 // $this->status_code = 400;
                  $message = "no data";
                  $shopStocks = [];
                  return ['status' => false ,  'returnStocks'  => $returnStocks];
              }

          }

      }
  }