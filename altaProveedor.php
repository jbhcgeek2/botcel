<!DOCTYPE html>
<html lang="en"> 
<?php
session_start();


 	include("includes/head.php");
?>

<body class="app">   	
  <?php
    include("includes/header.php");
    include("includes/empresas.php");
    include("includes/conexion.php");
    include("includes/articulos.php");
    
    if($rolUsuario == "Administrador"){
      
    }else{
      ?>
      <script>
        window.location = "../";
      </script>
      <?php
    }
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Registro de Proveedor</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verProveedores.php">Ver Proveedores</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      <form id="datosNewProv" class="row">
                        <div class="col-sm-12 col-md-4 col-lg-6 mb-3">
                          <label for="nombreProvAlta" class="form-label">Nombre Proveedor</label>
                          <input type="text" name="nombreProvAlta" id="nombreProvAlta" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                          <label for="telProvAlta" class="form-label">Telefono</label>
                          <input type="number" name="telProvAlta" id="telProvAlta" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                          <label for="mailProvAlta" class="form-label">Correo</label>
                          <input type="text" name="mailProvAlta" id="mailProvAlta" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                          <label for="direProvAlta" class="form-label">Direccion</label>
                          <input type="text" name="direProvAlta" id="direProvAlta" class="form-control">
                        </div>
                      </form>

                      <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                        <a href="#!" id="btnAltaProv" class="btn btn-primary" role="buttom">Registrar</a>
                      </div>
                    </div>

					        </div><!--//app-card-body-->
				        </div><!--//app-card-->
			        </div><!--//col-->
          <hr class="my-4">
        


			    
	    
	    <?php 
        include("includes/footer.php");
      ?>
    </div><!--//app-wrapper-->    					

 
    <!-- Javascript -->          
    <script src="assets/plugins/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>


    
    <!-- Page Specific JS -->
    <script src="assets/js/app.js"></script> 
    <script src="assets/js/swetAlert.js"></script>
    <script src="assets/js/altaProveedor.js"></script>
</body>
</html> 

