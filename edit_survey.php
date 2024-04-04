<?php
include 'db_connect.php';
$stmt = $conn->prepare("SELECT * FROM survey_set where id = :id");
$stmt->execute(array(':id' => $_GET['id']));
$result = $stmt->fetch(PDO::FETCH_ASSOC);
foreach($result as $k => $v){
    if($k == 'title')
        $k = 'stitle';
    $$k = $v;
}
include 'new_survey.php';
?>