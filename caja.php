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
    $apertura = existeApertura($idUsuario,$idSucursal,$idEmprersa);
    $datoApertura = json_decode($apertura);
    // print_r($datoApertura);
    if($datoApertura->status == "ok"){
      if($datoApertura->mensaje == "noData"){
        //no existe la apertura del dia, por lo que mostraremos un modal
        //para que pueda realizar su apertura del dia
        $sqlCi = "SELECT * FROM MOVCAJAS WHERE empresaMovID = '$idEmprersa' AND sucursalMovID = '$idSucursal' 
        AND conceptoMov = '4'ORDER BY fechaMovimiento DESC LIMIT 1";
        $queryCi = mysqli_query($conexion, $sqlCi);
        $saldoCierre = 0;
        if(mysqli_num_rows($queryCi) == 1){
          $fetchCi = mysqli_fetch_assoc($queryCi);
          $saldoCierre = $fetchCi['montoMov'];
        }
        ?>
          <div class="modal fade" tabindex="-1" id="modalAperturaDia" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Apertura del dia</h5>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <form id="datosApertura" class="row">
                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="fechaMov" class="form-label">Fecha</label>
                        <input type="date" name="fechaMov" id="fechaMov" value="<?php echo date('Y-m-d'); ?>" 
                        readonly class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="usuarioMov" class="form-label">Usuario</label>
                        <input type="text" id="usuarioMov" name="usuarioMov" 
                        value="<?php echo $usuario; ?>" readonly class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="tipoMov" class="form-label">Concepto</label>
                        <select name="tipoMov" id="tipoMov" class="form-select">
                          <option value="" disabled>Seleccione</option>
                          <?php 
                            $sqlConcep = "SELECT * FROM CONCEPTOSMOV WHERE estatusConcepto = '1'";
                            $queryConcep = mysqli_query($conexion, $sqlConcep);
                            while($fetchConcep = mysqli_fetch_assoc($queryConcep)){
                              $nombreCon = $fetchConcep['nombreConcepto'];
                              $idConce = $fetchConcep['idConcepto'];
                              if($idConce == 1){
                                echo "<option value='$idConce' selected>$nombreCon</option>";
                              }else{
                                echo "<option value='$idConce' disabled>$nombreCon</option>";
                              }
                            }//fin del while

                          ?>
                        </select>
                      </div>

                      <div class="col-sm-12 col-md-12 mb-3">
                        <label for="observMov" class="form-label">Observacion</label>
                        <input type="text" maxlength="180" id="observMov" name="observMov" 
                        placeholder="Ej: Dotacion Inicial cajero Pedro Calderon" class="form-control">
                      </div>

                        <!-- <div class="col-sm-12 col-md-4 offset-md-2">
                          <label for="montoMovAnterior" class="form-label">Cierre del dia anterior</label>
                          <input type="number" pattern="^\d*\.?\d*$" title="Ingresa un numero valido" id="montoMovAnterior" name="montoMovAnterior" class="form-control" readonly>
                        </div> -->

                        <div class="col-sm-12 col-md-4 offset-md-2 mb-3">
                          <label for="montoCierreEnt" class="form-label">Monto Cierre Anterior</label>
                          <input type="number" id="montoCierreEnt" name="montoCierreEnt" value="<?php echo $saldoCierre; ?>" 
                          class="form-control" readonly>
                        </div>

                        <div class="col-sm-12 col-md-4">
                          <label for="montoMov" class="form-label">Monto Inicio</label>
                          <input type="number" pattern="^\d*\.?\d*$" title="Ingresa un numero valido" id="montoMov" name="montoMov" class="form-control">
                        </div>
                      

                      


                    </form>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" id="enviaApertura">Registrar</button>
                </div>
              </div>
            </div>
          </div>
        <?php 
      }else{
        //si existe por lo que no hacemos nada
      }
    }else{
      //ocurrio un error al consultar el dato
      //para este caso mostraremos un Swal, y lo redireccionaremos a las funciones de caja
      ?> 
      <script src="assets/js/swetAlert.js"></script>
        <script>
          Swal.fire({
            title: 'Ha ocurrido un error',
            text: 'Ocurrio un error al consultar la apertura del dia, reportar a soporte',
            icon: 'error'
          }).then(function(){
            //lo redireccionamos al apartado de cajas
            // window.location = '';
          })
        </script>
      <?php
      
    }

    //verificamos si ya existe el cierre del dia
    $fecha = date('Y-m-d');
    $existeCierre = existeCierre($fecha,$idUsuario,$idSucursal);
    $existeCierre = json_decode($existeCierre);
    if($existeCierre->status == "ok"){
      if($existeCierre->mensaje == "cierreExiste"){
        //ya existe el cierre
        $cierreClase = "display:none;";
        ?>
          <script src="assets/js/swetAlert.js"></script>
          <script>
            Swal.fire({
              title: 'Cierre de caja registrado',
              text: 'Si crees que es un error, consulta con el administrador',
              icon: 'warning',
            }).then(function(){
              window.location = "reportesCaja.php";
            })
          </script>
        <?php
      }else{
        //no existe el cierre
        $cierreClase = "";
      }
    }else{
      //error en la consulta del cierrew
    }

    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4" style="<?php echo $cierreClase; ?>">
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

                <div class="input-group col-sm-12 mb-3 mt-3 p-1">
                  <span class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                      <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                  </span>
                  <input type="text" name="buscarProducto" id="buscarProducto" class="form-control" placeholder="Buscar Producto">
                  
                </div>

                <hr class="my-4">

                <!-- Seccion para cobrar trabajos -->
                <h4 class="app-card-title">Servicios listo para cobro</h4>

                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">Folio</th>
                      <th scope="col">Cliente</th>
                      <th scope="col">Dispositivo</th>
                      <th scope="col">Monto</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                      //consultaremos los trabajos que estan listos para su cobro
                      // $sqlTrab = "SELECT a.*,b.nombreCliente FROM TRABAJOS a INNER JOIN CLIENTES b 
                      // ON a.clienteID = b.idClientes WHERE a.sucursalID = '$idSucursal' AND 
                      // a.empresaID = '$idEmpresaSesion' AND a.fechaTermino != '0000-00-00' AND a.estatusTrabajo = 'Finalizado'";
                      $sqlTrab = "SELECT a.*,b.nombreCliente FROM TRABAJOS a INNER JOIN CLIENTES b 
                      ON a.clienteID = b.idClientes WHERE a.sucursalID = '$idSucursal' AND a.empresaID = '$idSucursal' 
                      AND a.fechaTermino IS NOT NULL AND a.fechaCobro IS NULL AND a.estatusTrabajo = 'Finalizado'";
                      try {
                        $queryTrab = mysqli_query($conexion, $sqlTrab);
                        if(mysqli_num_rows($queryTrab) > 0){
                          while($fetchTrab = mysqli_fetch_assoc($queryTrab)){

                            $nombreCliente = $fetchTrab['nombreCliente'];
                            $folioTrabajo = $fetchTrab['numTrabajo'];
                            $folioTrabajo = str_pad($folioTrabajo, 3, '0', STR_PAD_LEFT);
                            $dispositivo = $fetchTrab['tipoDispositivo']." ".$fetchTrab['marca']." ".$fetchTrab['modelo'];
                            $monto = $fetchTrab['costoFinal'];
                            $anticipo = $fetchTrab['anticipo'];

                            $costoFinal = $monto - $anticipo;
                            $idTrabajo = $fetchTrab['idTrabajo'];

                            echo "<tr onclick='addTrabajo($idTrabajo)'>
                            <td>$folioTrabajo</td>
                            <td>$nombreCliente</td>
                            <td>$dispositivo</td>
                            <td>$$costoFinal</td>
                            </tr>";
                          }//fin del while
                        }else{
                          //sin trabajos disponibles de cobro
                          echo "<tr><td colspan='4' style='text-align:center'>Sin Trabajos Disponibles</td></tr>";
                        }
                      } catch (\Throwable $th) {
                        //error en la consulta de trabajos
                        echo "<tr><td colspan='4'>Error en la consulta a la BD</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>

                <hr class="my-4">

                <div class="row m-1" id="resultBusqueda" style="height: 500px; overflow-y:scroll;">

                  <?php 
                    //consultamos el listado de productos
                    $prods = getProductosEmpresa($idEmpresaSesion);
                    $prods = json_decode($prods);
                    // print_r($prods);
                    $maxProds = 0;
                    if(count($prods->data) >20){
                      $maxProds = 20;
                    }else{
                      $maxProds = count($prods->data);
                    }
                    if($prods->mensaje == "operationSuccess"){
                      for($x = 0; $x < $maxProds; $x++){
                        $nombreprod = $prods->data[$x]->nombreArticulo;
                        $precio = $prods->data[$x]->precioUnitario;
                        $precioMayo = $prods->data[$x]->precioMayoreo;
                        $arti = $prods->data[$x]->idArticulo;
                        $maxLongitud = 22; // Longitud máxima deseada

                        $existencia = getArtiSucursal($idSucursal,$arti);
                        $numArti = json_decode($existencia)->data;
                        // echo $numArti;
                        // print_r($existencia);

                        if($numArti > 0 || $numArti != null){
                          if (strlen($nombreprod) > $maxLongitud) {
                            $cadenaTruncada = substr($nombreprod, 0, $maxLongitud) . "...";
                          } else {
                              $cadenaTruncada = $nombreprod;
                          }

                          if($prods->data[$x]->imgArticulo != ""){
                            $imgProd = $prods->data[$x]->imgArticulo;
                          }else{
                            $imgProd = 'assets/images/no-image-available.jpeg';
                          }

                          if($numArti == 0){
                            $clr = "bg-danger-subtle";
                          }else{
                            $clr = "";
                          }
                          
                          echo "<div class='col-sm-12 col-md-6 col-lg-4'>
                            <div class='card mb-3 $clr' style='min-height:75px; !important' onclick='addCarrito($arti)'>

                              <div class='row g-0'>

                                <div class='col-md-12'>
                                  <div class='card-body pl-1 pt-0 pb-0'>
                                    <span class='card-title text-truncate mb-0' style='font-size:11px;'><strong>$cadenaTruncada</strong></span><br>
                                    <span class='card-text mb-0'>$$precio</span><br>
                                    <span class='card-text mt-0'><small style='font-size:10px;'>Mayoreo: $$precioMayo    Existencia: $numArti</small></span>
                                  </div>
                                </div>

                              </div>

                            </div>
                          </div>";
                        }//fin if numero artioculos

                        
                      }
                      
                    }elseif($prods->mensaje == "noData"){
                      //no se tienen productos capturados
                    }else{
                      //ocurrio un error en la consulta
                    }

                  ?>

                </div>

                
              </div><!--//app-card-->
            </div><!--//col 7-->

            <div class="col-5 col-lg-5">
              <div class="app-card  h-100 shadow-sm">
                <div class="app-card-header p-3">
                  <div class="row justify-content-between align-items-center">

                    <div class="col-sm-12 text-center">
                      <h4 class="app-card-title"><?php echo $nombrEmpresa; ?></h4>
                    </div><!--//col-->

                  </div><!--//row-->
                </div><!--//app-card-header-->

                <div class="row">

                  <div class="col-sm-12 mb-2">
                    <div class="form-floating">
                      <select name="clienteVenta" id="clienteVenta" class="form-select">
                        <option value="" disabled>Seleccione...</option>
                        <option value="1" selected>Publico en General</option>
                        <?php 
                          //consultamos los clientes de la empresa
                          $clientes = verClientes($idEmpresaSesion);
                          $cliente = json_decode($clientes);
                          if($cliente->status == "ok"){
                            for($m = 0; $m < count($cliente->data); $m++){
                              $name = $cliente->data[$m]->nombreCliente;
                              $idCli = $cliente->data[$m]->idClientes;
                              echo "<option value='$idCli'>$name</option>";
                            }//fin del for
                          }
                        ?>
                      </select>
                      <label for="clienteVenta">Cliente</label>
                    </div>
                  </div>

                </div><!--row-->
                  
                <?php 
                  //buscamos articulos agregados
                  // $sqlVen = "SELECT * FROM DETALLEVENTA a  INNER JOIN ARTICULOS b ON a.articuloID = b.idArticulo 
                  // WHERE a.usuarioVenta = '$usuario' AND a.ventaID IS NULL";
                  $sqlVen = "SELECT * FROM DETALLEVENTA a WHERE a.usuarioVenta = '$usuario' AND a.ventaID IS NULL";
                  try {
                    $queryVen = mysqli_query($conexion, $sqlVen);
                    $nArti = mysqli_num_rows($queryVen);
                    $contenido = "";
                    $total = 0;
                    while($fetchVen = mysqli_fetch_assoc($queryVen)){
                      //buscamos el articulo y/o servicio
                      if($fetchVen['articuloID'] != NULL || $fetchVen['articuloID'] > 0){
                        //es articulo
                        $idArti = $fetchVen['articuloID'];
                        $sqlExt = "SELECT * FROM ARTICULOS WHERE idArticulo = '$idArti'";
                        $queryExt = mysqli_query($conexion, $sqlExt);
                        $fetchExt = mysqli_fetch_assoc($queryExt);
                        $nombreProdVenta = $fetchExt['nombreArticulo'];
                        // 
                        $cantidadVenta = $fetchVen['cantidadVenta'];
                        $subTotal = $cantidadVenta * $fetchVen['precioUnitario'];
                        $total = $total + $subTotal;
                        $idProdVenta = $fetchVen['idDetalleVenta'];
                        if (strlen($nombreProdVenta) > 20) {
                          $cadenaTruncada = substr($nombreProdVenta, 0, 20) . "...";
                        } else {
                            $cadenaTruncada = $nombreProdVenta;
                        }
                        $contenido .= "
                        <tr class='p-1' style='height: 58px;'>
                          <td style='font-size:11px;height: 58px !important;'>$cadenaTruncada</td>
  
                          <td class='d-flex ' style='height: 58px;'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' onclick='delOneProd($idProdVenta)' class='bi bi-cart-dash-fill m-2' viewBox='0 0 16 16'>
                              <path d='M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6.5 7h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1 0-1'/>
                            </svg>
                            
                            <input type='text' value='$cantidadVenta' pattern='[0-9]+' id='cantVent$idProdVenta' class='form-control' style='width:60px;' onchange='updateCantProd(this.id)'>
  
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' onclick='addMoreProd($idProdVenta)' class='bi bi-cart-plus-fill m-2' viewBox='0 0 16 16'>
                              <path d='M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M9 5.5V7h1.5a.5.5 0 0 1 0 1H9v1.5a.5.5 0 0 1-1 0V8H6.5a.5.5 0 0 1 0-1H8V5.5a.5.5 0 0 1 1 0'/>
                            </svg>
                          </td>
  
                          <td style='height: 58px;' id='subTotVenta$idProdVenta'>$$subTotal</td>
  
                          <td class='text-center' style='height: 58px;'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' onclick='delProd($idProdVenta)' class='bi bi-trash-fill text-danger' viewBox='0 0 16 16'>
                              <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
                            </svg>
                          </td>
                        </tr>";
                      }else{
                        //es servicio/trabajo
                        // $idTrabajo = $fetchVen['ventaID'];
                        // $sqlExt2 = "SELECT * FROM TRABAJOS WHERE idTrabajo = '$idTrabajo'";
                        // $queryExt2 = mysqli_query($conexion, $sqlExt2);
                        // $fetchExt2 = mysqli_fetch_assoc($queryExt2);
                        $cadenaTruncada = "Cobro de Servicio";
                        $cantidadVenta = "1";
                        $subTotal = $fetchVen['subtotalVenta'];
                        $total = $total + $subTotal;
                        $idProdVenta = $fetchVen['idDetalleVenta'];
                        // 
                        $contenido .= "
                        <tr class='p-1' style='height: 58px;'>
                          <td style='font-size:11px;height: 58px !important;'>$cadenaTruncada</td>
  
                          <td class='d-flex ' style='height: 58px;'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-cart-dash-fill m-2' viewBox='0 0 16 16'>
                              <path d='M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6.5 7h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1 0-1'/>
                            </svg>
                            
                            <input type='text' value='$cantidadVenta' pattern='[0-9]+' id='cantVent$idProdVenta' class='form-control' style='width:60px;' readonly>
  
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-cart-plus-fill m-2' viewBox='0 0 16 16'>
                              <path d='M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M9 5.5V7h1.5a.5.5 0 0 1 0 1H9v1.5a.5.5 0 0 1-1 0V8H6.5a.5.5 0 0 1 0-1H8V5.5a.5.5 0 0 1 1 0'/>
                            </svg>
                          </td>
  
                          <td style='height: 58px;' id='subTotVenta$idProdVenta'>$$subTotal</td>
  
                          <td class='text-center' style='height: 58px;'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' onclick='delProd($idProdVenta)' class='bi bi-trash-fill text-danger' viewBox='0 0 16 16'>
                              <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
                            </svg>
                          </td>
                        </tr>";

                      }

                      // $nombreProdVenta = $fetchVen['nombreArticulo'];
                      // $cantidadVenta = $fetchVen['cantidadVenta'];
                      // $subTotal = $cantidadVenta * $fetchVen['precioUnitario'];
                      // $total = $total + $subTotal;
                      // $idProdVenta = $fetchVen['idDetalleVenta'];
                      // if (strlen($nombreProdVenta) > 20) {
                      //   $cadenaTruncada = substr($nombreProdVenta, 0, 20) . "...";
                      // } else {
                      //     $cadenaTruncada = $nombreProdVenta;
                      // }
                      // $contenido .= "
                      // <tr class='p-1' style='height: 58px;'>
                      //   <td style='font-size:11px;height: 58px !important;'>$cadenaTruncada</td>

                      //   <td class='d-flex ' style='height: 58px;'>
                      //     <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' onclick='delOneProd($idProdVenta)' class='bi bi-cart-dash-fill m-2' viewBox='0 0 16 16'>
                      //       <path d='M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6.5 7h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1 0-1'/>
                      //     </svg>
                          
                      //     <input type='text' value='$cantidadVenta' pattern='[0-9]+' id='cantVent$idProdVenta' class='form-control' style='width:60px;' onchange='updateCantProd(this.id)'>

                      //     <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' onclick='addMoreProd($idProdVenta)' class='bi bi-cart-plus-fill m-2' viewBox='0 0 16 16'>
                      //       <path d='M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M9 5.5V7h1.5a.5.5 0 0 1 0 1H9v1.5a.5.5 0 0 1-1 0V8H6.5a.5.5 0 0 1 0-1H8V5.5a.5.5 0 0 1 1 0'/>
                      //     </svg>
                      //   </td>

                      //   <td style='height: 58px;' id='subTotVenta$idProdVenta'>$subTotal</td>

                      //   <td class='text-center' style='height: 58px;'>
                      //     <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' onclick='delProd($idProdVenta)' class='bi bi-trash-fill text-danger' viewBox='0 0 16 16'>
                      //       <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
                      //     </svg>
                      //   </td>
                      // </tr>";
                    }//fin del while
                  } catch (Throwable $th) {
                    //ocurrio un error en la base de datos
                    echo "Error ".$th;
                  }
                ?>
                <div class="row m-0">
                  <div class="col-sm-6">
                    <p>
                      <strong style="font-size:23px;">Carrito</strong>  
                      <span style="font-size:12px;" id="numArtiVenta">(<?php echo $nArti; ?> Articulos)</span>
                    </p>
                  </div>

                  <div class="col-sm-6 text-end align-middle mt-2">
                    <span class="badge text-bg-danger" onclick="sendToTras()">Vaciar Carrito</span>
                  </div>
                  
                </div>
                
                <div style="height:auto; max-height: 350px; min-height:350px; overflow-y:scroll;">
                  <div class="row m-0 p-1">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scooe="col">Nombre</th>
                          <th scooe="col" class="text-center">Cantidad</th>
                          <th scooe="col">Total</th>
                          <th scooe="col" class="text-center">Borrar</th>
                        </tr>
                      </thead>
                      <tbody id="cantenidoProds">
                        <?php echo $contenido; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                
                <div class="row">
                  <div class="d-flex align-middle">
                    <div class="p-2 flex-grow-1"></div>
                      <div class="p-2">
                        Descuento 
                        <span class="badge text-bg-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-percent text-light" viewBox="0 0 16 16">
                          <path d="M13.442 2.558a.625.625 0 0 1 0 .884l-10 10a.625.625 0 1 1-.884-.884l10-10a.625.625 0 0 1 .884 0M4.5 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5m7 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                        </svg>
                        </span>
                      </div>
                    <div class="p-2">
                      <input type="text" class="form-control" style="width:95px;" id="descuentoVenta" onchange="calculaDescuento()">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="d-flex align-middle">
                    <div class="p-2 flex-grow-1"></div>
                    <div class="p-2">
                      Iva
                      <span class="badge text-bg-info">
                        16%
                      </span>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control" style="width:80px;" id="ivaVenta">  
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="container">
                    
                    <div class="col-md-12 bg-success align-middle text-center pt-3" style="height:70px;" data-bs-toggle="modal" data-bs-target="#cobroCaja">
                      <span class="text-white fs-5 fw-bold" id="totalVentaProds" >Pagar $<?php echo number_format($total,2); ?></span>
                    </div>
                    
                  </div>
                </div>

                

                

                

                
              </div><!--//app-card-->
            </div><!--//col 5-->

          </div><!--row-->

          <input type="hidden" id="totalCobroVenta" value="<?php echo $total; ?>">
          <!-- Modal -->
          <div class="modal fade" id="cobroCaja" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">Cobro</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <h5 class="text-center">Total a pagar</h5>
                    <h3 class="text-center" id="totalVentanaCobro"><strong>$<?php echo number_format($total,2); ?></strong></h3>
                    <span class="text-center">Indique el medoto de pago y la cantidad recibida</span> <br>
                  </div>

                  <div class="row text-center">
                    <div class="col-md-3">
                      <img src="../assets/images/dinero.png" alt="Pago en Efectivo" width="90" id="imgpagoEfectivo"><br>
                      <br>
                      Efectivo
                      <input type="text" class="form-control p-1 text-center" placeholder="$" id="pagoEfectivo" onfocus="metodoPago(this.id)" onkeyup="calculaCambio()">
                    </div>
                    <div class="col-md-3">
                      <img src="../assets/images/tarjeta-de-credito.png" alt="Pago Con Tarjeta" width="90" id="imgpagoTarjeta"><br>
                      <br>
                      Tarjeta
                      <input type="text" class="form-control text-center" placeholder="$" id="pagoTarjeta" onfocus="metodoPago(this.id)">
                    </div>
                    <div class="col-md-3">
                      <img src="../assets/images/pago-movil.png" alt="pago En Transferencia" width="90" id="imgpagoTransferencia"><br>
                      <br>
                      Transferencia
                      <input type="text" class="form-control text-center" placeholder="$" id="pagoTransferencia" onfocus="metodoPago(this.id)">
                    </div>
                    <div class="col-md-3">
                      <img src="../assets/images/credito.png" alt="Pago Con Credito" width="90" id="imgpagoCredito"><br>
                      <br>
                      Credito
                      <input type="text" class="form-control text-center" placeholder="$" id="pagoCredito" onfocus="metodoPago(this.id)" disabled>
                    </div>
                  </div>

                  <div class="row mt-3">
                    <h5 class="text-center text-success fw-bold mt-3" id="cambioLabel">Cambio $0.00</h5>
                  </div>
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                  <button type="button" class="btn btn-primary" id="btnCobroVenta">Cobrar</button>
                </div>
              </div>
            </div>
          </div>


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
    <script src="assets/js/caja.js"></script>

</body>
</html> 

