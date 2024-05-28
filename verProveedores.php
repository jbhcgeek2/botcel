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
			    
			    <h1 class="app-page-title">Listado de Proveedores</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="altaProveedor.php">Registrar Proveedor</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      <table class="table table-striped align-middlet">
                        <thead>
                          <tr>
                            <th>Nombre</th>
                            <th>Telefono</th>
                            <th>Correo</th>
                            <th>Ver</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $sqlSuc = "SELECT * FROM PROVEEDORES WHERE provEmpresaID = '$idEmpresaSesion'";
                            try {
                              $querySuc = mysqli_query($conexion, $sqlSuc);
                              if(mysqli_num_rows($querySuc) > 0){
                                while($fetchSuc = mysqli_fetch_assoc($querySuc)){
                                  $nombreProv = $fetchSuc['nombreProveedor'];
                                  $telProv = $fetchSuc['telProveedor'];
                                  $mailProv = $fetchSuc['mailProveedor'];
                                  $estatuProv = $fetchSuc['estatusProveedor'];
                                  $idProv = $fetchSuc['idProveedor'];
                                  if($estatuProv == 1){
                                    $estatuProv = "Activa";
                                  }else{
                                    $estatuProv = "Baja";
                                  }
                                  $idProv = $fetchSuc['idProveedor'];

                                  echo "<tr>
                                    <td>$nombreProv</td>
                                    <td>$telProv</td>
                                    <td>$mailProv</td>
                                    <td>
                                      <a href='verInfoProv.php?data=$idProv'>Ver</a>
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

