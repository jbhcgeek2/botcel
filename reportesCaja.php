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
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Reportes de Caja</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="index.php">Ir a Inicio</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="reportes">
      
                    <div class="row">
                      
                      <div class="col-6 col-lg-3">
                        <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                          <div class="app-card-body p-3 p-lg-4">
                            <h5 class="stats-type mb-1">Ventas del dia</h5>
                          </div><!--//app-card-body-->
                          <a class="app-card-link-mask" href="ventasDelDia.php"></a>
                        </div><!--//app-card-->
                      </div><!--//col-->

                      <div class="col-6 col-lg-3">
                        <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                          <div class="app-card-body p-3 p-lg-4">
                            <h5 class="stats-type mb-1">Ventas por Usuario</h5>
                          </div><!--//app-card-body-->
                          <a class="app-card-link-mask" href="ventasUsuario.php"></a>
                        </div><!--//app-card-->
                      </div><!--//col-->

                      <div class="col-6 col-lg-3">
                        <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                          <div class="app-card-body p-3 p-lg-4">
                            <h5 class="stats-type mb-1">Salidas y Entradas de Efectivo</h5>
                          </div><!--//app-card-body-->
                          <a class="app-card-link-mask" href="salEntEfec.php"></a>
                        </div><!--//app-card-->
                      </div><!--//col-->

                      <div class="col-6 col-lg-3">
                        <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                          <div class="app-card-body p-3 p-lg-4">
                            <h5 class="stats-type mb-1">Salidas y Entradas de Mercancia</h5>
                          </div><!--//app-card-body-->
                          <a class="app-card-link-mask" href="salEntMerca.php"></a>
                        </div><!--//app-card-->
                      </div><!--//col-->

                      

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
</body>
</html> 

