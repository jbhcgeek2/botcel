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
    include("includes/ventas.php");
    include("includes/cliente.php");
   
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Listado de trabajos</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="altaTrabajo.php">Registrar Trabajo</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->


                  <div class="col-sm-12 col-md-10 offset-md-1 mt-3">
                    <div class="row">
                      <div class="col-sm-12 col-md-3 mb-3">
                        <label for="buscarEstatus" class="form-label">Buscar Por Estatus</label>
                        <select name="buscarEstatus" id="buscarEstatus" class="form-select">
                          <option value="" selected>Seleccione...</option>
                          <option value="Activo">Activo</option>
                          <option value="En Proceso">En Proceso</option>
                          <option value="En Espera">En Espera</option>
                          <option value="Finalizado">Finalizado</option>
                          <option value="Cancelado">Cancelado</option>
                        </select>
                      </div>
                      <div class="col-sm-12 col-md-6 mb-3">
                        <label for="clienteNombre" class="form-label">Buscar Por Cliente</label>
                        <input type="text" id="clienteNombre" class="form-control">
                      </div>
                    </div>
                  </div>
                  

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Cliente</th>
                          <th>Tipo de Trabajo</th>
                          <th>Fecha de Registro</th>
                          <th>Fecha de Entrega</th>
                          <th>Estatus</th>
                          <th>Ver</th>
                        </tr>
                      </thead>
                      <tbody id="resBusqueda">
                        <?php
                          // consultamos los trabajos que esten activos
                          $sqlTra = "SELECT * FROM TRABAJOS a INNER JOIN CLIENTES b ON a.clienteID = b.idClientes 
                          INNER JOIN SERVICIOS c ON a.servicioID = c.idServicio WHERE 
                          a.empresaID = '$idEmpresaSesion' AND a.estatusTrabajo <> 'Finalizado'";

                          $estatus = ["Activo","En Proceso","En Espera","Finalizado","Cancelado"];
                          $colorStatus = ["Activo"=>'badge rounded-pill text-bg-success',
                          "En Proceso"=>'badge rounded-pill text-bg-warning',
                          "En Espera"=>'badge rounded-pill text-bg-secondary',
                          "Finalizado"=>'badge rounded-pill text-bg-danger',
                          "Cancelado"=>'badge rounded-pill text-bg-dark'];

                          try {
                            $queryTra = mysqli_query($conexion, $sqlTra);
                            if(mysqli_num_rows($queryTra) > 0){
                              while($fetchTra = mysqli_fetch_assoc($queryTra)){

                                $cliente = $fetchTra['nombreCliente'];
                                $tipoTra = $fetchTra['nombreServicio'];
                                $fechaTra = $fetchTra['fechaTrabajo'];
                                $fechaEntrega = $fetchTra['fechaEntrega'];
                                $estatus = $fetchTra['estatusTrabajo'];
                                $idTra = $fetchTra['idTrabajo'];
                                $colorEstatus = $colorStatus[$estatus];

                                echo "<tr>
                                  <td>$cliente</td>
                                  <td>$tipoTra</td>
                                  <td>$fechaTra</td>
                                  <td>$fechaEntrega</td>
                                  <td><span class='$colorEstatus'>$estatus</span></td>
                                  <td>
                                    <a href='verInfoTrabajo.php?data=$idTra' class='btn btn-primary'>Ver</a>
                                  </td>
                                </tr>";
                              }//fin del while
                            }else{
                              //sin trabajos activos
                            }
                          } catch (\Throwable $th) {
                            //throw $th;
                          }
                        ?>
                      </tbody>
                    </table>

                    
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
    <script src="assets/js/verTrabajos.js"></script>
    
</body>
</html> 

