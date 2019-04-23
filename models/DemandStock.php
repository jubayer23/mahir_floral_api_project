<?php
require_once '../library/Validator.php';

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



                return array('status' => true, 'demand_id' => $demand_id, 'message' => 'Demand successful');
            }
        }


    }
}