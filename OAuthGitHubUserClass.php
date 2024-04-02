<?php 
/* 
 * User Class 
 * This class is used for database related (connect, insert, and update) operations
 */ 
 
 class User { 
    private $dbHost     = DB_HOST; 
    private $dbUsername = DB_USERNAME; 
    private $dbPassword = DB_PASSWORD; 
    private $dbName     = DB_NAME; 
    private $userTbl    = DB_USER_TBL; 
    private $db; 
    
    function __construct(){ 
        try {
            $dsn = "mysql:host={$this->dbHost};dbname={$this->dbName}";
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            $this->db = new PDO($dsn, $this->dbUsername, $this->dbPassword, $options);
        } catch (PDOException $e) {
            die("Error en la conexión " . $e->getMessage());
        }
    } 
    
    function checkUser($data = array()){ 
        if(!empty($data)){ 
            $userData = false;
            $oauthProvider = $data['oauth_provider'];
            $oauthUid = $data['oauth_uid'];
            
            // Check whether the user already exists in the database 
            $checkQuery = "SELECT * FROM ".$this->userTbl." WHERE oauth_provider = :oauth_provider AND oauth_uid = :oauth_uid"; 
            $stmt = $this->db->prepare($checkQuery);
            $stmt->bindParam(':oauth_provider', $oauthProvider);
            $stmt->bindParam(':oauth_uid', $oauthUid);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Add modified time to the data array 
            if(!array_key_exists('modified', $data)){ 
                $data['modified'] = date("Y-m-d H:i:s"); 
            } 
            
            if($userData){ 
                // Prepare column and value format 
                $colvalSet = ''; 
                $i = 0; 
                foreach($data as $key => $val){ 
                    $pre = ($i > 0) ? ', ' : ''; 
                    $colvalSet .= $pre.$key."=:".$key; 
                    $i++; 
                } 
                $whereSql = " WHERE oauth_provider = :oauth_provider AND oauth_uid = :oauth_uid"; 
                 
                // Update user data in the database 
                $query = "UPDATE ".$this->userTbl." SET ".$colvalSet.$whereSql; 
                $stmt = $this->db->prepare($query);
                foreach ($data as $key => &$value) {
                    $stmt->bindParam(':'.$key, $value);
                }
                $stmt->bindParam(':oauth_provider', $oauthProvider);
                $stmt->bindParam(':oauth_uid', $oauthUid);
                $stmt->execute();
            } else { 
                // Add created time to the data array 
                if(!array_key_exists('created', $data)){ 
                    $data['created'] = date("Y-m-d H:i:s"); 
                } 
                 
                // Prepare column and value format 
                $columns = $values = ''; 
                $i = 0; 
                foreach($data as $key => $val){ 
                    $pre = ($i > 0) ? ', ' : ''; 
                    $columns .= $pre.$key; 
                    $values  .= $pre.':'.$key; 
                    $i++; 
                } 
                 
                // Insert user data in the database 
                $query = "INSERT INTO ".$this->userTbl." (".$columns.") VALUES (".$values.")"; 
                $stmt = $this->db->prepare($query);
                foreach ($data as $key => &$value) {
                    $stmt->bindParam(':'.$key, $value);
                }
                $stmt->execute();
            } 
            
            // Get user data from the database 
            $stmt = $this->db->prepare($checkQuery);
            $stmt->bindParam(':oauth_provider', $oauthProvider);
            $stmt->bindParam(':oauth_uid', $oauthUid);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        } 
         
        // Return user data 
        return !empty($userData) ? $userData : false; 
    } 
}
?>