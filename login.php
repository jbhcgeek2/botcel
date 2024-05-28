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
	//validamos la sesion
	
?>

<body class="app app-login p-0">    	

    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			    <div class="app-auth-body mx-auto">	
				    <div class="app-auth-branding mb-4"><a class="app-logo" href="../"><img class="logo-icon me-2" src="assets/images/logo-postRepair.png" alt="logo"></a></div>
							<h2 class="auth-heading text-center mb-5">Identificate</h2>

			        <div class="auth-form-container text-start">  
									<div class="email mb-3">
										<label class="sr-only" for="userName">Usuario</label>
										<input id="userName" name="userName" type="text" class="form-control signin-email" placeholder="Nombre de Usuario" required="required">
									</div><!--//form-group-->
									<div class="password mb-3">
										<label class="sr-only" for="passLog">Password</label>
										<input id="passLog" name="signin-password" type="password" class="form-control signin-password" placeholder="Password" required="required">
									<div class="extra mt-3 row justify-content-between">
									
									
							</div><!--//extra-->
						</div><!--//form-group-->
						<div class="text-center">
							<!-- <button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Entrar</button> -->
							<a href="#!" id="brtSession" class="btn app-btn-primary w-100 theme-btn mx-auto">Entrar</a>
						</div>
						<!-- <br> -->
						<!-- <p class="text-center">
							Aun no tiene una cuenta? <a href="registro.php"><strong>Registrate</strong></a>
						</p> -->
						<div class="auth-option text-center pt-5">Aun no tienes cuenta? Registrate <a class="text-link" href="registro.php" >Aqui</a>.</div>
						
						
						
						
					</div><!--//auth-form-container-->	

			    </div><!--//auth-body-->
		    
			    <footer class="app-auth-footer">
				    <div class="container text-center py-3">
				         <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
			        <small class="copyright">Disenado por </i> by <a class="app-link" href="https://www.tecuanisoft.com" target="_blank">TecuaniSoft</a></small>
				       
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
					    <div>PostRepair es la solución integral que tu taller necesita para crecer y ser más eficiente.</div>
				    </div>
				</div>
		    </div><!--//auth-background-overlay-->
	    </div><!--//auth-background-col-->
    
    </div><!--//row-->


</body>
<script src="assets/js/login.js"></script>
<script src="assets/js/swetAlert.js"></script>

</html> 

