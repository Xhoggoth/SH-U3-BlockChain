<div class="col-lg-12">
	<div class="card">
		<div class="card-body">
			<form action="" id="manage_user">
				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6 border-right">
						<b class="text-muted">Información personal</b>
						<div class="form-group">
							<label for="" class="control-label">Primer Nombre</label>
							<input type="text" name="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? $firstname : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Segundo Nombre</label>
							<input type="text" name="middlename" class="form-control form-control-sm"  value="<?php echo isset($middlename) ? $middlename : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Apellidos</label>
							<input type="text" name="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? $lastname : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Celular</label>
							<input type="text" name="contact" class="form-control form-control-sm" required value="<?php echo isset($contact) ? $contact : '' ?>">
						</div>
						<div class="form-group">
							<label class="control-label">Dirección</label>
							<textarea name="address" id="" cols="30" rows="4" class="form-control" required><?php echo isset($address) ? $address : '' ?></textarea>
						</div>
					</div>
					<div class="col-md-6">
						<b class="text-muted">Credenciales del sistema</b>
						<?php if($_SESSION['login_type'] == 1): ?>
						<div class="form-group">
							<label for="" class="control-label">Rol del usuario</label>
							<select name="type" id="type" class="custom-select custom-select-sm">
								<option value="2" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>Alumno</option>
								<option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Admin</option>
							</select>
						</div>
						<?php else: ?>
							<input type="hidden" name="type" value="3">
						<?php endif; ?>
						<div class="form-group">
							<label class="control-label">Correo</label>
							<input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
							<small id="#msg"></small>
						</div>
						<div class="form-group">
							<label class="control-label">Contraseña</label>
							<input type="password" class="form-control form-control-sm" name="password" <?php echo isset($id) ? "":'required' ?>>
							<small><i><?php echo isset($id) ? "Deje este campo en blanco si no desea cambiar su contraseña":'' ?></i></small>
						</div>
						<div class="form-group">
							<label class="label control-label">Confirmar Contraseña</label>
							<input type="password" class="form-control form-control-sm" name="cpass" <?php echo isset($id) ? 'required' : '' ?>>
							<small id="pass_match" data-status=''></small>
							<?php
								$AES = array(
									"config" => "openssl.cnf",
									'private_key_bits' => 2048,
									'default_md' => "sha256",
								);
								function str_openssl_enc($str, $token){
									//Utilizamos la llave publica para cifrar los datos
									$key = 'AdminUTCpublic_key.pem';
									$cipher = "AES-128-CTR";
									$options = 0;
									$encrypted_str = openssl_encrypt($str, $cipher, $key, $options, $token);
									return $encrypted_str;
								}
								$token = openssl_random_pseudo_bytes(16);

								// Generamos las llaves AES de las calificaciones
								$privateKey = openssl_pkey_new($AES);
								openssl_pkey_export($privateKey, $privateKeyPEM, NULL,  $AES);

								$details = openssl_pkey_get_details($privateKey);
								$publicKeyPEM = $details['key'];

								// Función para guardar las llaves en archivos pueden ser ".key o .pem"
								file_put_contents('private_key.key', $privateKeyPEM);
								file_put_contents('public_key.key', $publicKeyPEM);

								$token_key = bin2hex($token);
							?>
							<input type="hidden" name="token" value="<?php echo $token_key ?> ">
						</div>
					</div>
				</div>
				<hr>
				<div class="col-lg-12 text-right justify-content-center d-flex">
					<button class="btn btn-primary mr-2">Guardar</button>
					<button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=user_list'">Cancelar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$('[name="password"],[name="cpass"]').keyup(function(){
		var pass = $('[name="password"]').val()
		var cpass = $('[name="cpass"]').val()
		if(cpass == '' ||pass == ''){
			$('#pass_match').attr('data-status','')
		}else{
			if(cpass == pass){
				$('#pass_match').attr('data-status','1').html('<i class="text-success">La contraseña coincide.</i>')
			}else{
				$('#pass_match').attr('data-status','2').html('<i class="text-danger">La contraseña no coincide.</i>')
			}
		}
	})
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#manage_user').submit(function(e){
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		if($('#pass_match').attr('data-status') != 1){
			if($("[name='password']").val() !=''){
				$('[name="password"],[name="cpass"]').addClass("border-danger")
				end_load()
				return false;
			}
		}
		$.ajax({
			url:'ajax.php?action=save_user',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					alert_toast('Datos guardados correctamente.',"success");
					setTimeout(function(){
						location.replace('index.php?page=user_list')
					},750)
				}else if(resp == 2){
					$('#msg').html("<div class='alert alert-danger'>Correo electrónico ya existente.</div>");
					$('[name="email"]').addClass("border-danger")
					end_load()
				}
			}
		})
	})
</script>