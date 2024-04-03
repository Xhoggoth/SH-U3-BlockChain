<?php include('db_connect.php') ?>
<!-- Info boxes -->
<?php if($_SESSION['login_type'] == 1): ?>
        <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Alumnos UTC</span>
                <span class="info-box-number">
                  <?php $stmt = $conn->query("SELECT * FROM users where type = 2");
                    echo $stmt->rowCount(); ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-poll-h"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Encuestas totales</span>
                 <span class="info-box-number">
                  <?php $stmt = $conn->query("SELECT * FROM survey_set");
                    echo $stmt->rowCount();
                  ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
      </div>

<?php else: ?>
	 <div class="col-12">
          <div class="card">
          	<div class="card-body">
          		Bienvenido <?php echo $_SESSION['login_name'] ?>!
          	</div>
          </div>
      </div>
      <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-poll-h"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Encuestas realizadas</span>
                <span class="info-box-number">
                  <?php $stmt = $conn->query("SELECT distinct(survey_id) FROM answers WHERE user_id = :user_id");
                  $stmt->bindParam(':user_id', $_SESSION['login_id']);
                  $stmt->execute();
                  echo $stmt->rowCount();
                  ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
      </div>
          
<?php endif; ?>
