<?php
require_once '../library/Validator.php';
require_once '../notifications/SendNotification.php';
include_once '../config/constants.php';
class DemandStock
{
    private $table = 'on_demand';

    public $id;
    public $product_id;
    public $quantity;
    public $priority;
    public $demanded_date;
    public $demanded_shop;
    public $demanded_by;    // manual add


    //raw_stock user entry
    //shop stock accept it..
    public function makeDemand()
    {
        //product_name, price, quantity, unit, date, color, comment, user_id
        $validator = new Validator();
        $sendNotification = new SendNotification();
        $errors = [];
        $message = null;
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        if (isset($_POST['demanded_shop_id']) && (trim($_POST['demanded_shop_id']) != '')) {
            if ($validator->integer($_POST['demanded_shop_id'])) {

                $shop_exist = DB::query("SELECT * FROM shop WHERE id = %i", $_POST['demanded_shop_id']);
                $shop_exist = _row_array($shop_exist);
                if (!$shop_exist) {
                    $errors['demanded_shop_id'] = "Shop not exist";
                } else {
                    $shop_id = $_POST['demanded_shop_id'];
                }

            } else {
                $errors['demanded_shop_id'] = "Shop Require Integer";
            }
        } else {
            $message = 'Required field missing';
            $errors['demanded_shop_id'] = "Shop Is Require";
        }


        if (isset($_POST['quantity']) && (trim($_POST['quantity']) != '')) {
            if ($validator->integer($_POST['quantity'])) {
                $quantity = $_POST['quantity'];
            } else {
                $errors['quantity'] = "Quantity Require Integer";
            }

        } else {
            $message = 'Required field missing';
            $errors['quantity'] = "Quantity Is Require";
        }
        if (isset($_POST['product_id']) && (trim($_POST['product_id']) != '')) {
            if ($validator->integer($_POST['product_id'])) {

                $product_exist = DB::query("SELECT * FROM ready_stock WHERE id = %i AND quantity >= %i ", $_POST['product_id'], $quantity);
                $product_exist = _row_array($product_exist);
                if (!$product_exist) {
                    $message = 'Product not exist Or Less Quantity';
                    $errors['product_id'] = "Product not exist Or Less Quantity";
                } else {
                    $product_id = $_POST['product_id'];
                }
            } else {
                $errors['product_id'] = "Shop Require Integer";
            }
        } else {
            $message = 'Required field missing';
            $errors['product_id'] = "Product Is Require";
        }

        if (isset($_POST['comment']) && (trim($_POST['comment']) != '')) {
            $comment = $_POST['comment'];
        } else {
            $comment = "";
        }

        if (isset($_POST['priority']) && (trim($_POST['priority']) != '')) {
            $priority = $_POST['priority'];
        } else {
            $message = 'Required field missing';
            $errors['priority'] = "priority Is Require";
        }

        if(isset($_POST['shop_name']) && (trim($_POST['shop_name']) != '') ) {
            $shop_name = $_POST['shop_name'];
        }else {
            $message = 'Required field missing';
            $errors['shop_name'] = "Shop Name Is Require";
        }

        if (!empty($errors)) {
            $this->status_code = 400;
            return ['status' => false, 'message' => $message, 'errors' => $errors];

        } else {

            $demand_data = [

                'product_id' => $product_id,
                'demanded_quantity' => $quantity,
                'priority' => $priority,
                'demanded_shop' => $shop_id,
                'demanded_by' => $this->demanded_by,
                'demanded_date' => date('Y-m-d H:i:s')
            ];
            //quantity subtract from ready stock
            //add in shop_stock
            //delivery_status 0

            //when shop stock user received
            $added = DB::insert($this->table, $demand_data);
            if ($added) {
                $demand_id = DB::insertId();

                $title = 'Product Delivery On The Way';
                $bodyMessage = "A new product is demanded. Demanded shop name: " . $shop_name;

                $sendNotification->sendToTopic($title, $bodyMessage, ROLE_ADMIN, NOTIFICATION_ACTION, NOTIFICATION_DESTINATION_DEMANDSTOCK, $shop_id , $shop_name );
                $sendNotification->sendToTopic($title, $bodyMessage, ROLE_DISTRIBUTOR, NOTIFICATION_ACTION, NOTIFICATION_DESTINATION_DEMANDSTOCK,$shop_id , $shop_name);


                return array('status' => true, 'demand_id' => $demand_id, 'message' => 'Demand successful');
            }
        }


    }


