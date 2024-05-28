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

    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    // $idUsuario = json_decode($datosUsuario)->idUsuario;
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
          <div class="col-12 col-lg-12">
            <div class="app-card h-100 shadow-sm">
              <div class="app-card-header p-3">
                <div class="row justify-content0between align-items0center">
                  <h4 class="app-card-title">Movimientos de Mercancia</h4>
                </div>
              </div><!--app-header-->
              
              <div class="modal fade" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" id="modalTraspaso">
                <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5>Traspaso entre Sucursales</h5>
                    </div>
                    <div class="modal-body row">
                      <?php 
                        //consultamos las sucursales
                        $sucursales2 = verSucursales($usuario,$idEmprersa);
                        $sucursales2 = json_decode($sucursales2);
                        if($sucursales2->estatus == "ok"){

                          $nSuc = count($sucursales2->dato);
                          if($nSuc > 1){
                            //si cuenta con mas de una sucursal, habilitamos el formulario
                            $camposSelect = "";
                            for($c = 0; $c < count($sucursales2->dato); $c++){
                              $nameSuc2 = $sucursales2->dato[$c]->nombreSuc;
                              $idSuc2 = $sucursales2->dato[$c]->idSucursal;
                              if($rolUsuario == "Administrador"){
                                // echo "<option value = '$idSuc'>$nameSuc</option>";
                                $camposSelect .= "<option value = '$idSuc2'>$nameSuc2</option>";
                              }else{
                                if($idSucursal == $idSuc2){
                                  $camposSelect .= "<option value = '$idSuc2' selected>$nameSuc2</option>";
                                }else{
                                  $camposSelect .= "<option value = '$idSuc2' disabled>$nameSuc2</option>";    
                                }
                              }
                            }//fin del for sucursales
                            ?> 
                            <div class="col-sm-12 col-md-12 mb-3">
                              <label for="prodModal" class="form-label">Seleccione Producto...</label>
                              <select name="prodModal" id="prodModal" class="form-select">
                                <option value="">Seleccione...</option>
                                <?php 
                                  //consultamos los productos de la empresa
                                  $productos2 = getProductos($idEmprersa);
                                  $productos2 = json_decode($productos2);
                                  if($productos2->status == "ok"){
                                    for($x = 0; $x < count($productos2->data); $x++){
                                      $nameProd = $productos2->data[$x]->nombreArticulo;
                                      $idProd = $productos2->data[$x]->idArticulo;
                                      echo "<option value='$idProd'>$nameProd</option>";
                                    }//fin del for
                                  }else{
                                    //error al consultar los datos
                                    echo "<option value=''>Error de Base de datos</option>";
                                  }
                                ?>
                              </select>
                            </div>

                            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                              <label for="tipoCompTras" class="form-label">Tipo de Comprobante</label>
                              <select name="tipoCompTras" id="tipoCompTras" class="form-select">
                                <option value="" selected disabled>Seleccione</option>
                                <option value="Ticket">Ticket</option>
                                <option value="Nota de Venta">Nota de Venta</option>
                              </select>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                              <label for="numComproTras" class="form-label">Numero Comprobante</label>
                              <input type="text" id="numComproTras" name="numComproTras" class="form-control">
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-5 mb-3">
                              <label for="fechaTras" class="form-label">Fecha de Traspaso</label>
                              <input type="date" id="fechaTras" name="fechaTras" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                              <label for="sucursalOrigen" class="form-label">Sucursal Origen</label>
                              <select name="sucursalOrigen" id="sucursalOrigen" class="form-select">
                                <option value="" selected disabled>Seleccione</option>
                                <?php echo $camposSelect; ?>
                              </select>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                              <label for="sucursalDestino" class="form-label">Sucursal Destino</label>
                              <select name="sucursalDestino" id="sucursalDestino" class="form-select">
                                <option value="" selected disabled>Seleccione</option>
                                <?php echo $camposSelect; ?>
                              </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                              <label for="cantidadMovSuc" class="form-label">Cantidad a Traspasar</label>
                              <input type="number" id="cantidadMovSuc" name="cantidadMovSuc" class="form-control">
                            </div>
                            <?php
                          }else{
                            //solo tiene una sucursal
                            //por lo que le indicaremos que no puede hacer uso de este metodo
                            ?> 
                            <h5 class="text-center">Sucursales no disponibles</h5>
                            <?php
                          }

                          //mostramnos las sucursales
                          // for($c = 0; $c < count($sucursales->dato); $c++){
                          //   $nameSuc = $sucursales->dato[$c]->nombreSuc;
                          //   $idSuc = $sucursales->dato[$c]->idSucursal;
                          //   if($rolUsuario == "Administrador"){
                          //     echo "<option value = '$idSuc'>$nameSuc</option>";
                          //   }else{
                          //     if($idSucursal == $idSuc){
                          //       echo "<option value = '$idSuc' selected>$nameSuc</option>";
                          //     }else{
                          //       echo "<option value = '$idSuc' disabled>$nameSuc</option>";    
                          //     }
                          //   }

                            
                          // }//fin del for sucursales

                        }else{
                          //error de comunicacion
                          
                        }
                      ?>

                    </div>
                    <div class="modal-footer">
                      <a href="#!" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Cancelar</a>
                      <a href="#!" class="btn btn-primary" onclick="procesaTraspaso()">Procesar Traspaso</a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="app-card-body p-3 p-lg-4">
                <div class="row">
                  <form action="" name="dataMovArti" id="dataMovArti" class="row">

                    <div class="col-sm-12 col-md-3 mb-3">
                      <label for="fechaMov" class="form-label">Fecha movimiento</label>
                      <input type="date" id="fechaMov" name="fechaMov" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                      <label for="proveedorMov" class="form-label">Proveedor</label>
                      <select name="proveedorMov" id="proveedorMov" class="form-select">
                        <option value="">Seleccione...</option>
                        <?php 
                          //consultamos los proveedores de la empresa
                          $sqlProv = "SELECT * FROM PROVEEDORES WHERE provEmpresaID = '$idEmprersa' AND estatusProveedor = '1'";
                          try {
                            $queryProv = mysqli_query($conexion, $sqlProv);
                            while($fetchProv = mysqli_fetch_assoc($queryProv)){
                              $idProv = $fetchProv['idProveedor'];
                              $nombreProv = $fetchProv['nombreProveedor'];
                              
                              echo "<option value='$idProv'>$nombreProv</option>";
                            }//fin del while
                          } catch (\Throwable $th) {
                            //error de base de datos
                            echo "<option value='0'>Error de BD</option>";
                          }
                          
                        ?>
                        <option value="1">Default</option>
                      </select>
                    </div>
                    <div class="col-sm-12 col-md-2 mb-3">
                      <label for="numCompro" class="form-label">No. Comprobante</label>
                      <input type="text" id="numCompro" name="numCompro" class="form-control">
                    </div>
                    <div class="col-sm-12 col-md-3 mb-3">
                      <label for="tipoCompro" class="form-label">Tipo Comprobante</label>
                      <select name="tipoCompro" id="tipoCompro" class="form-select">
                        <option value="" selected disabled>Selecione...</option>
                        <option value="Ticket">Ticket</option>
                        <option value="Nota de Venta">Nota de Venta</option>
                        <option value="Factura">Factura</option>
                      </select>
                    </div>
                    

                    <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                      <label for="tipoMov" class="form-label">Tipo de Movimiento</label>
                      <select name="tipoMov" id="tipoMov" class="form-select" onchange="traspasoModal(this.value)">
                        <option value="" selected disabled>Seleccione...</option>
                        <option value="Entrada">Entrada</option>
                        <option value="Salida">Salida</option>
                        <option value="Traspaso">Traspaso de Sucursal</option>
                      </select>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                        <label for="sucursal" class="form-label">Sucursal Destino</label>
                        <select name="sucursal" id="sucursal" class="form-select">
                          <option value="" selected disabled>Seleccione</option>
                          <?php 
                            //si e usuario es encargado de tienda o cajero,
                            //unicamente mostraremos su sucursal de operacion,
                            //pero si se trata de un usuario administrador, mostraremos todas
                            //las sucursales
                            $sucursales = verSucursales($usuario,$idEmprersa);
                            $sucursales = json_decode($sucursales);
                            if($sucursales->estatus == "ok"){
                              //mostramnos las sucursales
                              for($c = 0; $c < count($sucursales->dato); $c++){
                                $nameSuc = $sucursales->dato[$c]->nombreSuc;
                                $idSuc = $sucursales->dato[$c]->idSucursal;
                                if($rolUsuario == "Administrador"){
                                  echo "<option value = '$idSuc'>$nameSuc</option>";
                                }else{
                                  if($idSucursal == $idSuc){
                                    echo "<option value = '$idSuc' selected>$nameSuc</option>";
                                  }else{
                                    echo "<option value = '$idSuc' disabled>$nameSuc</option>";    
                                  }
                                }

                                
                              }//fin del for sucursales

                            }else{
                              //error de comunicacion
                              echo "<option value=''>Error de BD</option>";
                            }
                          ?>
                        </select>
                      </div>

                    <div class="col-sm-12 col-md-5 col-lg-3 mb-3">
                      <label for="producto" class="form-label">Seleccione Producto...</label>
                      <select name="producto" id="producto" class="form-select" onchange="getDataProd(this.value,this.id)">
                        <option value="">Seleccione...</option>
                        <?php 
                          //consultamos los productos de la empresa
                          $productos = getProductos($idEmprersa);
                          $productos = json_decode($productos);
                          if($productos->status == "ok"){
                            for($x = 0; $x < count($productos->data); $x++){
                              $nameProd = $productos->data[$x]->nombreArticulo;
                              $idProd = $productos->data[$x]->idArticulo;
                              echo "<option value='$idProd'>$nameProd</option>";
                            }//fin del for
                          }else{
                            //error al consultar los datos
                            echo "<option value=''>Error de Base de datos</option>";
                          }
                        ?>
                      </select>
                    </div>

                    <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                      <label for="codProducto" class="form-label">Codigo</label>
                      <input type="text" id="codProducto" name="codProducto" class="form-control" onchange="getDataProd(this.value,this.id)">
                    </div>

                    

                    

                    <hr>

                    <div class="row"><!--row segunda seccion-->

                      

                      <div class="col-sm-12 col-md-2 col-lg-2 col-lg-2 mb-3">
                        <label for="actActual" class="form-label">Cantidad Actual</label>
                        <input type="text" id="actActual" class="form-control" disabled>
                      </div>
                      <div class="col-sm-12 col-md-2 col-lg-2 col-lg-2 mb-3">
                        <label for="preActual" class="form-label">Precio Venta</label>
                        <input type="text" id="preActual" name="preActual" class="form-control" disabled>
                      </div>
                      <div class="col-sm-12 col-md-2 col-lg-2 col-lg-2 mb-3">
                        <label for="preCompra" class="form-label">Precio Compra</label>
                        <input type="text" id="preCompra" class="form-control" disabled>
                      </div>

                      <div class="col-sm-12 col-md-2 col-lg-2 mb-3">
                        <label for="cantidadMov" class="form-label">Cantidad</label>
                        <input type="number" id="cantidadMov" name="cantidadMov" class="form-control" disabled onchange="calculaTotal()">
                      </div>

                      <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                        <label for="precioCompra" class="form-label">Precio U. Compra</label>
                        <input type="number" id="precioCompra" name="precioCompra" class="form-control" disabled onchange="calculaTotal()">
                      </div>

                      <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                        <label for="totalCompra" class="form-label">Total de Compra</label>
                        <input type="number" id="totalCompra" name="totalCompra" class="form-control" disabled>
                      </div>

                      
                      <div class="col-sm-12 col-md-4 mb-3 mt-4">
                          <input type="checkbox" class="form-check-input" id="cambiarPrecio" name="cambiarPrecio" autocomplete="off">
                          <label for="cambiarPrecio" class="btn form-check-label fs-5">
                            <strong>Cambiar precio de venta</strong>
                          </label>
                      </div>
                      
                      <div id="cambioPrecioCompra"></div>

                      <div class="row">
                        <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                          <a href="#!" class="btn btn-info" id="addMov">Agregar Movimiento</a>
                        </div>
                      </div>

                    </div><!--Fin row segundsa seccion-->

                    <div class="row"><!--Inicio Tercer seccion-->
                      <input type="hidden" name="numRowsTab" id="numRowsTab" value="0">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Tipo</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Sucursal</th>
                            <th>Total</th>
                            <th>Borrar</th>
                          </tr>
                        </thead>
                        <tbody id="resMovs">

                        </tbody>
                      </table>

                      <div class="col-sm-12 col-md-3 offset-md-6 mb-3">
                        <label for="numTotArti" class="form-label">Total de Articulos</label>
                        <input type="number" readonly id="numTotArti" value="0" class="form-control" name="numTotArti">
                      </div>
                      <div class="col-sm-12 col-md-3 mb-3">
                        <label for="totCompra" class="form-label">Total</label>
                        <input type="number" id="totCompra" value="0" class="form-control" name="totCompra">
                      </div>

                      <div class="row">
                        <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                          <a href="#!" class="btn btn-success" id="saveMov">Finalizar Movimientos</a>
                        </div>
                      </div>
                    </div><!--fin tercer seccion-->

                  </form>
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
    <script src="assets/js/movProductos.js"></script>

</body>
</html> 

