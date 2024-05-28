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
    
    //seccion exclusiva para administradores
    if($rolUsuario != "Administrador"){
      //cargamos el js para redireccionar
      ?>
        <script>
          window.location = 'index.php';
        </script>
      <?php
    }
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Configuracion de cuenta</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="account.php">Mis Datos</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
                    <div class="row">
                      <form id="datosEmpresa">
                        <?php 
                          //consultamos los datos de la empresa
                          $sqlEmp = "SELECT * FROM EMPRESAS WHERE idEmpresa = '$idEmpresaSesion'";
                          try {
                            $queryEmp = mysqli_query($conexion, $sqlEmp);
                            if(mysqli_num_rows($queryEmp) == 1){
                              $fetchEmp = mysqli_fetch_assoc($queryEmp);

                              $nombreEmp = $fetchEmp['nombreEmpresa'];
                              $suscripcion = $fetchEmp['suscripcionID'];
                              ?>
                              <div class="row">
                                <div class="col-sm-12 col-md-4 col-lg-6 mb-3">
                                  <label for="nombreEmpresa" class="form-label">Nombre Empresa</label>
                                  <input type="text" id="nombreEmpresa" name="nombreEmpresa" class="form-control" 
                                  value="<?php echo $nombreEmp; ?>">
                                </div>

                                <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                                  <label for="planEmpresa" class="form-label">Suscripcion</label>
                                  <select name="planEmpresa" id="planEmpresa" class="form-select">
                                    <option value="" selected disabled>Seleccione</option>
                                    <?php 
                                      //consultamos los planes de las empresas
                                      $sqlPlanes = "SELECT * FROM SUSCRIPCION";
                                      $queryPlanes = mysqli_query($conexion, $sqlPlanes);
                                      if(mysqli_num_rows($queryPlanes) > 0){
                                        while($fetchPlanes = mysqli_fetch_assoc($queryPlanes)){
                                          $nombrePlan = $fetchPlanes['nombreSuscripcion'];
                                          $idPlan = $fetchPlanes['idSuscripcion'];
                                          if($idPlan == $suscripcion){
                                            echo "<option value='$idPlan' selected>$nombrePlan</option>";
                                          }else{
                                            echo "<option value='$idPlan'>$nombrePlan</option>";
                                          }
                                          
                                        }//fin del while planes
                                      }else{
                                        //sin planes registrados
                                      }

                                    ?>
                                  </select>
                                </div>
                              </div>
                              

                              <div class="input-group col-sm-12 mb-3">
                                <input type="file" id="logotipo" name="logotipo" class="form-control">
                                <label for="logotipo" class="input-group-text">Sube tu Logotipo</label>
                              </div>

                              <div class="row" style="text-align:center;">
                                <div class="col-sm-12 col-md-4 offset-md-4">
                                  <a href="#!" class="btn btn-primary" id="btnUpdateDatos">Actualizar Datos</a>
                                </div>
                              </div>

                              <hr class="my-4">

                              <!-- seccion de sucursales -->
                              <div class="row">
                                <h5 class="text-center">Sucursales registradas</h5>
                              </div>

                              <table class="table">
                                <thead>
                                  <tr>
                                    <th>Sucursal</th>
                                    <th>Direccion</th>
                                    <th>Telefono</th>
                                    <th>Editar</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                    //consultamos las sucursales de la empresa
                                    $sqlSuc = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion'";
                                    try {
                                      $querySuc = mysqli_query($conexion, $sqlSuc);
                                      if(mysqli_num_rows($querySuc) > 0){
                                        while($fetchSucs = mysqli_fetch_assoc($querySuc)){
                                          $nombreSuc = $fetchSucs['nombreSuc'];
                                          $direccionSuc = $fetchSucs['calleSuc'];
                                          $telSuc = $fetchSucs['telefonoSuc'];
                                          $idSuc = $fetchSucs['idSucursal'];

                                          echo "<tr>
                                            <td>$nombreSuc</td>
                                            <td>$direccionSuc</td>
                                            <td>$telSuc</td>
                                            <td>
                                              <a href='verInfoSuc.php?data=$idSuc' class='btn btn-primary'>
                                                Editar
                                              </a>
                                            </td>
                                          </tr>";
                                        }//fin del while sucursales
                                      }else{
                                        //sin sucursales registreadas
                                        echo "<tr>
                                          <td colspan='4' style='text-align:center;'><h5>Sin Sucursales Registradas</h5></td>
                                        </tr>";
                                      }
                                    } catch (\Throwable $th) {
                                      //error de consulta
                                      echo "<tr>
                                        <td colspan='4' style='text-align:center;'><h5>Error de consulta a la base de datos</h5></td>
                                      </tr>";
                                    }
                                    

                                  ?>
                                </tbody>
                              </table>
                              
                              <?php
                            }else{
                              //empresa no localizada
                              echo "<h5 class='text-center'>Empresa no localizada</h5>";
                            }
                          } catch (\Throwable $th) {
                            echo "<h5 class='text-center'>Error de consulta a la base de datos</h5>";
                          }
                        ?>
                      </form>
                      

                      
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
    <script src="assets/js/settings.js"></script>
</body>
</html> 

