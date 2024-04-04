
<?php
require_once 'db_connect.php';

if(isset($_POST["send"])){
    $mensajeUTC = $_POST['mensajeUTC'];

    // Validar el tipo de archivo
    $permitted = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];

        if (in_array($file['type'], $permitted)) {
            // Obtener correos de la base de datos
            $query = "SELECT email FROM users";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if ($emails) {
                foreach ($emails as $received) {
                    // Asunto del correo
                    $subject = "Encuesta UTC";

                    // Enviar el archivo adjunto
                    $attachedFile = $file['tmp_name'];
                    $nameFile = $file['name'];
                    $typeFile = $file['type'];

                    //Nombre personalizado para el correo del host
                    $header = "From: Encuestas@UTC.com";
                    $header .= "\r\nMIME-Version: 1.0\r\n";
                    $header .= "Content-Type: multipart/mixed; boundary=\"mixed_boundary\"";

                    $message = "--mixed_boundary\r\n";
                    $message .= "Content-Type: multipart/alternative; boundary=\"alternative_boundary\"\r\n\r\n";
                    $message .= "--alternative_boundary\r\n";
                    $message .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
                    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $message .= "$mensajeUTC \n";
                    $message .= "--mixed_boundary\r\n";
                    $message .= "Content-Type: $typeFile; name=\"$nameFile\"\r\n";
                    $message .= "Content-Transfer-Encoding: base64\r\n";
                    $message .= "Content-Disposition: attachment\r\n\r\n";
                    $message .= chunk_split(base64_encode(file_get_contents($attachedFile))) . "\r\n";
                    $message .= "--mixed_boundary--";

                    // Enviar el correo
                    $mail = mail($received, $subject, $message, $header);
                    if($mail){
                        // echo "<script>alert('Correo enviado a los usuarios!')</script>";
                        header("Location: index.php");
                    } else {
                        echo "<script>alert('Error al enviar el correo!')</script>";
                    }
                }
            } else {
                echo "<script>alert('No hay usuarios en la base de datos')</script>";
            }
        } else {
            echo 'Tipo de archivo no permitido. Por favor, sube un archivo PDF, JPG, JPEG o PNG.';
        }
    } else {
        echo "<script>alert('Error al enviar el archivo adjunto')</script>";
    }
}
?>
<div class="col-lg-12">
	<div class="card">
		<div class="card-body">
			<form action="" action="" method="post" enctype="multipart/form-data" id="manage_user">
				<div class="row">
					<div class="col-md-6 border-right">
						<b class="text-muted">Notificar a Alumnos</b>
						
						<div class="form-floating">
							<label class="control-label">Mensaje:</label>
							<textarea	name="mensajeUTC" cols="30" rows="4" class="form-control" placeholder="Redacte el anuncio publicitario aquí" id="floatingTextarea2" style="height: 100px" required></textarea>
						</div>
						<div class="form-group">
							<label for="" class="control-label">Archivo:</label>
							<input type="file" name="file" class="form-control form-control-lg" id="formFileLg" accept=".pdf, .jpg, .jpeg, .png" required>
						</div>
					</div>
					<div class="col-md-6">
						<center>
						<img src="assets/img/logo.png" alt="" width="250px" height="200px" />
						</center>
					</div>
				</div>
				<hr>
				<div class="col-lg-12 text-right justify-content-center d-flex">
					<button class="btn btn-primary mr-2" type="submit" name="send" id="send">Notificar</button>
					<button class="btn btn-secondary" type="button" onclick="location.href = 'index.php'">Cancelar</button>
				</div>
			</form>
		</div>
	</div>
</div>