<?php
require_once '../library/Validator.php';

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

    public function add()
    {
        //product_name, price, quantity, unit, date, color, comment, user_id
        $validator = new Validator();
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
                return array('status' => true, 'ready_stock_id' => $ready_stock_id, 'message' => 'Ready Stock Successfully Added');
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

            $ready_stock = DB::query("SELECT R.id, R.product_name as name , R.quantity,R.price, R.unit, R.date as received_date, R.color, R.comment, U.name as added_by FROM `ready_stock` R JOIN `users` U ON U.id = R.added_by WHERE  YEAR(date) = " . $_POST['year'] . "  AND MONTH(date) = " . $_POST['month']);//


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