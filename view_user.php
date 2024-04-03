<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
    $type_arr = array('',"Admin","Alumno");
    $stmt = $conn->prepare("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) as name FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $qry = $stmt->fetch(PDO::FETCH_ASSOC);
    foreach($qry as $k => $v){
        $$k = $v;
    }
}
?>

<div class="container-fluid">
	<table class="table">
		<tr>
			<th>Nombre:</th>
			<td><b><?php echo ucwords($name) ?></b></td>
		</tr>
		<tr>
			<th>Correo:</th>
			<td><b><?php echo $email ?></b></td>
		</tr>
		<tr>
			<th>Celular:</th>
			<td><b><?php echo $contact ?></b></td>
		</tr>
		<tr>
			<th>Dirección:</th>
			<td><b><?php echo $address ?></b></td>
		</tr>
		<tr>
			<th>Rol:</th>
			<td><b><?php echo $type_arr[$type] ?></b></td>
		</tr>
	</table>
</div>
<div class="modal-footer display p-0 m-0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
</div>
<style>
	#uni_modal .modal-footer{
		display: none
	}
	#uni_modal .modal-footer.display{
		display: flex
	}
</style>