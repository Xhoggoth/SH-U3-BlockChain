<?php
include 'db_connect.php';
$stmt = $conn->prepare("SELECT * FROM users where id = :id");
$stmt->execute(array(':id' => $_GET['id']));
$result = $stmt->fetch(PDO::FETCH_ASSOC);
foreach($result as $k => $v){
    $$k = $v;
}
include 'new_user.php';
?>
