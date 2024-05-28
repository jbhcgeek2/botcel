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
			    
			    <h1 class="app-page-title">Registro de Servicios</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verServicios.php">Ver Servicios</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      <?php 
                        //verificamos si el usuario cuenta con permisos para registrar una nueva sucursal
                        $sql1 = "SELECT *,(SELECT count(*) FROM SERVICIOS c WHERE 
                        c.empresaID = a.idEmpresa ) AS numServAlta FROM EMPRESAS a INNER JOIN SUSCRIPCION b ON 
                        a.suscripcionID = b.idSuscripcion WHERE a.idEmpresa = '$idEmpresaSesion'";
                        try {
                          $query1 = mysqli_query($conexion, $sql1);
                          if(mysqli_num_rows($query1) > 0){
                            $fetch1 = mysqli_fetch_assoc($query1);
                            $procedeAlta = "no";
                            if($fetch1['maxServicios'] == 0){
                              //tiene acceso a sucursales ilimitadas
                              $procedeAlta = "si";
                            }else{
                              if($fetch1['numSerAlta'] < $fetch1['maxServicios']){
                                //podemos continuar
                                $procedeAlta = "si";
                              }else{
                                //ya tiene limite de sucursales
                                $procedeAlta = "no";
                              }
                            }

                            if($procedeAlta == "si"){
                              //mostramos el formato de alta
                              ?>
                              <form id="dataAltaServ" class="row">
                                <div class="col-sm-12 col-md-6 mb-3">
                                  <label for="nombreServ" class="form-label">Servicio</label>
                                  <input type="text" id="nombreServ" name="nombreServ" class="form-control">
                                </div>
                                <div class="col-sm-12 col-md-3 mb-3">
                                  <label for="precioFijo" class="form-label">Tipo de Precio</label>
                                  <select name="precioFijo" id="precioFijo" class="form-select">
                                    <option value=""selected disabled>Seleccione...</option>
                                    <option value="1">Fijo</option>
                                    <option value="0">Variable</option>
                                  </select>
                                </div>
                                <div class="col-sm-12 col-md-3 mb-3">
                                  <label for="precioServ" class="form-label">Precio</label>
                                  <input type="number" id="precioServ" name="precioServ" class="form-control">
                                </div>

                                <div class="col-sm-12 col-md-4 offset-md-4 mb-3">
                                  <label for="catServicio" class="form-label">Categoria de Servicio</label>
                                  <select name="catServicio" id="catServicio" class="form-select">
                                    <option value=""selected disabled>Seleccione...</option>
                                    <option value="newCatServ">Nueva Categoria</option>
                                    <?php 
                                      //consultamos las categorias de la empresa
                                      $sqlCat = "SELECT * FROM CATEGORIASERVICIO WHERE empresaID = '$idEmpresaSesion'";
                                      try {
                                        $queryCat = mysqli_query($conexion, $sqlCat);
                                        if(mysqli_num_rows($queryCat) > 0){
                                          while($fetchCat = mysqli_fetch_assoc($queryCat)){
                                            $nombreCat = $fetchCat['nombreCatServ'];
                                            $idCatServ = $fetchCat['idCategoriaServ'];

                                            echo "<option value='$nombreCat'>$nombreCat</option>";
                                          }//fin del while cat
                                        }else{
                                          //sin categorias Registradas
                                          echo "<option value='noData'>Sin Categorias</option>";
                                        }
                                      } catch (\Throwable $th) {
                                        //throw $th;
                                        echo "<option value=''>Error de cosnulta</option>";
                                      }
                                    ?>
                                  </select>
                                </div>
                                

                                
                                
                              </form>
                              <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                                <a href="#!" class="btn btn-primary" role="buttom" id="altaServ">Registrar</a>
                              </div>
                              
                              <?php
                            }else{
                              //le indicamos que ya tiene el maximo de sucursales
                              ?>
                              <div class="row">
                                <div class="col-sm-12 text-center">
                                  <h5>Se ha llegado al limite de sucursales</h5>
                                  <img src="assets/images/limite.png" width="100px" alt="Limite de Sucursales">
                                  <p>Adquiere un nuevo plan para registrar mas sucursales.</p>
                                </div>
                              </div>
                              <?php
                            }
                          }else{
                            //no se tienen datos de la empresa
                          }
                        } catch (\Throwable $th) {
                          //throw $th;
                        }
                      ?>
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
    <script src="assets/js/altaServicio.js"></script>
</body>
</html> 

