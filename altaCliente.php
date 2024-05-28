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
    include("includes/cliente.php");
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Registro de Cliente</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="clientes.php">Ver Clientes</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoForm">
                    <form action="" id="datosCliente">
                      <div class="containe row">

                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label class="form-label" for="nombreCliente">Nombre Cliente</label>
                          <input type="text" id="nombreCliente" name="nombreCliente" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="telefonoCliente" class="form-label">Telefono</label>
                          <input type="text" id="telefonoCliente" name="telefonoCliente" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="emailCliente" class="form-label">Email</label>
                          <input type="text" id="emailCliente" name="emailCliente" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                          <label for="direccionCliente" class="form-label">Direccion</label>
                          <input type="text" id="direccionCliente" name="direccionCliente" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                          <label for="rfcCliente" class="form-label">RFC</label>
                          <input type="text" id="rfcCliente" name="rfcCliente" class="form-control">
                        </div>

                      </div>
                      
                        <a href="#!" class="btn btn-success" id="btnSaveCliente">Guardar</a>
                  
                    </form>
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
    <script src="assets/js/altaCliente.js"></script>
</body>
</html> 

