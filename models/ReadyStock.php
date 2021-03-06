<?php
require_once '../library/Validator.php';
require_once '../notifications/SendNotification.php';
include_once '../config/constants.php';
class ReadyStock
{
    private $table = 'ready_stock';

    public $id;
    public $product_name;
    public $quantity;
    public $unit;
    public $price;
    public $date;
    public $color;
    public $comment;
    public $added_by;
    public $is_admin;

    public function add($username)
    {
        //product_name, price, quantity, unit, date, color, comment, user_id
        $validator = new Validator();
        $sendNotification = new SendNotification();
        $errors = [];
        $message = null;
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        if (isset($_POST['product_name']) && (trim($_POST['product_name']) != '')) {
            $product_name = $_POST['product_name'];
        } else {
            $message = 'Required field missing';
            $errors['product_name'] = "Product Name Is Require";
        }
        if (isset($_POST['price']) && (trim($_POST['price']) != '')) {
            if ($validator->validate_float($_POST['price'])) {
                $price = $_POST['price'];
            } else {
                $errors['price'] = "Invalid Price Type";
            }

        } else {
            $message = 'Required field missing';
            $errors['price'] = "Price Is Require";
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
        if (isset($_POST['unit']) && (trim($_POST['unit']) != '')) {
            $unit = $_POST['unit'];
        } else {
            $message = 'Required field missing';
            $errors['unit'] = "Unit Is Require";
        }
        if (isset($_POST['color']) && (trim($_POST['color']) != '')) {
            $color = $_POST['color'];
        } else {
            $message = 'Required field missing';
            $errors['color'] = "Color Is Require";
        }

        if (isset($_POST['comment']) && (trim($_POST['comment']) != '')) {
            $comment = $_POST['comment'];
        } else {
            $message = 'Required field missing';
            $errors['comment'] = "Comment Is Require";
        }

        if (!empty($errors)) {
            $this->status_code = 400;
            return ['status' => false, 'message' => $message, 'errors' => $errors];

        } else {

            $shop_data = [
                'product_name' => html_escape($product_name),
                'quantity' => $quantity,
                'price' => $price,
                'unit' => html_escape($unit),
                'color' => html_escape($color),
                'comment' => html_escape($comment),
                'added_by' => $this->added_by,
            ];
            $added = DB::insert($this->table, $shop_data);
            if ($added) {
                $ready_stock_id = DB::insertId();
                $this->status_code = 201;

                $title = 'Product Ready';
                $bodyMessage = $product_name ." is ready for delivery. Added by " . $username;

                $sendNotification->sendToTopic($title, $bodyMessage, ROLE_ADMIN, NOTIFICATION_ACTION, NOTIFICATION_DESTINATION_READYSTOCK, '', '');


                return array('status' => true, 'ready_stock_id' => $ready_stock_id, 'message' => 'Ready Stock Successfully Added');
            }
        }


    }

    public function deleteReadyStock(){
        $errors = [];
        $message = null;
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        if (isset($_POST['ready_stock_id']) && (trim($_POST['ready_stock_id']) != '')) {

            $ready_stock_exist = DB::query("SELECT * FROM ready_stock WHERE id = %i",$_POST['ready_stock_id']);
            $ready_stock_exist = _row_array($ready_stock_exist);
            if(!$ready_stock_exist){
                $errors['ready_stock_id'] = "Ready Stock not exist";
            }else{
                $ready_stock_id = $_POST['ready_stock_id'];
            }


        } else {
            $message = 'Required field missing';
            $errors['ready_stock_id'] = "ready_stock_id Is Require";
        }

        if (!empty($errors)) {
            //$this->status_code = 400;
            return ['status' => false, 'message' => $message, 'errors' => $errors];

        } else {
            /*$delete = DB::query("DELETE FROM ready_stock WHERE id = %i",$ready_stock_id);
            //var_dump($delete);
            if($delete){
                return array('status' => true, 'ready_stock_id' => $ready_stock_id, 'message' => 'Ready Stock Successfully Deleted');
            }else{



            }*/


            $update = DB::query("UPDATE `ready_stock` SET  `quantity`= -1 WHERE id =%i",$ready_stock_id);

            if($update){
                return array('status' => true, 'ready_stock_id' => $ready_stock_id, 'message' => 'Ready Stock Successfully Deleted');
            }else{
                return array('status' => false, 'ready_stock_id' => $ready_stock_id, 'message' => 'Ready Stock Delete Error from server');
            }


        }
    }

    public function updateReadyStock(){
        $errors = [];
        $message = null;
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        if (isset($_POST['ready_stock_id']) && (trim($_POST['ready_stock_id']) != '')) {

            $ready_stock_exist = DB::query("SELECT * FROM ready_stock WHERE id = %i",$_POST['ready_stock_id']);
            $ready_stock_exist = _row_array($ready_stock_exist);
            if(!$ready_stock_exist){
                $errors['ready_stock_id'] = "Ready Stock not exist";
            }else{
                $ready_stock_id = $_POST['ready_stock_id'];
            }


        } else {
            $message = 'Required field missing';
            $errors['ready_stock_id'] = "ready_stock_id Is Require";
        }



        if(isset($_POST['product_name']) && (trim($_POST['product_name']) != '')){
            $product_name = $_POST['product_name'];
        }else{
            $message = 'Required field missing';
            $errors['error'] = "Product name field Is Require";
        }


        if(isset($_POST['quantity']) && (trim($_POST['quantity']) != '')){
            $quantity = $_POST['quantity'];
        }else{
            $message = 'Required field missing';
            $errors['quantity'] = "Quantity field Is Require";
        }

        if(isset($_POST['price']) && (trim($_POST['price']) != '')){
            $price = $_POST['price'];
        }else{
            $message = 'Required field missing';
            $errors['price'] = "Price field Is Require";
        }

        if (!empty($errors)) {
            //$this->status_code = 400;
            return ['status' => false, 'message' => $message, 'errors' => $errors];

        } else {
            $update = DB::query("UPDATE `ready_stock` SET `product_name`= '$product_name', `quantity`= '$quantity', `price`= '$price'   WHERE id =%i",$ready_stock_id);
            //var_dump($delete);
            if($update){
                return array('status' => true, 'ready_stock_id' => $ready_stock_id, 'message' => 'Ready Stock Successfully updated');
            }else{
                return array('status' => false, 'ready_stock_id' => $ready_stock_id, 'message' => 'Ready Stock update Error from server');
            }


        }

    }

    public function getshops()
    {
        $shops = DB::query("SELECT * FROM shop");
        if ($shops) {
            $this->status_code = 200;
            return array('status' => true, 'shops' => $shops);
        }
    }

    public function get()
    {


        $validator = new Validator();
        $errors = [];
        $message = null;
        $_POST = (array)json_decode(file_get_contents("php://input", true));
        if (isset($_POST['month']) && (trim($_POST['month']) != '')) {
            if ($validator->regex_match($_POST['month'], '/^[0-9]{2}$/')) {
                $month = $_POST['month'];
            } else {
                $errors['month'] = "Invalid month Formate Year Look Like mm";
            }
        } else {
            $message = 'Required field missing';
            $errors['month'] = "Month is Require";
        }

        if (isset($_POST['year']) && (trim($_POST['year']) != '')) {
            if ($validator->regex_match($_POST['year'], '/^[0-9]{4}$/')) {
                $year = $_POST['year'];
            } else {
                $errors['year'] = "Invalid Year Formate Year Look Like yyyy";
            }
        } else {
            $message = 'Required field missing';
            $errors['year'] = "Year is Require";
        }
        if (!empty($errors)) {
            $this->status_code = 400;
            return ['status' => false, 'message' => $message, 'errors' => $errors];

        } else {

            //$ready_stock = DB::query("SELECT R.id, R.product_name as name , R.quantity,R.price, R.unit, DATE_FORMAT(date(R.date), '%d/%m/%Y') as received_date, R.color, R.comment, U.name as added_by FROM `ready_stock` R JOIN `users` U ON U.id = R.added_by WHERE  YEAR(date) = " . $_POST['year'] . "  AND MONTH(date) = " . $_POST['month']);//

            $ready_stock = DB::query("SELECT R.id, R.product_name as name , R.quantity,R.price, R.unit, R.date as received_date, R.color, R.comment, U.name as added_by FROM `ready_stock` R JOIN `users` U ON U.id = R.added_by WHERE  YEAR(R.date) = " . $_POST['year'] . "  AND MONTH(R.date) = " . $_POST['month'] . " AND R.quantity >= 0");//


            if ($ready_stock) {
                $this->status_code = 200;
                return array('status' => true, 'readyStocks' => $ready_stock);
            } else {
                $this->status_code = 200;
                return array('status' => true, 'readyStocks' => []);
            }

        }

    }


    public function getReadyStockForDemand()
    {


        $validator = new Validator();
        $errors = [];
        $message = null;
        $_POST = (array)json_decode(file_get_contents("php://input", true));
        if (isset($_POST['month']) && (trim($_POST['month']) != '')) {
            if ($validator->regex_match($_POST['month'], '/^[0-9]{2}$/')) {
                $month = $_POST['month'];
            } else {
                $errors['month'] = "Invalid month Formate Year Look Like mm";
            }
        } else {
            $message = 'Required field missing';
            $errors['month'] = "Month is Require";
        }

        if (isset($_POST['year']) && (trim($_POST['year']) != '')) {
            if ($validator->regex_match($_POST['year'], '/^[0-9]{4}$/')) {
                $year = $_POST['year'];
            } else {
                $errors['year'] = "Invalid Year Formate Year Look Like yyyy";
            }
        } else {
            $message = 'Required field missing';
            $errors['year'] = "Year is Require";
        }

        if (isset($_POST['shop_id']) && (trim($_POST['shop_id']) != '')) {

            $shop_id = $_POST['shop_id'];

        } else {
            $message = 'Required field missing';
            $errors['shop_id'] = "Year is Require";
        }

        if (!empty($errors)) {
            $this->status_code = 400;
            return ['status' => false, 'message' => $message, 'errors' => $errors];

        } else {

            $ready_stock = DB::query("SELECT R.id, R.product_name as name , R.quantity,R.price, R.unit, DATE_FORMAT(date(R.date), '%d/%m/%Y') as received_date, R.color, R.comment, U.name as added_by FROM `ready_stock` R JOIN `users` U ON U.id = R.added_by WHERE  YEAR(date) = " . $_POST['year'] . "  AND MONTH(date) = " . $_POST['month']);//


            /*echo '<pre>';
            var_dump($ready_stock);
             echo '</pre>';*/

            foreach ($ready_stock as $kbc) {
                foreach ($kbc as $abc) {
                    echo $kbc['id'];
                }
            }

            /* $ids = array_unique(array_column($ready_stock, 'id'));
             foreach ($ids as $value) {

                 echo $value;
             }


             while ($row=mysqli_fetch_row($ready_stock))
             {
                 printf ("%s (%s)\n",$row[0],$row[1]);
             }


             if ($ready_stock) {
                 $this->status_code = 200;
                 return array('status' => true, 'readyStocks' => $ready_stock);
             } else {
                 $this->status_code = 200;
                 return array('status' => true, 'readyStocks' => []);
             }*/

        }

    }
}