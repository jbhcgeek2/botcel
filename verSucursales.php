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
			    
			    <h1 class="app-page-title">Listado de Sucursales</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="altaSucursal.php">Registrar Sucursal</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>Sucursal</th>
                            <th>Telefono</th>
                            <th>Estatus</th>
                            <th>Ver</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $sqlSuc = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion'";
                            try {
                              $querySuc = mysqli_query($conexion, $sqlSuc);
                              if(mysqli_num_rows($querySuc) > 0){
                                while($fetchSuc = mysqli_fetch_assoc($querySuc)){
                                  $nombreSuc = $fetchSuc['nombreSuc'];
                                  $telSuc = $fetchSuc['telefonoSuc'];
                                  $estatuSuc = $fetchSuc['estatusSuc'];
                                  if($estatuSuc == 1){
                                    $estatuSuc = "Activa";
                                  }else{
                                    $estatuSuc = "Baja";
                                  }
                                  $idSuc = $fetchSuc['idSucursal'];

                                  echo "<tr>
                                    <td>$nombreSuc</td>
                                    <td>$telSuc</td>
                                    <td>$estatuSuc</td>
                                    <td>
                                      <a href='verInfoSuc.php?data=$idSuc'>Ver</a>
                                    </td>
                                  </tr>";
                                }//fin del while
                              }else{
                                //sin sucursales registradas
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

