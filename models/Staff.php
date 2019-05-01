<?php

class Staff
{
    private $table = 'users';

    public $id;
    public $name;
    public $email;
    public $salt;
    public $password;
    public $role;
    public $creation_date;
    public $fields;
    public $status_code;
    public $is_admin;


    public function signup()
    {
        require_once '../library/Validator.php';
        $validator = new Validator();
        $errors = [];
        $message = null;
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        

        if (isset($_POST['name']) && (trim($_POST['name']) != '')) {
            $name = $_POST['name'];
        } else {
            $message = 'Required field missing';
            $errors['name'] = "Name Is Require";
        }
        if (isset($_POST['email']) && (trim($_POST['email']) != '')) {

            if ($validator->valid_email($_POST['email'])) {
                $user_exist = DB::query("SELECT * FROM users WHERE email = %s", $_POST['email']);
                $user_exist = _row_array($user_exist);
                if ($user_exist) {
                    $message = "email already registered";
                    $errors['email'] = "email already registered";
                } else {
                    $email = $_POST['email'];
                }
            } else {
                $message = "Invalid Email";
                $errors['email'] = "Invalid Email";
            }
        } else {
            $message = 'Required field missing';
            $errors['email'] = "Email Is Require";
        }
        if (isset($_POST['shop_id']) && (trim($_POST['shop_id']) != '')) {
            if ($validator->integer($_POST['shop_id'])) {
                //check shop exit
                $shop_exist = DB::query("SELECT * FROM shop WHERE id = %i", $_POST['shop_id']);
                $shop_exist = _row_array($shop_exist);
                if (!$shop_exist) {
                    $errors['shop_id'] = "Shop not exist";
                } else {
                    $shop_id = $_POST['shop_id'];
                }
            } else {
                $errors['shop_id'] = "Shop Is Require Integer";
            }

        } else {
            $message = 'Required field missing';
            $errors['shop_id'] = "Shop Is Require";
        }

        if (isset($_POST['password']) && (trim($_POST['password']) != '')) {
            $password = $_POST['password'];
            if ($validator->min_length($_POST['password'], 8)) {
                $password = $_POST['password'];
            } else {
                $errors['password'] = "Password Is Require At Least 8 Char";
            }
        } else {
            $message = 'Required field missing';
            $errors['password'] = "Password Is Require";
        }

        if (isset($_POST['role']) && (trim($_POST['role']) != '')) {
            $role = $_POST['role'];
            if ($validator->in_list($_POST['role'], ['Raw Stock', 'Shop Stock'])) {
                $role = $_POST['role'];
            } else {
                $errors['role'] = "Invalid Role";
            }
        } else {
            $message = 'Required field missing';
            $errors['role'] = "Role Is Require";
        }
        if (!empty($errors)) {
            return ['status' => false, 'message' => $message, 'errors' => $errors];

            //echo json_encode($errors);exit;
        } else {
            $saltgen = hash('sha512', substr(str_replace('+', '.', base64_encode(md5(mt_rand(), true))), 0, 127));
            $user = [
                'email' => $email,
                'salt' => $saltgen,
                'password' => hash('sha512', $saltgen . $password),
                'name' => $name,
                'role' => $role,
                'is_online' => 0,
                'added_by' => $this->id,
                'updated_by' => $this->id,
                'creation_date' => date('Y-m-d H:i:s')
            ];
            $added = DB::insert($this->table, $user);

            if ($added) {
                $user_id = DB::insertId();
                //sorry here is error
                $user_shop = [
                    'user_id' => $user_id,
                    'shop_id' => $shop_id,
                ];
                DB::insert('user_shop', $user_shop);


                $to = $email;
                $subject = 'Mahir Floral Management App: Registration successful';
                $message = 'You have been registered with the Mahir Floral Management App. Please use following credentials for login.
                 email =' + $email + ' password=' + $password;
                $headers = 'From: tasmina@mahirfloralevents.com' . "\r\n" .
                    'Reply-To: tasmina@mahirfloralevents.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                mail($to, $subject, $message, $headers);

                echo json_encode(['status' => true, 'message' => 'Successfully Registration', 'user_id' => $user_id]);
                exit;
                return ['status' => true, 'message' => 'Successfully Login', 'access_token' => $api_key];
            }
            return false;
        }
    }

