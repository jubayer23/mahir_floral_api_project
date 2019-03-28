<?php 
  class Api_key {
    private $table = 'api_key';

    public $id;
    public $access_token;
    public $user_id;
    public $last_used;
    public $created;
	
	public function set_api_key($user_id)
    {
        $key = $this->generate_api_key();
		
		$added = DB::insert($this->table, array(
			'access_token' => $key,
			'user_id' => $user_id
		));
		if ($added) {
            return $key;
        }
    }
	public function generate_api_key()
    {
		
		require_once '../library/common.php';
		$common = new common();
        $length = 50;
        $t = 1;
        while ($t <= 10) {
            $temp = $common->generateRandomString($length);
			$key_is_avail = DB::query("SELECT  * FROM $this->table WHERE access_token=%s_temp", 
			  array(
				'table' => $this->table,
				'temp' => $temp
			  )
			);
            if ($key_is_avail) {
                $t++;
            } else {
                return $temp;
            }
        }
    }
    public function validate_api_key()
    {
        $headers = getallheaders();
        if (isset($headers['authorization'])) {
            $token = $headers['authorization'];
        } elseif (isset($headers['Authorization'])) {
            $token = $headers['Authorization'];
        } else {
            return;
        }
		$result = DB::query("SELECT  * FROM $this->table WHERE access_token=%s_token", 
			 array(
				'table' => $this->table,
				'token' => $token
			)
		);
        if (!$result) {
            return false;
        }
        $result = _row_array($result);
		DB::update($this->table, array(
		'last_used' => date('Y-m-d H:i:s')
		 ), "id=%i", $result['id']);
		return $result['user_id'];
    }
  }