    public function completeDemand()
    {
        //product_name, price, quantity, unit, date, color, comment, user_id
        $validator = new Validator();
        $errors = [];
        $message = null;
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        if (isset($_POST['demand_id']) && (trim($_POST['demand_id']) != '')) {
            if ($validator->integer($_POST['demand_id'])) {

                $shop_exist = DB::query("SELECT * FROM on_demand WHERE id = %i", $_POST['demand_id']);
                $shop_exist = _row_array($shop_exist);
                if (!$shop_exist) {
                    $errors['demand_id'] = "Shop not exist";
                } else {
                    $shop_id = $_POST['demand_id'];
                }

            } else {
                $errors['demand_id'] = "Shop Require Integer";
            }
        } else {
            $message = 'Required field missing';
            $errors['demand_id'] = "Shop Is Require";
        }




        if (!empty($errors)) {
            $this->status_code = 400;
            return ['status' => false, 'message' => $message, 'errors' => $errors];

        } else {


            //quantity subtract from ready stock
            //add in shop_stock
            //delivery_status 0
            $delete_query = DB::query("DELETE FROM on_demand WHERE id = %i", $_POST['demand_id']);
            //when shop stock user received
            if($delete_query){
                return array('status' => true,  'message' => 'Demand successful');
            }else{
                return array('status' => true,  'message' => 'Demand successful');
            }

        }


    }



    public function getDemandedStocks()
    {
        //product_name, price, quantity, unit, date, color, comment, user_id
        $validator = new Validator();
        $errors = [];
        $message = null;
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        if (isset($_POST['shop_id']) && (trim($_POST['shop_id']) != '')) {
            if ($_POST['shop_id'] > 0) {

                $shop_exist = DB::query("SELECT * FROM shop WHERE id = %i", $_POST['shop_id']);
                $shop_exist = _row_array($shop_exist);
                if (!$shop_exist) {
                    $errors['shop_id'] = "Shop not exist";
                } else {
                    $shop_id = $_POST['shop_id'];
                }

            } else {
                $shop_id = $_POST['shop_id'];
            }
        } else {
            $message = 'Required field missing';
            $errors['demand_id'] = "Shop Is Require";
        }




        if (!empty($errors)) {
            $this->status_code = 400;
            return ['status' => false, 'message' => $message, 'errors' => $errors];

        } else {



            if($shop_id == 0){
                $demanded_stocks = DB::query("SELECT D.id, R.product_name, R.unit, R.color, D.demanded_quantity, D.priority, D.demanded_date, S.name as demanded_shop_name 
                FROM ready_stock R JOIN on_demand D ON R.id = D.product_id JOIN shop S ON S.id = D.demanded_shop");
            }else{
                $demanded_stocks = DB::query("SELECT D.id, R.product_name, R.unit, R.color, D.demanded_quantity, D.priority, D.demanded_date, S.name as demanded_shop_name 
                FROM ready_stock R JOIN on_demand D ON R.id = D.product_id JOIN shop S ON S.id = D.demanded_shop WHERE D.demanded_shop = %i", $shop_id);
            }

            if ($demanded_stocks) {
                //$this->status_code = 200;
                return array('status' => true, 'demandedStocks' => $demanded_stocks);
            } else {
                //$this->status_code = 200;
                return array('status' => true, 'demandedStocks' => []);
            }

        }


    }
}