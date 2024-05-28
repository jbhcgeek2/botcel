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
			    
			    <h1 class="app-page-title">Registro de Categorias Servicio</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verCatServicio.php">Ver Categoria de Servicio</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      <form id="dataAltaServCat" class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="nombreCatServ" class="form-label">Categoria de Servicio</label>
                          <input type="text" id="nombreCatServ" name="nombreCatServ" class="form-control">
                        </div>
                        <!-- <div class="col-sm-12 col-md-6 mb-3">
                          <label for="precioFijo" class="form-label">Estatus Categoria</label>
                          <select name="precioFijo" id="precioFijo" class="form-select">
                            <option value=""selected disabled>Seleccione...</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                          </select>
                        </div> -->
                        <div class="col-sm-12 col-md-6 col-lg-8 mb-3">
                          <label for="descripcionCatServ" class="form-label">Descripcion</label>
                          <input type="text" id="descripcionCatServ" name="descripcionCatServ" class="form-control">
                        </div>
                        
                        
                        

                        
                        
                      </form>
                      <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                        <a href="#!" class="btn btn-primary" role="buttom" id="altaCatServ">Registrar</a>
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
    <script src="assets/js/altaCatServicio.js"></script>
    
</body>
</html> 

