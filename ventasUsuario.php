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

    //unicamente el usuario administrador podra ver esta seccion
    if($rolUsuario != "Administrador"){
      ?>
      <script>
        window.location = "reportesCaja.php";
      </script>
      <?php
    }
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Ventas Por Usuario</h1>
			    			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="reportesCaja.php">Ver Reportes</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="reportes">
      
                    <div class="row">
                      <div class="col-sm-12 col-md-3 col-lg-2">
                        <label for="form-label">Fecha Inicio</label>
                        <input type="date" id="fechaIniMov" class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-3 col-lg-2">
                        <label for="form-label">Fecha Fin</label>
                        <input type="date" id="fechaFinMov" class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-4 col-lg-4">
                        <label for="usuarioVenta" label="form-label">Usuario</label>
                        <select name="usuarioVenta" id="usuarioVenta" class="form-select">
                          <option value="" selected disabled>Seleccione</option>
                          <?php 
                            //consultamos los usuarios activos
                            $sqlU = "SELECT * FROM USUARIOS WHERE empresaID = '$idEmpresaSesion' AND 
                            statusUsuario = '1'";
                            try {
                              $queryU = mysqli_query($conexion, $sqlU);
                              if(mysqli_num_rows($queryU) > 0){
                                while($fetchU = mysqli_fetch_assoc($queryU)){
                                  $nombreUs = $fetchU['nombreUsuario']." ".$fetchU['apPaternoUsuario']." ".$fetchU['apMaternoUsuario'];
                                  $idUs = $fetchU['idUsuario'];

                                  echo "<option value='$idUs'>$nombreUs</option>";
                                }//fin del while
                              }else{
                                //sin usuario registrados
                                echo "<option value='' disabled>Sin Usuario</option>";
                              }
                            } catch (\Throwable $th) {
                              //throw $th;
                              echo "<option value='' disabled>Error de consulta</option>";
                            }
                          ?>
                        </select>
                      </div>
                      
                      <div class="col-sm-12 col-md-3 mt-4">
                        <a href="#!" class="btn btn-primary" role="buttom" id="btnBuscarMovs">Buscar</a>
                      </div>
                      
                    </div>

                    <hr clas="my-4">

                    <h5>Articulos Vendidos: <span class='stext-success fw-bold' id='totArti'></span> </h5>
                    <h5>Total de Venta: <span class='text-success fw-bold' id='totVenta'></span></h5>

                    <div class="row" style="height:600px;overflow-y:scroll;">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Sucursal</th>
                          </tr>
                        </thead>
                        <tbody id="resultBusqueda">

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
    <script src="assets/js/ventasUsuario.js"></script>
</body>
</html> 

