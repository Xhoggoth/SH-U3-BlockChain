<?php include 'db_connect.php' ?>
<?php 
$stmt = $conn->prepare("SELECT DISTINCT(survey_id) FROM answers WHERE user_id = :user_id");
$stmt->execute(array(':user_id' => $_SESSION['login_id']));
$answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="col-lg-12">
	<div class="d-flex w-100 justify-content-center align-items-center mb-2">
		<label for="" class="control-label">Buscar encuesta</label>
		<div class="input-group input-group-sm col-sm-5">
          <input type="text" class="form-control" id="filter" placeholder="Encuesta...">
          <span class="input-group-append">
            <button type="button" class="btn btn-primary btn-flat" id="search">Buscar</button>
          </span>
        </div>
	</div>
	<div class=" w-100" id='ns' style="display: none"><center><b>Sin resultados.</b></center></div>
	<div class="row">
	<?php 
		$stmt = $conn->query("SELECT * FROM survey_set ORDER BY RAND()");
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
	?>
		<div class="col-md-3 py-1 px-1 survey-item">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<h3 class="card-title"><?php echo ucwords($row['title']) ?></h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
				<div class="card-body" style="display: block;">
					<?php echo $row['description'] ?>
					<div class="row">
						<hr class="border-primary">
						<div class="d-flex justify-content-center w-100 text-center">
							<a href="index.php?page=view_survey_report&id=<?php echo $row['id'] ?>" class="btn btn-sm bg-gradient-primary"><i class="fa fa-poll"></i> Ver informe</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endwhile; ?>
	</div>
</div>
<script>
	function find_survey(){
		start_load()
		var filter = $('#filter').val()
			filter = filter.toLowerCase()
			console.log(filter)
		$('.survey-item').each(function(){
			var txt = $(this).text()
			if((txt.toLowerCase()).includes(filter) == true){
				$(this).toggle(true)
			}else{
				$(this).toggle(false)
			}
			if($('.survey-item:visible').length <= 0){
				$('#ns').show()
			}else{
				$('#ns').hide()
			}
		})
		end_load()
	}
	$('#search').click(function(){
		find_survey()
	})
	$('#filter').keypress(function(e){
		if(e.which == 13){
		find_survey()
		return false;
		}
	})
</script>