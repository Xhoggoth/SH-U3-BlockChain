<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('db_connect.php');
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Mi portal UTC</title>
  <!-- Icon -->
  <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo.png">
 	

<?php include('header.php'); ?>
<?php 
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");

?>

</head>
<style>
	body{
		width: 100%;
	    height: calc(100%);
	    position: fixed;
	    top:0;
	    left: 0
	    /*background: #007bff;*/
	}
	main#main{
		width:100%;
		height: calc(100%);
		display: flex;
	}

</style>

<body class="bg-dark">


  <main id="main" >
  	
  		<div class="align-self-center w-100">
		<h4 class="text-white text-center"><b>Mi Portal UTC</b></h4>
  		<div id="login-center" class="bg-dark row justify-content-center">
  			<div class="card col-md-4">
  				<div class="card-body">
					<center><img src="assets/img/logo.png" alt="" width="150px" height="100px"></center>
  					<form id="login-form" >
  						<div class="form-group">
  							<label for="email" class="control-label text-dark">Correo:</label>
  							<input type="text" id="email" name="email" class="form-control form-control-sm">
  						</div>
  						<div class="form-group">
  							<label for="password" class="control-label text-dark">Contraseña:</label>
  							<input type="password" id="password" name="password" class="form-control form-control-sm">
							<input type="checkbox" id="showPassword" /><br>
                  			<label for="showPassword" style="color: black; font-size: .7em;">Mostrar contraseña</label> 
  						</div>
  						<center><button class="btn-sm btn-block btn-wave col-md-4 btn-primary">Iniciar Sesión</button></center>
  					</form>
  				</div>
  			</div>
  		</div>
  		</div>
  </main>

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>


</body>
<script>
	document.getElementById('showPassword').onclick = function() {
		if ( this.checked ) {
		document.getElementById('password').type = "text";
		} else {
		document.getElementById('password').type = "password";
		}
	};
	$('#login-form').submit(function(e){
		e.preventDefault()
		$('#login-form button[type="button"]').attr('disabled',true).html('Iniciando sesión...');
		if($(this).find('.alert-danger').length > 0 )
			$(this).find('.alert-danger').remove();
		$.ajax({
			url:'ajax.php?action=login',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)
		$('#login-form button[type="button"]').removeAttr('disabled').html('Login');

			},
			success:function(resp){
				if(resp == 1){
					location.href ='index.php?page=home';
				}else{
					$('#login-form').prepend('<div class="alert alert-danger">El correo o la contraseña son incorrectos.</div>')
					$('#login-form button[type="button"]').removeAttr('disabled').html('Login');
					// location.href ='index.php?page=home';
				}
			}
		})
	})
	$('.number').on('input',function(){
        var val = $(this).val()
        val = val.replace(/[^0-9 \,]/, '');
        $(this).val(val)
    })
</script>	
</html>