<?php include 'header.php' ?>
<?php include 'db_connect.php' ?>
<?php 
$stmt = $conn->prepare("SELECT * FROM survey_set WHERE id = :id");
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();
$qry = $stmt->fetch(PDO::FETCH_ASSOC);
foreach ($qry as $k => $v) {
    if ($k == 'title') {
        $k = 'stitle';
    }
    $$k = $v;
}

$stmt = $conn->prepare("SELECT DISTINCT(user_id) FROM answers WHERE survey_id = :survey_id");
$stmt->bindParam(':survey_id', $id);
$stmt->execute();
$taken = $stmt->rowCount();

$stmt = $conn->prepare("SELECT a.*, q.type FROM answers a INNER JOIN questions q ON q.id = a.question_id WHERE a.survey_id = :survey_id");
$stmt->bindParam(':survey_id', $id);
$stmt->execute();
$ans = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['type'] == 'radio_opt') {
        $ans[$row['question_id']][$row['answer']][] = 1;
    }
    if ($row['type'] == 'check_opt') {
        foreach (explode(",", str_replace(array("[","]"), '', $row['answer'])) as $v) {
            $ans[$row['question_id']][$v][] = 1;
        }
    }
    if ($row['type'] == 'textfield_s') {
        $ans[$row['question_id']][] = $row['answer'];
    }
}
?>

<div class="col-lg-12">
	<p>Título: <b><?php echo $stitle ?></b></p>
	<p class="mb-0">Descripción:</p>
	<small><?php echo $description; ?></small>
	<p>Inicio: <b><?php echo date("M d, Y",strtotime($start_date)) ?></b></p>
	<p>Fin: <b><?php echo date("M d, Y",strtotime($end_date)) ?></b></p>
	<p>Estado: <b><?php echo number_format($taken) ?></b></p>

	<div class="row">
		<div class="col-md-12">
			<div class="card card-outline card-success">
				<div class="card-header">
					<h3 class="card-title"><b>Informe de la encuesta</b></h3>
				</div>
				<div class="card-body ui-sortable">
				<?php 
					$statement = $conn->prepare("SELECT * FROM questions WHERE survey_id = :id AND type != 'textfield_s' ORDER BY ABS(order_by) ASC, ABS(id) ASC");
					$statement->bindParam(':id', $id);
					$statement->execute();
					while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
				?>
					<div class="callout callout-info">
						<h5><?php echo $row['question'] ?></h5>	
						<div class="col-md-12">
								<ul>
								<?php 
									$statement2 = $conn->prepare("SELECT frm_option FROM questions WHERE id = :question_id");
									$statement2->bindParam(':question_id', $row['id']);
									$statement2->execute();
									$options = json_decode($statement2->fetchColumn());

									foreach($options as $k => $v): 
										$prog = ((isset($ans[$row['id']][$k]) ? count($ans[$row['id']][$k]) : 0) / $taken) * 100;
										$prog = round($prog, 2);
								?>
								<li>
									<div class="d-block w-100">
										<b><?php echo $v ?></b>
									</div>
									<div class="d-flex w-100">
									<span class=""><?php echo isset($ans[$row['id']][$k]) ? count($ans[$row['id']][$k]) : 0 ?>/<?php echo $taken ?></span>
									<div class="mx-1 col-sm-8"">
									<div class="progress w-100" >
					                  <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $prog ?>%">
					                    <span class="sr-only"><?php echo $prog ?>%</span>
					                  </div>
					                </div>
					                </div>
					                <span class="badge badge-info"><?php echo $prog ?>%</span>
									</div>
								</li>
								<?php endforeach; ?>
								</ul>
						</div>	
					</div>
					<?php } ?>
					<?php 
						$statement = $conn->prepare("SELECT * FROM questions WHERE survey_id = :id AND type = 'textfield_s' ORDER BY ABS(order_by) ASC, ABS(id) ASC");
						$statement->bindParam(':id', $id);
						$statement->execute();
						while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
					?>
					<div class="callout callout-info">
						<h5><?php echo $row['question'] ?></h5>	
						<div class="col-md-12 bg-dark py-1">
							<div class="d-block tfield-area w-100">
								<?php if(isset($ans[$row['id']])): ?>
								<?php foreach($ans[$row['id']] as $val): ?>
								<blockquote class="text-dark"><?php echo $val ?></blockquote>
								<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>	
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('#manage-survey').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_answer',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Gracias por contestar",'success')
					setTimeout(function(){
						location.href = 'index.php?page=survey_widget'
					},2000)
				}
			}
		})
	})
</script>