    public function user_info()
    {

        /*if (isset($this->fields) && !empty($this->fields)) {
            $this->fields = implode(",",$this->fields);
        } else {
           $this->fields = '*';
        }*/
        if ($this->is_admin) {
            $user_info = DB::query("SELECT U.`id`, U.`name`,U.role FROM `users` U WHERE U.id = %i", $this->id);
        } else {
            $user_info = DB::query("SELECT U.`id`, U.`name`,U.role,S.id as shop_id ,S.name as shop_name,S.address as shop_address,S.loc_lat as shop_lat,S.loc_long as shop_long FROM `users` U JOIN user_shop A ON A.user_id = U.id JOIN shop S ON S.id = A.shop_id
			WHERE U.id = %i", $this->id);
            if (!$user_info) {
                $user_info = DB::query("SELECT U.`id`, U.`name`,U.role FROM `users` U WHERE U.id = %i", $this->id);
            }
        }

        if ($user_info) {
            $user_info = _row_array($user_info);
            //$this->status_code = 200;
            return array('status' => true, 'message' => null, 'user_profile' => $user_info);
        }

    }

    public function check_role()
    {
        $user_info = DB::query("SELECT role FROM users WHERE id = %i", $this->id);
        $user_info = _row_array($user_info);
        if ($user_info) {
            return $user_info['role'];
        }
        return false;
    }

    public function signin()
    {
        require_once '../library/Validator.php';
        $validator = new Validator();

        $errors = [];
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        if (isset($_POST['email']) && (trim($_POST['email']) != '')) {

            if ($validator->valid_email($_POST['email'])) {
                $user_exist = DB::query("SELECT * FROM users WHERE email = %s AND status = %s", $_POST['email'], 'Active');
                if ($user_exist) {
                    $user_exist = _row_array($user_exist);
                } else {
                    $message = "Invalid username and password";
                    $errors['error_msg'] = 'Invalid username and password';
                }
            } else {
                $errors['email'] = "Invalid Email";
            }
        } else {
            $message = 'Required field missing';
            $errors['email'] = "Email Is Require";
        }

        if (isset($_POST['password']) && (trim($_POST['password']) != '')) {
            $password = $_POST['password'];
        } else {
            $message = 'Required field missing';
            $errors['password'] = "Password Is Require";
        }

        if (!empty($errors)) {
            //$this->status_code = 200;
            return ['status' => false, 'message' => $message, 'errors' => $errors];
        } else {
            $user_info = DB::query("SELECT * FROM users WHERE email = %s AND status = %s", $_POST['email'], 'Active');

            if ($user_info) {
                $message = "Invalid username and password";
                $user_info = _row_array($user_info);
                $pass = hash('sha512', $user_info['salt'] . $_POST['password']);
                if ($user_info['password'] == $pass) {
                    //$this->status_code = 200;
                    require_once '../models/Api_key.php';
                    $api_key = new Api_key();
                    $api_key = $api_key->set_api_key($user_info['id']);
                    //Update Login user Status
                    DB::update($this->table, array(
                        'is_online' => 1
                    ), "id=%i", $user_info['id']);
                    return array('status' => true, 'message' => 'Successfully Login', 'access_token' => $api_key);
                }
                //$this->status_code = 401;
                $message = "Invalid username and password";
                return ['status' => false, 'message' => $message, 'errors' => ['error_msg' => 'Invalid username and password']];
            } else {
                $message = "Invalid username and password";
                //$this->status_code = 401;
                return ['status' => false, 'message' => $message, 'errors' => ['error_msg' => 'Invalid username and password']];
            }
        }
    }

    public function get_user_online_status()
    {
        require_once '../library/Validator.php';
        $validator = new Validator();

        $errors = [];
        //$_POST = (array)json_decode(file_get_contents("php://input", true));
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        if (isset($_POST['user_id']) && (trim($_POST['user_id']) != '')) {
            if ($validator->integer($_POST['user_id'])) {
                //check shop exit
                $user_exist = DB::query("SELECT * FROM users WHERE id = %i", $_POST['user_id']);
                $user_exist = _row_array($user_exist);
                if (!$user_exist) {
                    $errors['user_id'] = "User not exist";
                } else {
                    $user_id = $_POST['user_id'];
                }


            } else {
                $errors['user_id'] = "User Is Integer";
            }

        } else {
            $errors['user_id'] = "user is Require";
        }
        if (!empty($errors)) {
            // $this->status_code = 400;
            return ['status' => false, 'message' => 'INVALID', 'errors' => $errors];
        } else {
            //$this->status_code = 200;

            $user_checks_by_id = DB::query("SELECT * FROM user_check WHERE user_id = %i order by id desc limit 1", $_POST['user_id']);
            if ($user_checks_by_id) {
                return array('status' => true,
                    'message' => null,
                    'Online' => $user_exist['is_online'],
                    'last_checked_in' => $user_checks_by_id[0]['check_in'],
                    'last_checked_out' => $user_checks_by_id[0]['check_out']
                );
            } else {
                return array('status' => true,
                    'message' => null,
                    'Online' => $user_exist['is_online'],
                    'last_checked_in' => null,
                    'last_checked_out' => null
                );
            }


        }
        //$this->status_code = 503;
        return ['status' => false, "message" => "Please Try Later"];
    }

    /*  public function user_check()
      {

          require_once '../library/Validator.php';
          $validator = new Validator();

          $errors = [];
          $_POST = (array)json_decode(file_get_contents("php://input", true));
          if (isset($_POST['user_id']) && (trim($_POST['user_id']) != '')) {
              if ($validator->integer($_POST['user_id'])) {
                  //check shop exit
                  $user_exist = DB::query("SELECT * FROM users WHERE id = %i", $_POST['user_id']);
                  $user_exist = _row_array($user_exist);
                  if (!$user_exist) {
                      $errors['user_id'] = "User not exist";
                  } else {
                      $user_id = $_POST['user_id'];
                  }
              } else {
                  $errors['user_id'] = "User Is Integer";
              }

          } else {
              $errors['user_id'] = "user is Require";
          }
          if (isset($_POST['user_check']) && (trim($_POST['user_check']) != '')) {
              if ($validator->integer($_POST['user_check'])) {
                  $user_check = $_POST['user_check'];
              } else {
                  $errors['user_check'] = "user_check is Integer";
              }

              if ($validator->regex_match($user_check, '/^[0-1]*$/')) {
                  true;
              } else {
                  $errors['user_check'] = "Invalid Value";
              }

          } else {
              $errors['user_check'] = "user_check is Require";
          }

          if (!empty($errors)) {
              $this->status_code = 400;
              return ['status' => false, 'message' => 'INVALID', 'errors' => $errors];
          } else {
              //date('Y-m-d H:i:s')
              $check_in = null;
              $check_out = null;

              if ($user_check) {
                  $check_in = date('Y-m-d H:i:s');
              } else {
                  $check_out = date('Y-m-d H:i:s');
              }

              $user_check_data = [
                  'user_id' => $user_id,
                  'check_in' => $check_in,
                  'check_out' => $check_out,
              ];
              //check user already Check-in
              $today_check_in = DB::query("SELECT * FROM user_check WHERE user_id = %i AND DATE(check_in) = CURDATE() ", $user_id);

              if ($today_check_in) {
                  $today_check_in = _row_array($today_check_in);
                  if ($today_check_in['check_in'] && $user_check) {
                      $this->status_code = 400;
                      return array('status' => false, 'message' => 'User Already Check-in');
                  }
                  if ($today_check_in['check_out']) {
                      $this->status_code = 400;
                      return array('status' => false, 'message' => 'User Already Check-out');
                  }

                  DB::update('user_check', array('check_out' => $check_out), "id=%i", $today_check_in['id']);
                  $this->status_code = 201;
                  return array('status' => true, 'message' => 'User Successfully Check-out');

              } else {
                  if ($check_out) {
                      $this->status_code = 400;
                      return array('status' => false, 'message' => 'User Not Check-in');
                  }
                  $added = DB::insert('user_check', $user_check_data);
                  if ($added) {
                      $this->status_code = 201;
                      return array('status' => true, 'message' => 'User Successfully Check-in');
                  }

              }


          }
          $this->status_code = 503;
          return ['status' => false, "message" => "Please Try Later"];
      }*/


    public function user_check()
    {
        require_once '../library/Validator.php';
        $validator = new Validator();

        $errors = [];
        $message = 'invalid input';
        $_POST = (array)json_decode(file_get_contents("php://input", true));
        if (isset($_POST['user_id']) && (trim($_POST['user_id']) != '')) {
            if ($validator->integer($_POST['user_id'])) {
                //check shop exit
                $user_exist = DB::query("SELECT * FROM users WHERE id = %i", $_POST['user_id']);
                $user_exist = _row_array($user_exist);
                if (!$user_exist) {
                    $message = 'User does not exist';
                    $errors['user_id'] = "User not exist";
                } else {
                    $user_id = $_POST['user_id'];
                }
            } else {
                $errors['user_id'] = "User Is Integer";
            }

        } else {
            $message = 'Required field are missing';
            $errors['user_id'] = "user is Require";
        }
        if (isset($_POST['user_check']) && (trim($_POST['user_check']) != '')) {
            if ($validator->integer($_POST['user_check'])) {
                $user_check = $_POST['user_check'];
            } else {
                $errors['user_check'] = "user_check is Integer";
            }

            if ($validator->regex_match($user_check, '/^[0-1]*$/')) {
                true;
            } else {
                $errors['user_check'] = "Invalid Value";
            }

        } else {
            $errors['user_check'] = "user_check is Require";
        }


        if (!empty($errors)) {
            //$this->status_code = 400;
            return ['status' => false, 'message' => $message, 'errors' => $errors];
        }

        $user_id = $_POST['user_id'];
        $user_check = $_POST['user_check'];


        $check_in = null;
        $check_out = null;

        if ($user_check) {
            $check_in = date('Y-m-d H:i:s');
        } else {
            $check_out = date('Y-m-d H:i:s');
        }

        $user_check_data = [
            'user_id' => $user_id,
            'check_in' => $check_in,
            'check_out' => $check_out,
        ];

        $user_online_status = DB::query("SELECT * FROM users WHERE id = %i", $user_id);

        if ($user_online_status) {
            $user_online_status = _row_array($user_online_status);

            $is_online = $user_online_status['is_online'];

            if ($is_online == 1) {

                //User is trying to check out in this section

                if ($user_check) {
                    //$this->status_code = 400;
                    return array('status' => false, 'message' => 'User Already Check-in');
                }

                $user_checks_by_id = DB::query("SELECT * FROM user_check WHERE user_id = %i order by id desc limit 1", $user_id);


                if (!$user_checks_by_id) {
                    return array('status' => false, 'message' => 'Database error: No row found!');
                }

                if ($user_checks_by_id[0]['check_out']) {
                    //$this->status_code = 400;
                    return array('status' => false, 'message' => 'User Already Check-out');
                }

                DB::update('user_check', array('check_out' => $check_out), "id=%i", $user_checks_by_id[0]['id']);
                DB::update('users', array('is_online' => $user_check), "id=%i", $user_id);
                //$this->status_code = 201;
                //array('status' => true, 'message' => null, 'Online' => $user_exist['is_online'], 'last_checked_in' => $user_checks_by_id[0]['check_in']);
                return array('status' => true,
                    'message' => 'User Successfully Check-out',
                    'Online' => $user_check,
                    'last_checked_in' => $user_checks_by_id[0]['check_in'],
                    'last_checked_out' => $check_out
                );


            } else {

                //user wants to check-in in this section

                if (!$user_check) {
                    // $this->status_code = 400;
                    return array('status' => false, 'message' => 'User Already Check-out');
                }
                $user_checks_by_id = DB::query("SELECT * FROM user_check WHERE user_id = %i order by id desc limit 1", $user_id);

                if ($user_checks_by_id) {
                    // if( date('Y-m-d', $user_checks_by_id[0]['check_in']) == date('Y-m-d', $check_in)  ){
                    $t1 = strtotime($check_in);
                    $t2 = strtotime($user_checks_by_id[0]['check_in']);

                    if (date('d/m/y', $t1) == date('d/m/y', $t2)) {
                        return array('status' => false, 'message' => 'You already check-in check-out today. You cannot check-in two times in a day.');
                    }
                    // echo date('d/m/y',$t);
                    // echo date('Y-m-d', $user_checks_by_id[0]['check_in']);
                    //  echo date('Y-m-d', $check_in);
                    //  return array('status' => false, 'message' => 'You already check-in check-out today. You cannot check-in two times in a day.');
                    //}
                }

                $added = DB::insert('user_check', $user_check_data);
                if ($added) {
                    DB::update('users', array('is_online' => $user_check), "id=%i", $user_id);
                    // $this->status_code = 201;
                    // return array('status' => true, 'message' => 'User Successfully Check-in');
                    return array('status' => true,
                        'message' => 'User Successfully Check in',
                        'Online' => $user_check,
                        'last_checked_in' => $check_in,
                        'last_checked_out' => $check_out
                    );
                }


            }


        } else {
            // $this->status_code = 201;
            return array('status' => false, 'message' => 'Database error');
        }
    }


    public function timesheet()
    {

        require_once '../library/Validator.php';
        $validator = new Validator();

        $errors = [];
        $_POST = (array)json_decode(file_get_contents("php://input", true));

        if (isset($_POST['filter_type']) && (trim($_POST['filter_type']) != '')) {

            if ($validator->in_list($_POST['filter_type'], ['week', 'date'])) {
                if ($_POST['filter_type'] == 'week') {
                    if (isset($_POST['week_num']) && (trim($_POST['week_num']) != '')) {

                        if ($validator->integer($_POST['week_num'])) {
                            if ($validator->regex_match($_POST['week_num'], '/^(0[1-9]|[1-4][0-9]|5[0-2])$/')) {
                                true;
                            } else {
                                $errors['week_num'] = "Invalid Week Number Formate Year Look Like 09 Or 12 ";
                            }
                        } else {
                            $errors['week_num'] = "Week Number  is Integer";
                        }


                    } else {
                        $errors['week_num'] = "Week Number  is Require";
                    }
                    if (isset($_POST['year']) && (trim($_POST['year']) != '')) {
                        if ($validator->regex_match($_POST['year'], '/^[0-9]{4}$/')) {
                            true;
                        } else {
                            $errors['year'] = "Invalid Year Formate Year Look Like yyyy";
                        }
                    } else {
                        $errors['year'] = "Year is Require";
                    }
                }
                if ($_POST['filter_type'] == 'date') {
                    if (isset($_POST['start_date']) && trim($_POST['start_date']) != '') {

                        if ($validator->regex_match($_POST['start_date'], '/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/')) {
                            true;
                        } else {
                            $errors['start_date'] = "Invalid Start Date Formate Date Look Like dd/mm/yyyy";
                        }

                    } else {
                        $errors['start_date'] = "Start Date is Require";
                    }
                    if (isset($_POST['end_date']) && trim($_POST['end_date']) != '') {

                        if ($validator->regex_match($_POST['end_date'], '/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/')) {
                            if (strtotime($_POST['start_date']) > strtotime($_POST['end_date'])) {
                                $errors['end_date'] = "End Date Should be Greater then Start Date";
                            }
                            $datetime = new DateTime();
                            $start_date = $datetime->createFromFormat('d/m/Y', $_POST['start_date']);
                            $end_date = $datetime->createFromFormat('d/m/Y', $_POST['end_date']);

                            //$start_date = new DateTime($_POST['start_date']);
                            //$end_date = new DateTime($_POST['end_date']);
                            //var_dump($start_date,$end_date );die;
                            $days = $end_date->diff($start_date)->format('%a');
                            if ($days > 7) {
                                $errors['end_date'] = "You can Select Only 7 Days";
                            }
                        } else {
                            $errors['end_date'] = "Invalid End Date Formate Date Look Like dd/mm/yyyy";
                        }

                    } else {
                        $errors['end_date'] = "End Date is Require";
                    }
                }

            } else {
                $errors['filter_type'] = "Invalid Filter Type";
            }

        } else {
            $errors['filter_type'] = "Filter Type is Require";
        }


        if (!empty($errors)) {
            // $this->status_code = 400;
            return ['status' => false, 'message' => 'INVALID', 'errors' => $errors];
        } else {

            if ($_POST['filter_type'] == 'week') {
                $gendate = new DateTime();
                $gendate->setISODate($_POST['year'], $_POST['week_num'], 1); //year , week num , day
                $startDate = $gendate->format('Y-m-d');

                $Date1 = new DateTime($startDate);
                $Date1->modify('+6 day');
                $endDate = $Date1->format('Y-m-d');

                $today_check_in = DB::query("SELECT user_id,U.name,DATE_FORMAT(date(check_in), '%d/%m/%Y') as date,TIME_FORMAT(`check_in`, '%H:%i') as check_in,TIME_FORMAT(`check_out`, '%H:%i') as check_out, (TIME_FORMAT(`check_out`, '%H:%i') - TIME_FORMAT(`check_in`, '%H:%i'))as total_hour 
				FROM `user_check`
				JOIN users U ON U.id = user_check.user_id
				WHERE date(check_in) BETWEEN '" . $startDate . "'  AND '" . $endDate . "' ");

                $timeSheets = [];
                $ids = array_unique(array_column($today_check_in, 'user_id'));
                foreach ($ids as $value) {

                    $timeSheet = $this->search_value($today_check_in, 'user_id', $value); //extract unique ids from array
                    $name = $timeSheet[0]['name'];
                    $keysRemove = ['user_id', 'name'];
                    $timeSheet = $this->removeElementWithValue($timeSheet, $keysRemove);

                    //$name = $this->get_associative_value($timeSheet,'name');
                    $timeSheets[] = ['user_id' => $value, 'user_name' => $name, 'timeSheets' => $timeSheet];
                }
                // $this->status_code = 20;
                return array('status' => true, 'users' => $timeSheets);
            }
            if ($_POST['filter_type'] == 'date') {
                $datetime = new DateTime();
                $start_date = $datetime->createFromFormat('d/m/Y', $_POST['start_date']);
                $end_date = $datetime->createFromFormat('d/m/Y', $_POST['end_date']);
                $start_date = $start_date->format('Y-m-d');
                $end_date = $end_date->format('Y-m-d');

                $today_check_in = DB::query("SELECT user_id,U.name,DATE_FORMAT(date(check_in), '%d/%m/%Y') as date,TIME_FORMAT(`check_in`, '%H:%i') as check_in,TIME_FORMAT(`check_out`, '%H:%i') as check_out, (TIME_FORMAT(`check_out`, '%H:%i') - TIME_FORMAT(`check_in`, '%H:%i'))as total_hour 
				FROM `user_check`
				JOIN users U ON U.id = user_check.user_id
				WHERE date(check_in) BETWEEN '" . $start_date . "'  AND '" . $end_date . "' ");
                //print_r($today_check_in);die;
                $timeSheets = [];
                $ids = array_unique(array_column($today_check_in, 'user_id'));
                foreach ($ids as $value) {

                    $timeSheet = $this->search_value($today_check_in, 'user_id', $value); //extract unique ids from array
                    //$name = $this->get_associative_value($timeSheet,'name');
                    $name = $timeSheet[0]['name'];
                    $keysRemove = ['user_id', 'name'];
                    $timeSheet = $this->removeElementWithValue($timeSheet, $keysRemove);

                    $timeSheets[] = ['user_id' => $value, 'user_name' => $name, 'timeSheets' => $timeSheet];
                }
                // $this->status_code = 20;
                return array('status' => true, 'users' => $timeSheets);
            }

        }
        //$this->status_code = 503;
        return ['status' => false, "message" => "Please Try Later"];
    }

    ///How to search by key=>value in a multidimensional array in PHP
    function search_value($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search_value($subarray, $key, $value));
            }
        }

        return $results;
    }

    ///How to search by key in a multidimensional array in PHP
    function search($array, $key)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key])) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key));
            }
        }

        return $results;
    }

    function get_associative_value($array, $field)
    {
        foreach ($array as $key => $value) {
            return $value[$field];
        }
        return false;
    }
//if key is array 
// multiple key as array
    function removeElementWithValue($array, $keys)
    {
        foreach ($array as $subKey => $subArray) {
            foreach ($subArray as $arrKey => $value) {
                if (is_array($keys)) {
                    foreach ($keys as $indexKey) {
                        unset($array[$subKey][$indexKey]);
                    }
                } else {
                    unset($array[$subKey][$key]);
                }

            }

        }
        return $array;
    }
    /*
//	If efficiency is important you could write it so all the recursive calls store their results in the same temporary $results array rather than merging arrays together, like so:

function search($array, $key, $value)
{
    $results = array();
    search_r($array, $key, $value, $results);
    return $results;
}

function search_r($array, $key, $value, &$results)
{
    if (!is_array($array)) {
        return;
    }

    if (isset($array[$key]) && $array[$key] == $value) {
        $results[] = $array;
    }

    foreach ($array as $subarray) {
        search_r($subarray, $key, $value, $results);
    }
}*/

}