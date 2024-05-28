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

    // Unicamente los vendedores no podran entrar a ver la informacion del trabajo
    
    

    //verificamos la existencia de la solicitud
    $idTicket = $_GET['data'];
    $trabajo = ticketTrabajo($idTicket,$idEmpresaSesion);
    $trabajo = json_decode($trabajo);

    if($trabajo->status == "ok"){
      // print_r($trabajo);

      $nombreCliente = $trabajo->data->nombreCliente;
      $fechaTrabajo = $trabajo->data->fechaTrabajo;
      $sucursal = $trabajo->data->nombreSuc;
      $tipoDispo = $trabajo->data->tipoDispositivo;
      $tipoServicio = $trabajo->data->servicioID;
      $marca = $trabajo->data->marca;
      $modelo = $trabajo->data->modelo;
      $imei = $trabajo->data->imeiClave;
      $accesorios = $trabajo->data->accesorios;
      $problema = $trabajo->data->problema;
      $observaciones = $trabajo->data->observaciones;
      $contraDis = $trabajo->data->contraDispo;
      $fechaEntrega = $trabajo->data->fechaEntrega;
      $costoIni = $trabajo->data->costoInicial;
      $anticipo = $trabajo->data->anticipo;
      $costoFin = $trabajo->data->costoFinal;
      $estatusTrab = $trabajo->data->estatusTrabajo;

      $restante = $costoIni - $anticipo;
       


    }else{
      //lo mandamos fuera
      ?>
        <script>
          window.location = 'verTrabajos.php';
        </script>
      <?php
    }

    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Informacion de Trabajo</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verTrabajos.php">Ver Trabajos</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      
                      <form id="dataAltaTrab" class="row">
                        <input type="hidden" id="datoTrabajo" value="<?php echo $idTicket; ?>">
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="clienteTrabajo" class="form-label">Cliente</label>
                          <input type="text" id="clienteTrabajo" class="form-control" value="<?php echo $nombreCliente; ?>" readonly>
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                          <label for="fechaEntrega" class="form-label">Fecha de Entrega</label>
                          <input type="date" name="fechaEntrega" id="fechaEntrega" class="form-control" required
                          value="<?php echo $fechaEntrega; ?>">
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                          <label for="costoServicio" class="form-label">Costo</label>
                          <input type="number" name="costoServicio" id="costoServicio" class="form-control" 
                          value="<?php echo $costoIni; ?>" readonly>
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                          <label for="anticipoServicio" class="form-label">Anticipo</label>
                          <input type="number" name="anticipoServicio" id="anticipoServicio" class="form-control" 
                          value="<?php echo $anticipo; ?>" readonly>
                        </div>

                        <div class="col-sm-12 col-md-4 offset-md-4">
                          <label for="estatusTrabajo" class="form-label">Estatus Trabajo</label>
                          <select name="estatusTrabajo" id="estatusTrabajo" class="form-select">
                            <option value="">Seleccione</option>
                            <?php 
                              //
                              $estatus = ["Activo","En Proceso","En Espera","Finalizado","Cancelado"];
                              for ($i=0; $i < count($estatus); $i++) { 
                                if($estatus[$i] == $estatusTrab){
                                  echo "<option value='".$estatus[$i]."' selected>".$estatus[$i]."</option>";
                                }else{
                                  if($estatus[$i] == "Finalizado"){
                                    echo "<option value='".$estatus[$i]."' disabled>".$estatus[$i]."</option>";
                                  }else{
                                    echo "<option value='".$estatus[$i]."'>".$estatus[$i]."</option>";
                                  }
                                  
                                }
                              }//fin del for
                            ?>
                          </select>
                        </div>

                        <hr class="my-4">
                        <h4 class="fw-bold">Informacion Tecnica</h4>

                        <div class="col-sm-12 col-md-6 col-lg-5 mb-3">
                          <label for="tipoServicio" class="form-label">Tipo de Servicio</label>
                          <select name="tipoServicio" id="tipoServicio" class="form-select" readonly>
                            <option value="" disabled>Seleccione...</option>
                            <?php 
                              $sqlTServ = "SELECT * FROM SERVICIOS WHERE empresaID = '$idEmpresaSesion' AND 
                              estatusCategoria = '1'";
                              try {
                                $queryTServ = mysqli_query($conexion, $sqlTServ);
                                if(mysqli_num_rows($queryTServ) > 0){
                                  while($fetchTServ = mysqli_fetch_assoc($queryTServ)){
                                    $nombreServ = $fetchTServ['nombreServicio'];
                                    $idServicio = $fetchTServ['idServicio'];
                                    if($idServicio == $tipoServicio){
                                      echo "<option value='$idServicio' selected>$nombreServ</option>";
                                    }else{
                                      echo "<option value='$idServicio' disabled>$nombreServ</option>";
                                    }
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
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="tipoDispositivo" class="form-label">Dispositivo</label>
                          <select name="tipoDispositivo" id="tipoDispositivo" class="form-select" readonly>
                            <option value="" selected>Seleccione...</option>
                            <?php 
                              $dispos = ['Celular','Tablet','Laptop','Desktop','Smartwatch'];
                              for ($i=0; $i < count($dispos); $i++) { 
                                
                                if($tipoDispo == $dispos[$i]){
                                  echo "<option value='".$dispos[$i]."' selected>".$dispos[$i]."</option>";
                                }else{
                                  echo "<option value='".$dispos[$i]."' disabled>".$dispos[$i]."</option>";
                                }
                                
                              }//fin del for
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                          <label for="marcaServicio" class="form-label">Marca</label>
                          <input type="text" name="marcaServicio" id="marcaServicio" class="form-control" 
                          value="<?php echo $marca; ?>" required>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                          <label for="modeloServicio" class="form-label">Modelo</label>
                          <input type="text" name="modeloServicio" id="modeloServicio" class="form-control" 
                          value="<?php echo $modelo; ?>" readonly>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="numberDevice" class="form-label">IMEI / ESN / SN </label>
                          <input type="text" name="numberDevice" id="numberDevice" class="form-control" 
                          value="<?php echo $imei; ?>" readonly>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                          <label for="contraDisp" class="form-label">Contraseña Dispositivo</label>
                          <input type="text" name="contraDisp" id="contraDisp" class="form-control" 
                          value="<?php echo $contraDis; ?>">
                        </div>


                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="fechaServicio" class="form-label">Fecha Alta</label>
                          <input type="date" id="fechaServicio" name="fechaServicio" 
                          value="<?php echo $fechaTrabajo; ?>" class="form-control" readonly>
                        </div>
                        
                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="sucursalServicio" class="form-label">Sucursal</label>
                          <input type="text" id="sucursalServicio" name="sucursalServicio" 
                          value="<?php echo $sucursal; ?>" class="form-control" readonly>
                        </div>

                        

                        

                        

                        <div class="col-sm-12 mb-3">
                          <label for="accesorioServicio" class="form-label">Accesorios</label>
                          <input type="text" name="accesorioServicio" id="accesorioServicio" 
                          value="<?php echo $accesorios; ?>" class="form-control">
                        </div>

                        <div class="col-sm-12 col-md-6 mb-3">
                          <label class="form-label">Descripcion del Problema</label>
                          <textarea name="descripcionProblema" id="descripcionProblema" style="height:70px;"
                          class="form-control" reeadonly><?php echo $problema; ?></textarea>
                        </div>

                        <div class="col-sm-12 col-md-6 mb-3">  
                          <label class="form-label">Observaciones</label>
                          <textarea name="observServicio" id="observServicio" style="height:70px;"
                          class="form-control" readonly><?php echo $observaciones; ?></textarea>
                        </div>
                        
                      </form>

                      <div class="row text-center">
                        <div class="col-sm-12 col-md-6">
                          <a href="#!" class="btn btn-primary" id="btnAddPieza" data-bs-toggle="modal" 
                          data-bs-target="#modalPieza">Registrar Pieza</a>
                        </div>
                        <div class="col-sm-12 col-md-6">
                          <a href="#!" class="btn btn-danger" id="btnFinaliza" data-bs-toggle="modal"
                          data-bs-target="#modalFinaliza">Finalizar Trabajo</a>
                        </div>
                      </div>
                              
                    </div>

                    <div class="row">
                      <hr class="my-4">
                      <h4 class="fw-bold">Articulos utilizados</h4>
                      <br>

                      <?php 
                        //consultaremos las piezas utilizadas en la reparacion
                        $sqlArtiExt = "SELECT a.*,b.nombreArticulo FROM DETALLETRABAJO a INNER JOIN ARTICULOS b 
                        ON a.articuloID = b.idArticulo WHERE a.trabajoID = '$idTicket'";
                        try {
                          $queryArtiExt = mysqli_query($conexion, $sqlArtiExt);
                          $sumArti = 0;
                          if(mysqli_num_rows($queryArtiExt) > 0){
                            while($fetchArtiExt = mysqli_fetch_assoc($queryArtiExt)){
                              $nombreArti = $fetchArtiExt['nombreArticulo'];
                              $cantidad = $fetchArtiExt['cantidad'];
                              $precio = $fetchArtiExt['precioUnitario'];
                              $subtotal = $fetchArtiExt['subTotalArticulo'];

                              $sumArti = $sumArti+$subtotal;

                              echo "<div class='col-sm-12 col-md-3 mb-3'>
                                <label class='form-label' for='nombreArti'>Articulo</label>
                                <input type='text' id='nombreArti' value='$nombreArti' class='form-control' readonly>
                              </div>
                              <div class='col-sm-12 col-md-3 mb-3'>
                                <label for='cantidad' class='form-label'>Cantidad</label>
                                <input type='number' id='cantidad' value='$cantidad' class='form-control' readonly>
                              </div>
                              <div class='col-sm-12 col-md-3 mb-3'>
                                <label for='precioUni' class='form-label'>Precio Unitario</label>
                                <input type='number' id='precioUni' value='$precio' class='form-control' readonly>
                              </div>
                              <div class='col-sm-12 col-md-3 mb-3'>
                                <label for='subtotal' class='form-label'>Subtotal</label>
                                <input type='number' id='subtotal' value='$subtotal' class='form-control' readonly>
                              </div>
                              <hr class='my-4'>
                              ";
                            }//fin del while

                            echo "<input type='hidden' id='sumaTotalArtis' value='$sumArti'>";
                          }else{
                            //no se tienen articulos registrados para este trabajo

                          }
                        } catch (\Throwable $th) {
                          //throw $th;
                        }
                      ?>
                    </div>

                    <div class="modal fade" id="modalPieza" tabindex="-1" data-bs-backdrop="static" 
                      aria-labelledby="modalPiezaLabel" data-bs-keyboard="false" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalPiezaLabel">Registrar Pieza</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-sm-12">
                                <p>Aqui podras registrar las piezas/articulos que se utilicen en este trabajo</p>
                              </div>

                              <div class="col-sm-12 col-md-6 mb-3">
                                <label for="catArticulo" class="form-label">Categoria</label>
                                <select name="catArticulo" id="catArticulo" class="form-select">
                                  <option value="" selected disabled>Seleccione...</option>
                                  <?php 
                                    $sqlCat = "SELECT * FROM CATEGORIA WHERE empresaID = '$idEmpresaSesion' 
                                    AND estatusCategoria = '1'";
                                    try {
                                      $queryCat = mysqli_query($conexion, $sqlCat);
                                      if(mysqli_num_rows($queryCat) > 0){
                                        while($fetchcat = mysqli_fetch_assoc($queryCat)){
                                          $nombreCat = $fetchcat['nombreCategoria'];
                                          $idCat = $fetchcat['idCategoria'];

                                          echo "<option value='$idCat'>$nombreCat</option>";
                                        }//fin del while
                                      }else{
                                        //sin categorias registradas
                                        echo "<option>Sin Categorias</option>";
                                      }
                                    } catch (\Throwable $th) {
                                      //throw $th;
                                      echo "<option>Error de consulta</option>";
                                    }
                                  ?>
                                </select>
                              </div>
                              <div class="col-sm-12 col-md-6 mb-3">
                                <label for="articuloAgrega" class="form-label">Articulo</label>
                                <select name="articuloAgrega" id="articuloAgrega" class="form-select">
                                  <option value="" selected disabled>Seleccione...</option>
                                  <option value=""></option>
                                </select>
                              </div>
                              
                              <div class="col-sm-12 col-md-4 mb-3">
                                <label for="precioArti" class="form-label">Precio de venta</label>
                                <input type="number" id="precioArti" name="precioArti" class="form-control" onchange="updateTotal()">
                              </div>

                              <div class="col-sm-12 col-md-4 mb-3">
                                <label for="cantidadArti" class="form-label">Cantidad</label>
                                <input type="number" id="cantidadArti" name="cantidadArti" class="form-control" onchange="updateTotal()">
                              </div>
                              
                              <div class="col-sm-12 col-md-4 mb-3">
                                <label for="totalExtra" class="form-label">Total</label>
                                <input type="number" id="totalExtra" class="form-control">
                              </div>
                              
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" id="btnSave">Registrar</button>
                          </div>
                        </div>
                      </div>
                    </div>


                    <div class="modal fade" id="modalFinaliza" tabindex="-1" data-bs-backdrop="static" 
                      aria-labelledby="modalFinalizaLabel" data-bs-keyboard="false" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalPiezaLabel">Finalizar Trabajo</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <p>Antes de finalizar por completo el trabajo verifica la informacion</p>
                            </div>

                            <div class="row">
                              <div class="col-sm-12 col-md-4 mb-3">
                                <label for="costoIniFinal" class="form-label">Costo Inicial</label>
                                <input type="number" id="costoIniFinal" value="<?php echo $costoIni; ?>" 
                                class="form-control" readonly>
                              </div>
                              <div class="col-sm-12 col-md-4 mb-3">
                                <label for="anticipoFinal" class="form-label">Anticipo</label>
                                <input type="number" id="anticipoFinal" value="<?php echo $anticipo; ?>" 
                                class="form-control" readonly>
                              </div>
                              <div class="col-sm-12 col-md-4 mb-3">
                                <label for="montoRestante" class="form-label">Restante</label>
                                <input type="number" id="montoRestante" class="form-control" 
                                value="<?php echo $restante; ?>" readonly>
                              </div>
                              <div class="col-sm-12 col-md-4 mb-3">
                                <label for="montoArticulos" class="form-label">Gasto Final</label>
                                <input type="number" id="montoArticulos" class="form-control" 
                                value="<?php echo $sumArti; ?>" readonly>
                              </div>

                              <div class="col-sm-12 col-md-4 offset-md-4 mb-3">
                                <label for="costoFinal" class="form-label">Costo Final</label>
                                <input type="number" id="costoFinal" class="form-control">
                              </div>
                            </div>

                            <div class="row text-center">
                              <p>Se prevé obtener una ganancia aproximada de 
                                <span class="fw-bold text-primary" id="gananciaEstimada">$0</span> 
                              </p>
                            </div>

                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btnTerminaTrabajo">Finalizar</button>
                          </div>
                        </div>
                        </div>
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
    <script src="assets/js/verInfoTrabajo.js"></script>
</body>
</html> 

