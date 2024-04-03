<?php
session_start();
ini_set('display_errors', 1);

class Action {
    private $db;

    public function __construct() {
        ob_start();
        include 'db_connect.php';
        $this->db = $conn;
    }

    function __destruct() {
        $this->db = null;
        ob_end_flush();
    }

    function login(){
        extract($_POST);
        $stmt = $this->db->prepare("SELECT *, concat(lastname,', ',firstname,' ',middlename) as name FROM users where email = :email and password = :password");
        $stmt->execute(array(':email' => $email, ':password' => md5($password)));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            foreach ($result as $key => $value) {
                if($key != 'password' && !is_numeric($key))
                    $_SESSION['login_'.$key] = $value;
            }
            return 1;
        }else{
            return 3;
        }
    }

    function logout(){
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:login.php");
    }

    function save_user(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
                if($k =='password')
                    $v = md5($v);
                if(empty($data)){
                    $data .= " $k=:$k ";
                }else{
                    $data .= ", $k=:$k ";
                }
            }
        }
        $stmt = $this->db->prepare("SELECT * FROM users where email = :email ".(!empty($id) ? " and id != :id " : ''));
        $stmt->execute(array(':email' => $email, ':id' => $id));
        $check = $stmt->rowCount();
        if($check > 0){
            return 2;
            exit;
        }
        if(empty($id)){
            $stmt = $this->db->prepare("INSERT INTO users set $data");
        }else{
            $stmt = $this->db->prepare("UPDATE users set $data where id = :id");
            $stmt->bindParam(':id', $id);
        }
        foreach ($_POST as $key => &$value) {
            if($key == 'password')
                $value = md5($value);
            $stmt->bindParam(':'.$key, $value);
        }
        $save = $stmt->execute();
        if($save){
            return 1;
        }
    }

    function update_user(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id','cpass','table')) && !is_numeric($k)){
                if($k =='password')
                    $v = md5($v);
                if(empty($data)){
                    $data .= " $k=:$k ";
                }else{
                    $data .= ", $k=:$k ";
                }
            }
        }
        $stmt = $this->db->prepare("SELECT * FROM users where email = :email ".(!empty($id) ? " and id != :id " : ''));
        $stmt->execute(array(':email' => $email, ':id' => $id));
        $check = $stmt->rowCount();
        if($check > 0){
            return 2;
            exit;
        }
        if(empty($id)){
            $stmt = $this->db->prepare("INSERT INTO users set $data");
        }else{
            $stmt = $this->db->prepare("UPDATE users set $data where id = :id");
            $stmt->bindParam(':id', $id);
        }
        foreach ($_POST as $key => &$value) {
            if($key == 'password')
                $value = md5($value);
            $stmt->bindParam(':'.$key, $value);
        }
        $save = $stmt->execute();
        if($save){
            foreach ($_POST as $key => $value) {
                if($key != 'password' && !is_numeric($key))
                    $_SESSION['login_'.$key] = $value;
            }
            return 1;
        }
    }

    function delete_user(){
        extract($_POST);
        $stmt = $this->db->prepare("DELETE FROM users where id = :id");
        $stmt->bindParam(':id', $id);
        $delete = $stmt->execute();
        if($delete)
            return 1;
    }
	
	function save_page_img(){
		extract($_POST);
		if($_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			if($move){
				$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
				$hostName = $_SERVER['HTTP_HOST'];
				$path =explode('/',$_SERVER['PHP_SELF']);
				$currentPath = '/'.$path[1]; 
				// $pathInfo = pathinfo($currentPath); 
				return json_encode(array('link'=>$protocol.'://'.$hostName.$currentPath.'/admin/assets/uploads/'.$fname));
			}
		}
	}
	
	function save_survey(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k=:$v ";
				}else{
					$data .= ", $k=:$v ";
				}
			}
		}
		if(empty($id)){
			$stmt = $conn->prepare("INSERT INTO survey_set set $data");
		}else{
			$stmt = $conn->prepare("UPDATE survey_set set $data where id = :id");
			$stmt->bindParam(':id', $id);
		}
	
		if($stmt->execute())
			return 1;
	}
	
	function delete_survey(){
		extract($_POST);
		$stmt = $conn->prepare("DELETE FROM survey_set where id = :id");
		$stmt->bindParam(':id', $id);
		if($stmt->execute()){
			return 1;
		}
	}
	
	function save_question(){
		extract($_POST);
		$data = " survey_id=:sid ";
		$data .= ", question=:question ";
		$data .= ", type=:type ";
		if($type != 'textfield_s'){
			$arr = array();
			foreach ($label as $k => $v) {
				$i = 0 ;
				while($i == 0){
					$k = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(5/strlen($x)) )),1,5);
					if(!isset($arr[$k]))
						$i = 1;
				}
				$arr[$k] = $v;
			}
			$data .= ", frm_option=:frm_option ";
		}else{
			$data .= ", frm_option='' ";
		}
		if(empty($id)){
			$stmt = $conn->prepare("INSERT INTO questions set $data");
		}else{
			$stmt = $conn->prepare("UPDATE questions set $data where id = :id");
			$stmt->bindParam(':id', $id);
		}
		$stmt->bindParam(':sid', $sid);
		$stmt->bindParam(':question', $question);
		$stmt->bindParam(':type', $type);
		if($type != 'textfield_s'){
			$json_arr = json_encode($arr);
			$stmt->bindParam(':frm_option', $json_arr);
		}
	
		if($stmt->execute())
			return 1;
	}
	
	function delete_question(){
		extract($_POST);
		$stmt = $conn->prepare("DELETE FROM questions where id = :id");
		$stmt->bindParam(':id', $id);
		if($stmt->execute()){
			return 1;
		}
	}
	
	function action_update_qsort(){
		extract($_POST);
		$i = 0;
		foreach($qid as $k => $v){
			$i++;
			$stmt = $conn->prepare("UPDATE questions set order_by = :order_by where id = :id");
			$stmt->bindParam(':order_by', $i);
			$stmt->bindParam(':id', $v);
			$stmt->execute();
		}
		return 1;
	}
	
	function save_answer(){
		extract($_POST);
		foreach($qid as $k => $v){
			$data = " survey_id=:survey_id ";
			$data .= ", question_id=:question_id ";
			$data .= ", user_id=:user_id ";
			if($type[$k] == 'check_opt'){
				$data .= ", answer=:answer ";
				$json_answer = json_encode($answer[$k]);
			}else{
				$data .= ", answer=:answer ";
				$json_answer = $answer[$k];
			}
			$stmt = $conn->prepare("INSERT INTO answers set $data");
			$stmt->bindParam(':survey_id', $survey_id);
			$stmt->bindParam(':question_id', $qid[$k]);
			$stmt->bindParam(':user_id', $_SESSION['login_id']);
			$stmt->bindParam(':answer', $json_answer);
			$stmt->execute();
		}
		return 1;
	}
	
	function delete_comment(){
		extract($_POST);
		$stmt = $conn->prepare("DELETE FROM comments where id = :id");
		$stmt->bindParam(':id', $id);
		if($stmt->execute()){
			return 1;
		}
	}
}