<!DOCTYPE html>
<html lang="en">
<?php 
	session_start();
	if(!empty($_SESSION['cochiUsuario'])){
		header('Location:index.php');
		?>
		<script>
			window.location = "index.php";
		</script>
		<?php
	}
	include("includes/head.php");
  include("includes/conexion.php");
	//validamos la sesion
	
?>

<body class="app app-login p-0">    	
    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			    <div class="app-auth-body mx-auto">	
				    <div class="app-auth-branding mb-4"><a class="app-logo" href="index.html"><img class="logo-icon me-2" src="assets/images/app-logo.svg" alt="logo"></a></div>
					  <h2 class="auth-heading text-center mb-5">Nuevo Registro</h2>
			        <div class="auth-form-container text-start">
						
              <form class="auth-form login-form" id="formRegistro">

							<div class="nombreEmpresa mb-3">
								<label class="" for="nombreEmpresa">Nombre de Empresa</label>
								<input id="nombreEmpresa" name="nombreEmpresa" type="email" class="form-control nombreEmpresa" placeholder="Nombre de Empresa" required="required">
							</div><!--//Nombre de empresa-->

              <div class="userName mb-3">
								<label class="" for="userName">Nombre de Usuario</label>
								<input id="userName" name="userName" type="text" class="form-control userName" placeholder="Nombre de Usuario" required="required">
							</div><!--//nombre de usuario-->
              <div class="userName mb-3">
								<label class="" for="emailUser">Correo</label>
								<input id="emailUser" name="emailUser" type="text" class="form-control emailUser" placeholder="Correo Electronico" required="required">
							</div><!--//nombre de usuario-->
              <div class="nombreUser mb-3">
								<label class="" for="nombreUser">Nombre(s)</label>
								<input id="nombreUser" name="nombreUser" type="text" class="form-control nombreUser" placeholder="Nombre(s)" required="required">
							</div><!--//nombre de usuario-->
              <div class="row">
                <div class="d-flex ">
                  <div class="apPaterno mb-3 ">
                    <label class="" for="apPaterno">Apellido Paterno</label>
                    <input id="apPaterno" name="apPaterno" type="text" class="form-control apPaterno" placeholder="Apellido Paterno">
                  </div><!--//Apellido Paterno-->
                  <div class="apPaterno mb-3 ">
                    <label class="" for="apMaterno">Apellido Materno</label>
                    <input id="apMaterno" name="apMaterno" type="text" class="form-control apMaterno" placeholder="Apellido Materno">
                  </div><!--//Apellido Materno-->
                </div>
                
              </div>
              <div class="planSelect mb-3">
								<label class="" for="suscripcion">Suscripcion</label>
								<select class="form-select" name="suscripcion" id="suscripcion">
                  <option value="" selected disabled>Seleccione...</option>
                  <?php 
                    //mostraremos las suscipciones
                    $sqlSus = "SELECT * FROM SUSCRIPCION";
                    try {
                      $querySus = mysqli_query($conexion, $sqlSus);
                      $fetchSus = mysqli_fetch_assoc($querySus);
                      $nombreSus = $fetchSus['nombreSuscripcion'];
                      $idSus = $fetchSus['idSuscripcion'];

                      echo "<option value='$idSus'>$nombreSus</option>";
                    } catch (Throwable $th) {
                      echo "<option value='' selected disabled>Error de BD</option>";
                    }
                  ?>
                </select>
							</div><!--//plan-->
              
              

              <div class="password mb-3">
								<label class="" for="contra1">Contrasena</label>
								<input id="contra1" name="contra1" type="password" class="form-control contra1" placeholder="****" required="required">
							</div><!--//Contra 1-->
							<div class="password mb-3">
								<label class="" for="contra2">Confirme Contrasena</label>
								<input id="contra2" name="contra2" type="password" class="form-control contra2" placeholder="****" required="required">
								<div class="extra mt-3 row justify-content-between">
									<div class="col-6">
										<div class="form-check">
											<!-- <input class="form-check-input" type="checkbox" value="" id="RememberPassword">
											<label class="form-check-label" for="RememberPassword">
											Recuerdame
											</label> -->
										</div>
									</div><!--//col-6-->
									<div class="col-6">
										<div class="forgot-password text-end">
											<a href="reset-password.html">Contrasena Olvidada?</a>
										</div>
									</div><!--//col-6-->
								</div><!--//extra-->
							</div><!--//form-group-->

							<div class="text-center">
								<a href="#!" id="registroNuevo" class="btn app-btn-primary w-100 theme-btn mx-auto">Registrarme</a>
							</div>
						</form>
						
						<div class="auth-option text-center pt-5">Ya tienes cuenta? Ingresa <a class="text-link" href="login.php" >Aqui</a>.</div>
					</div><!--//auth-form-container-->	

			    </div><!--//auth-body-->
		    
			    <footer class="app-auth-footer">
				    <div class="container text-center py-3">
				         <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
			        <small class="copyright">Disenado por <a class="app-link" href="https://tecuanisoft.com" target="_blank">TecuaniSoft</a></small>
				       
				    </div>
			    </footer><!--//app-auth-footer-->	
		    </div><!--//flex-column-->   
	    </div><!--//auth-main-col-->
			
	    <div class="col-12 col-md-5 col-lg-6 h-100 auth-background-col">
		    <div class="auth-background-holder">
		    </div>
		    <div class="auth-background-mask"></div>
		    <div class="auth-background-overlay p-3 p-lg-5">
			    <div class="d-flex flex-column align-content-end h-100">
				    <div class="h-100"></div>
				    <div class="overlay-content p-3 p-lg-4 rounded">
					    <h5 class="mb-3 overlay-title">El control de tu taller desde cualquier lugar</h5>
					    <div><strong>PostRepair</strong> es la solución integral que tu taller necesita para crecer y ser más eficiente.</div>
				    </div>
				</div>
		    </div><!--//auth-background-overlay-->
	    </div><!--//auth-background-col-->
    
    </div><!--//row-->
</body>
<script src="assets/js/registro.js"></script>
<script src="assets/js/swetAlert.js"></script>

</html> 

