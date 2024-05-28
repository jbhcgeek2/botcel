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
			    
			    <h1 class="app-page-title">Listado de Categorias de Servicios</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="altaCatServicio.php">Registrar Categoria de Servicio</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoServicio">
      
                    <div class="row">
                      <table class="table table-striped align-middlet">
                        <thead>
                          <tr>
                            <th>Categoria</th>
                            <th>Estatus</th>
                            <th>Editar</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $sqlServ = "SELECT * FROM CATEGORIASERVICIO WHERE empresaID = '$idEmpresaSesion'";
                            try {
                              $queryServ = mysqli_query($conexion, $sqlServ);
                              if(mysqli_num_rows($queryServ) > 0){
                                while($fetchCatServ = mysqli_fetch_assoc($queryServ)){

                                  $nombreCatServ = $fetchCatServ['nombreCatServ'];
                                  $estatusCatServ = $fetchCatServ['estatusCategoriaServ'];
                                  $idCatServ = $fetchCatServ['idCategoriaServ'];

                                  if($estatusCatServ == '1'){
                                    $estatusCatServ = "Activo";
                                  }else{
                                    $estatusCatServ = "Baja";
                                  }
                                  

                                  echo "<tr>
                                    <td>$nombreCatServ</td>
                                    <td>$estatusCatServ</td>
                                    <td>
                                      <a href='verInfoCatServ.php?data=$idCatServ' class='btn btn-primary'>Editar</a>
                                    </td>
                                  </tr>";

                                }//fin del while
                              }else{
                                //sin sucursales registradas
                                echo "<tr>
                                  <td colspan='4' class='text-center'><h5>Sin Proveedores Registrados</h5></td>
                                </tr>";
                              }
                            } catch (\Throwable $th) {
                              //throw $th;
                            }
                          ?>
                        </tbody>
                      </table>
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

