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
    include("includes/operacionesCaja.php");

    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    $idUsuario = json_decode($datosUsuario)->idUsuario;

    //antes de proceder, verificamos si existe el cierre y apertura del dia
    // $apertura = existeApertura($idUsuario,$idSucursal,$idEmprersa);
    // $datoApertura = json_decode($apertura);
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
          <div class="row">
            
            <div class="col-7 col-lg-7">
              <div class="app-card app-card-chart h-100 shadow-sm">
                <div class="app-card-header p-3">
                  <div class="row justify-content-between align-items-center">

                    <div class="col-auto">
                      <h4 class="app-card-title"><?php echo $nombrEmpresa." - Sucursal ".$nombreSucursal; ?></h4>
                    </div><!--//col-->

                    <div class="col-auto">
                      <div class="card-header-action">
                        <!-- <a href="altaProducto.php">Registrar Producto</a> -->
                      </div><!--//card-header-actions-->
                    </div><!--//col-->

                  </div><!--//row-->
                </div><!--//app-card-header-->

                

                <!-- <hr class="my-4"> -->

                <div class="row m-1" id="datosVenta">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th class='text-center'>No Ticket</th>
                        <th>Cliente</th>
                        <th>Tipo Pago</th>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        //buscamos los datos de venta
                        $fecha = date('Y-m-d');
                        $sqlMov = "SELECT * FROM MOVCAJAS WHERE fechaMovimiento = '$fecha' AND conceptoMov = '1' AND 
                        sucursalMovID = '$idSucursal' AND usuarioMov = '$idUsuario'";
                        try {
                          $queryMov = mysqli_query($conexion, $sqlMov);
                          if(mysqli_num_rows($queryMov) == 1){
                            //ahora veriricamos que no tenga hecho algun cierre
                            $sqlMov2 = "SELECT * FROM MOVCAJAS WHERE fechaMovimiento = '$fecha' AND conceptoMov = '4' AND 
                            sucursalMovID = '$idSucursal' AND usuarioMov = '$idUsuario'";
                            try {
                              $queryMov2 = mysqli_query($conexion, $sqlMov2);
                              if(mysqli_num_rows($queryMov2) == 0){
                                //si procede el cierre, por lo que pasamos a consultar sus ventas
                                $fetchMov = mysqli_fetch_assoc($queryMov);
                                $montoInicio = $fetchMov['montoMov'];
                                $sqlMov3 = "SELECT *,(SELECT DISTINCT(b.sucursalID) FROM DETALLEVENTA b 
                                WHERE b.ventaID = a.idVenta) AS sucursalVenta FROM VENTAS a WHERE 
                                a.fechaVenta = '$fecha' AND a.usuarioID = '$idUsuario' AND a.empresaID = '$idEmprersa'";
                                
                                try {
                                  $queryMov3 = mysqli_query($conexion, $sqlMov3);
                                  if(mysqli_num_rows($queryMov3) > 0){
                                    $totalVenta = 0;
                                    $totalEfectivo = 0;
                                    $totalDigital = 0;
                                    while($fetchMov3 = mysqli_fetch_assoc($queryMov3)){
                                      $montoVenta = $fetchMov3['totalVenta'];
                                      $tipopago = $fetchMov3['tipoPago'];
                                      $ticket = $fetchMov3['num_comprobante'];
                                      $idCliente = $fetchMov3['clienteID'];

                                      $dataCliente = verCliente($idCliente,$idEmprersa);
                                      $nombreClie = json_decode($dataCliente)->data->nombreCliente;
                                      
                                      if($tipopago == "Efectivo"){
                                        $totalEfectivo = $totalEfectivo + $montoVenta;
                                      }else{
                                        $totalDigital = $totalDigital + $montoVenta;
                                      }

                                      $totalVenta = $totalVenta + $montoVenta;

                                      echo "<tr>
                                        <td class='text-center'>$ticket</td>
                                        <td>$nombreClie</td>
                                        <td>$tipopago</td>
                                        <td>$$montoVenta</td>
                                      </tr>";
                                    }//fin del while
                                  }else{
                                    //sin ventas registradas en el dia
                                  }
                                } catch (\Throwable $th) {
                                  //throw $th;
                                }
                              }else{
                                //ya tiene realizado el cierre del dia en la sucursal
                                $controlCierre = "display:none;";
                                echo "<tr><td colspan = '4' class='text-center'><h4 class='text-danger'>Cierre ya procesado</h4></td></tr>";
                              }
                            } catch (\Throwable $th) {
                              //error al consultar si existe el cierre del dia
                            }
                          }else{
                            //no procede cierre

                          }
                        } catch (\Throwable $th) {
                          //error al consultar si existe la apertura del dia
                        }
                      ?>
                    </tbody>
                  </table>
                </div>

                
              </div><!--//app-card-->
            </div><!--//col 7-->

            <div class="col-5 col-lg-5">
              <div class="app-card  h-100 shadow-sm" style="">
                <div class="app-card-header p-3">
                  <div class="row justify-content-between align-items-center">

                    <div class="col-sm-12 text-center">
                      <h4 class="app-card-title"><?php echo $nombrEmpresa; ?></h4>
                    </div><!--//col-->

                  </div><!--//row-->
                </div><!--//app-card-header-->

                <div class="row" style="padding-left:20px; <?php echo $controlCierre; ?>">

                  <div class=" row col-sm-12 mb-2 hide">

                    <div class="col-sm-12 col-md-6 mb-3">
                      <label for="montoEfectivoIni" class="form-label">Efectivo Inicial</label>
                      <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" id="montoEfectivoIni" class="form-control" 
                        value="<?php echo $montoInicio; ?>" readonly>
                      </div>
                      
                    </div>

                    <div class="col-sm-12 col-md-6 mb-3">
                      <label for="ventaEfectivo" class="form-label">Venta en Efectivo</label>
                      <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" id="ventaEfectivo" class="form-control" readonly
                        value="<?php echo $totalEfectivo; ?>">
                      </div>
                      
                    </div>

                    <div class="col-sm-12 col-md-6 offset-md-3 mb-3">
                      <label for="ventaDigital" class="form-label">Venta Digital</label>
                      <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" id="ventaDigital" class="form-control" readonly
                        value="<?php echo $totalDigital; ?>">
                      </div>
                    </div>

                    <div class="col-sm-12 col-md-12 mb-3">
                      <label for="montoEfectivo" class="form-label">Efectivo total en Caja</label>
                      <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" id="montoEfectivo" class="form-control" onkeyup="calculaTotal(this.value)">
                        <div class="invalid-feedback" id="montoEfectivoMal">Indique un monto valido</div>
                      </div>
                    </div>

                    <div class="col-sm-12 col-md-12 mb-3">
                      <label for="montoRetiroEfe" class="form-label">Monto a retirar</label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-safe2-fill" viewBox="0 0 16 16">
                            <path d="M6.563 8H5.035a3.5 3.5 0 0 1 .662-1.596l1.08 1.08q-.142.24-.214.516m.921-1.223-1.08-1.08A3.5 3.5 0 0 1 8 5.035v1.528q-.277.072-.516.214M9 6.563V5.035a3.5 3.5 0 0 1 1.596.662l-1.08 1.08A2 2 0 0 0 9 6.563m1.223.921 1.08-1.08c.343.458.577 1.003.662 1.596h-1.528a2 2 0 0 0-.214-.516M10.437 9h1.528a3.5 3.5 0 0 1-.662 1.596l-1.08-1.08q.142-.24.214-.516m-.921 1.223 1.08 1.08A3.5 3.5 0 0 1 9 11.965v-1.528q.277-.072.516-.214M8 10.437v1.528a3.5 3.5 0 0 1-1.596-.662l1.08-1.08q.24.142.516.214m-1.223-.921-1.08 1.08A3.5 3.5 0 0 1 5.035 9h1.528q.072.277.214.516M7.5 8.5a1 1 0 1 1 2 0 1 1 0 0 1-2 0"/>
                            <path d="M2.5 1A1.5 1.5 0 0 0 1 2.5V3H.5a.5.5 0 0 0 0 1H1v4H.5a.5.5 0 0 0 0 1H1v4H.5a.5.5 0 0 0 0 1H1v.5A1.5 1.5 0 0 0 2.5 16h12a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 14.5 1zm6 3a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9"/>
                          </svg>
                        </span>
                        <input type="number" id="montoRetiroEfe" class="form-control" onkeyup="calculaMontoResta(this.value)">
                        <div class="invalid-feedback" id="montoRetiroEfeMal">Indique un monto correcto</div>
                      </div>
                    </div>

                    <div class="col-sm-12 col-md-12 mb-3">
                      <label for="obervacionCierre" class="form-label">Observacion</label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-dots" viewBox="0 0 16 16">
                          <path d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                          <path d="m2.165 15.803.02-.004c1.83-.363 2.948-.842 3.468-1.105A9 9 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6a10.4 10.4 0 0 1-.524 2.318l-.003.011a11 11 0 0 1-.244.637c-.079.186.074.394.273.362a22 22 0 0 0 .693-.125m.8-3.108a1 1 0 0 0-.287-.801C1.618 10.83 1 9.468 1 8c0-3.192 3.004-6 7-6s7 2.808 7 6-3.004 6-7 6a8 8 0 0 1-2.088-.272 1 1 0 0 0-.711.074c-.387.196-1.24.57-2.634.893a11 11 0 0 0 .398-2"/>
                        </svg>
                        </span>
                        <input type="text" id="obervacionCierre" class="form-control" placeholder="Ej: Cierre del dia cajero Pedro Calderas.">
                        <div class="invalid-feedback" id="obervCierreMal">Indique una observacion valida</div>
                      </div>
                    </div>

                  </div>

                </div><!--row-->
                  
                <?php 
                  //ultima seccion de operaciones
                  $totalCaja = $montoInicio + $totalEfectivo;
                ?>
                
                <div class="row m-0" style='<?php echo $controlCierre; ?>'>
                  <div class="col-sm-12">
                    <span class="fs-4 fw-bold" id="totalDiferencia">Diferencia: $<span id="montoDife">0.00</span></span> <br>
                    <span class="fs-4 fw-bold text-primary" id="saldoTotal">Total en Efectivo: $<?php echo number_format($totalCaja,2); ?></span> <br>
                    <input type="hidden" id="totalCajaSaldo" value="<?php echo $totalCaja; ?>">
                    <span class="fs-4 fw-bold ">Efectivo Final en Caja: $<span id="saldoDeja">0.00</span></span> <br>
                    <input type="hidden" id="ventaTotalDia" value="<?php echo $totalVenta; ?>">
                  </div>
                </div>

                

                <div class="row m-2" style="<?php echo $controlCierre; ?>">
                  <a href="#!" class="btn btn-warning" id="btnCerrarDia">Cerrar Dia</a>
                </div>
                

                <div class="row">
                  <div class="container">
                    <div class="col-md-12 bg-success align-middle text-center pt-3" style="height:70px;" data-bs-toggle="modal" data-bs-target="#cobroCaja">
                      <span class="text-white fs-5 fw-bold" id="totalVentaProds" >Total Venta $<?php echo number_format($totalVenta,2); ?></span>
                    </div>
                  </div>
                </div>

                

                

                

                
              </div><!--//app-card-->
            </div><!--//col 5-->
          </div><!--row-->
        </div><!--container-xl-->
      </div><!--app-content-->


      

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
    <script src="assets/js/cierreCajas.js"></script>

</body>
</html> 

