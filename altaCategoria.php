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
    
    if($rolUsuario == "Administrador" || $rolUsuario == "Encargado"){
      
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
			    
			    <h1 class="app-page-title">Registrar Categoria</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title">Captura la informacion de la nueva categoria</h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="productos.php">Ver categorias</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoForm">
      
                    <form id="dataCategoria">
                      <div class="row">

                        <div class="col-sm-12 col-md-8 col-lg-3 mb-3">
                          <label for="nombreCat" class="form-label"><strong class="text-warning">*</strong> Nombre Categoria</label>
                          <input type="text" class="form-control required" name="nombreCat" id="nombreCat">
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                          <label for="estatus" class="form-label"><strong class="text-warning">*</strong> Estatus</label>
                          <select name="estatus" id="estatus" class="form-select">
                            <option value="" selected disabled>Seleccione...</option>
                            <option value="1">Activo</option>
                            <option value="NA">Desactivado</option>
                          </select>
                        </div>
                        <div class="col-sm-12 mb-3">
                          <label for="descripcionCat" class="form-label"><strong class="text-warning">*</strong> Descripcion</label>
                          <input type="text" class="form-control required" name="descripcionCat" id="descripcionCat">
                        </div>

                        <div class="col-sm-12">
                          <a href="#!" class="btn btn-primary w-25" id="saveCat">Guardar</a>
                        </div>
                      </div>
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
    <script src="assets/js/altaCategoria.js"></script>

</body>
</html> 

