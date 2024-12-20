<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_survey"><i class="fa fa-plus"></i> Crear nueva encuesta</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Título</th>
						<th>Descripción</th>
						<th>Inicio</th>
						<th>Fin</th>
						<th>Acción</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$i = 1;
					$stmt = $conn->query("SELECT * FROM survey_set ORDER BY DATE(start_date) ASC, DATE(end_date) ASC");
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
				?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['title']) ?></b></td>
						<td><b class="truncate"><?php echo $row['description'] ?></b></td>
						<td><b><?php echo date("M d, Y", strtotime($row['start_date'])) ?></b></td>
						<td><b><?php echo date("M d, Y", strtotime($row['end_date'])) ?></b></td>
						<td class="text-center">
							<div class="btn-group">
								<a href="./index.php?page=edit_survey&id=<?php echo $row['id'] ?>" class="btn btn-primary btn-flat">
									<i class="fas fa-edit"></i>
								</a>
								<a href="./index.php?page=view_survey&id=<?php echo $row['id'] ?>" class="btn btn-info btn-flat">
									<i class="fas fa-eye"></i>
								</a>
								<button type="button" class="btn btn-danger btn-flat delete_survey" data-id="<?php echo $row['id'] ?>">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</td>
					</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	$('.delete_survey').click(function(){
	_conf("¿Está seguro de eliminar esta encuesta?","delete_survey",[$(this).attr('data-id')])
	})
	})
	function delete_survey($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_survey',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Datos eliminados correctamente.",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>