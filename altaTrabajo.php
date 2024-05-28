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
    include("includes/cliente.php");
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Registrar Nuevo Trabajo</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verSucursales.php">Ver Trabajos</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      
                      <form id="dataAltaTrab" class="row">
                        

                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="clienteTrabajo" class="form-label">Cliente <span class='text-danger fw-bold'>*<span></label>
                          <select name="clienteTrabajo" id="clienteTrabajo" aria-describedby="clienteTrabajoFeedBack" class="form-select" required>
                            <option value="" selected>Seleccione...</option>
                            <?php 
                              $clientes = verClientes($idEmpresaSesion);
                              $clientes = json_decode($clientes);
                              if($clientes->status == 'ok'){

                                for ($i=0; $i <count($clientes->data) ; $i++) { 
                                  $nombreCliente = $clientes->data[$i]->nombreCliente;
                                  $cliente = $clientes->data[$i]->idClientes;

                                  echo "<option value='$cliente'>$nombreCliente</option>";
                                }
                              }else{
                                //error de consulta
                                echo "<option>Error de consulta a la BD</option>";
                              }
                            ?>
                          </select>
                          
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                          <label for="tipoServicio" class="form-label">Tipo de Servicio <span class='text-danger fw-bold'>*<span></label>
                          <select name="tipoServicio" id="tipoServicio" class="form-select" aria-describedby="tipoServicioFeedBack" required>
                            <option value="" selected>Seleccione...</option>
                            <?php 
                              $sqlTServ = "SELECT * FROM SERVICIOS WHERE empresaID = '$idEmpresaSesion' AND 
                              estatusCategoria = '1'";
                              try {
                                $queryTServ = mysqli_query($conexion, $sqlTServ);
                                if(mysqli_num_rows($queryTServ) > 0){
                                  while($fetchTServ = mysqli_fetch_assoc($queryTServ)){
                                    $nombreServ = $fetchTServ['nombreServicio'];
                                    $idServicio = $fetchTServ['idServicio'];
                                    echo "<option value='$idServicio'>$nombreServ</option>";
                                  }//fin del while
                                }else{
                                  //sin servicios registrados
                                  echo "<option value='noData'>Registrar Servicio</option>";
                                }
                              } catch (\Throwable $th) {
                                echo "<option value='error'>Error de consulta DB</option>";
                              }
                            ?>
                          </select>
                          <div id="tipoServicioFeedBack" class="invalid-feedback">Selecciona servicio valido</div>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                          <label for="fechaServicio" class="form-label">Fecha Alta<span class='text-danger fw-bold'>*<span></label>
                          <input type="date" id="fechaServicio" name="fechaServicio" 
                          value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                          <div class="invalid-feedback">Indique una fecha valida</div>
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                          <label for="fechaEntrega" class="form-label">Fecha de Entrega a Cliente <span class='text-danger fw-bold'>*<span></label>
                          <input type="date" name="fechaEntrega" id="fechaEntrega" class="form-control" required>
                          <div class="invalid-feedback">Indique una fecha aproximada de entrega</div>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                          <label for="costoServicio" class="form-label">Costo Servicio</label>
                          <input type="number" name="costoServicio" id="costoServicio" class="form-control">
                        </div>

                        <div class="col-sm-12 col-md-2 col-lg-2 mb-3">
                          <label for="anticipoServicio" class="form-label">Anticipo</label>
                          <input type="number" name="anticipoServicio" id="anticipoServicio" class="form-control">
                        </div>

                        
                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="sucursalServicio" class="form-label">Sucursal</label>
                          <input type="text" id="sucursalServicio" name="sucursalServicio" 
                          value="<?php echo $nombreSucursal; ?>" class="form-control" readonly required>
                        </div>

                        

                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="tipoDispositivo" class="form-label">Dispositivo <span class='text-danger fw-bold'>*<span></label>
                          <select name="tipoDispositivo" id="tipoDispositivo" aria-describedby="tipoDispositivoFeedBack" class="form-select" required>
                            <option value="" selected>Seleccione...</option>
                            <option value="Celular">Celular</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Smartwatch">Smartwatch</option>
                          </select>
                          <div id="tipoDispositivoFeedBack" class="invalid-feedback">Selecciona un dispositivo valido</div>
                        </div>


                        <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                          <label for="marcaServicio" class="form-label">Marca <span class='text-danger fw-bold'>*<span></label>
                          <input type="text" name="marcaServicio" id="marcaServicio" class="form-control" required>
                          <div class="invalid-feedback">Indique una marca valida</div>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                          <label for="modeloServicio" class="form-label">Modelo <span class='text-danger fw-bold'>*<span></label>
                          <input type="text" name="modeloServicio" id="modeloServicio" class="form-control" required>
                          <div class="invalid-feedback">indique un modelo valido</div>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="numberDevice" class="form-label">IMEI / ESN / SN <span class='text-danger fw-bold'>*<span></label>
                          <input type="text" name="numberDevice" id="numberDevice" class="form-control" required>
                          <div class="invalid-feedback">Indique un dato valido</div>
                        </div>

                        <div class="col-sm-12 col-md-9 col-lg-7 mb-3">
                          <label for="accesorioServicio" class="form-label">Accesorios</label>
                          <input type="text" name="accesorioServicio" id="accesorioServicio" class="form-control">
                        </div>

                        <div class="col-sm-12 mb-3">
                          <div class="input-group">
                            <span class="input-group-text p-3">Descripcion del Problema <span class='text-danger fw-bold'>*</span></span>
                            <textarea name="descripcionProblema" id="descripcionProblema" style="height:70px;"
                            class="form-control" required></textarea>
                            <div class="invalid-feedback">Indique la problematica que presenta el dispositivo</div>
                          </div>
                        </div>

                        <div class="col-sm-12 mb-3">
                          <div class="input-group">
                            <span class="input-group-text">Observaciones</span>
                            <textarea name="observServicio" id="observServicio" style="height:70px;"
                            class="form-control"></textarea>
                          </div>
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                          <label for="contraDisp" class="form-label">Contraseña Dispositivo</label>
                          <input type="text" name="contraDisp" id="contraDisp" class="form-control">
                        </div>
                        
                      </form>

                      <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                        <a href="#!" class="btn btn-primary" role="buttom" id="altaTrabajo">Registrar</a>
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
    <script src="assets/js/altaTrabajo.js"></script>
</body>
</html> 

