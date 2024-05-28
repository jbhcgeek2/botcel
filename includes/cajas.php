<?php 
session_start();
// error_reporting(1);
if(!empty($_SESSION['usuarioPOS'])){
  include("usuarios.php");
  include("conexion.php");
  include("articulos.php");
  include("ventas.php");
  include("operacionesCaja.php");

  //veriricamos la peticion
  if(!empty($_POST['busqueda'])){
    //seccion para realizar la busqueda de un producto
    $valor = $_POST['busqueda'];
    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    // echo "Empresa es: ".$idEmprersa;
  
    // echo $datosUsuario;V150101199109


    $sqlExt = "SELECT * FROM ARTICULOS a INNER JOIN ARTICULOSUCURSAL b 
    ON a.idArticulo = b.articuloID WHERE (a.nombreArticulo LIKE '%$valor%' 
    OR a.codigoProducto = '$valor') AND (a.empresaID = '$idEmprersa' AND b.sucursalID = '$idSucursal')";
    try {
      $queryExt = mysqli_query($conexion, $sqlExt);
      $datos = [];
      $x = 0;
      //si el resultado es exacatemento 1 automaticamente agregaremos el articulo
      //siempre y cuando lo escrito sea lo mismo que el codigo del producto
      if(mysqli_num_rows($queryExt) == 1){
        //aqui se encuentra un resultado
        $fetchExt1 = mysqli_fetch_assoc($queryExt);
        if($fetchExt1['codigoProducto'] == $valor){
          //el producto y el escaneo es el mismo, lo agregamos a la venta
          //obteneemos el id del producto para agregarlo al carrito
          $articulo = $fetchExt1['idArticulo'];
          $existen = getArtiSucursal($idSucursal,$articulo);
          $datExit = json_decode($existen);
          if($datExit->status == "ok"){
            if($datExit->data > 0){
              //registramos el movimiento de la venta y lo ingresamos directamente 
              //a la tabla detalleventa
              $infoArti = getInfoproducto($idEmprersa,$articulo);
              $infoArti = json_decode($infoArti);
              $precio = $infoArti->data->precioUnitario;
              //antes de insertar el producto, verificamos si ya existe
              $sqlExt = "SELECT * FROM DETALLEVENTA WHERE articuloID = '$articulo' AND usuarioVenta = '$usuario' AND 
              ventaID IS NULL";
              $queryExt = mysqli_query($conexion, $sqlExt);
              if(mysqli_num_rows($queryExt) > 0){
                //ya esta agregado el producto, por lo que agregamos uno mas
                $fetchExt = mysqli_fetch_assoc($queryExt);
                $cantidad = $fetchExt['cantidadVenta'];
                $monto = $fetchExt['precioUnitario'];

                $nuevaCantidad = $cantidad+1;
                $subTotal = $nuevaCantidad * $monto;
                $sql = "UPDATE DETALLEVENTA SET cantidadVenta = '$nuevaCantidad', subtotalVenta = '$subTotal' 
                WHERE articuloID = '$articulo' AND usuarioVenta = '$usuario' AND ventaID IS NULL";
                
              }else{
                //no existe el articulo
                $sql = "INSERT INTO DETALLEVENTA (cantidadVenta,precioUnitario,subtotalVenta,descuento,usuarioVenta,
                sucursalID,articuloID) VALUES ('1','$precio','$precio','0','$usuario','$idSucursal','$articulo')";
                
              }
              // $sql = "INSERT INTO DETALLEVENTA (cantidadVenta,precioUnitario,subtotalVenta,descuento,usuarioVenta,
              // sucursalID,articuloID) VALUES ('1','$precio','$precio','0','$usuario','$idSucursal','$articulo')";
              try {
                $query = mysqli_query($conexion, $sql);
                //si se inserto respondemos exitoso, consultamos los productos para mostrarlos
                // $sql2 = "SELECT * FROM DETALLEVENTA a  INNER JOIN ARTICULOS b ON a.articuloID = b.idArticulo 
                // WHERE a.usuarioVenta = '$usuario' AND a.ventaID IS NULL";
                $sql2 = "SELECT * FROM DETALLEVENTA a WHERE a.usuarioVenta = '$usuario' AND a.ventaID IS NULL";
                try {
                  $queryVen = mysqli_query($conexion, $sql2);
                  $nArti = mysqli_num_rows($queryVen);
                  $contenido = "";
                  $total = 0;
                  $totalArticulos = 0;
                  while($fetchVen = mysqli_fetch_assoc($queryVen)){
                    //verificamos si es un articulo o un trabajo
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
                      //es servicio
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
                    // $totalArticulos = $totalArticulos + $cantidadVenta;
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
                  // $datos = ["totalVenta"=>$total,"contenido"=>$contenido];
                  // $res = ["status"=>"ok","data"=>$datos];

                  // echo json_encode($res);
                  // $respuesta = "operationSuccess";
                  // $datos[0] = "operationSuccess";
                  $res = ["status"=>"ok","data"=>"operationSuccess"];
                  echo json_encode($res);
                } catch (Throwable $th) {
                  //error al consultar
                }
                // $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
                // echo json_encode($res);
              } catch (Throwable $th) {
                //throw $th;
                // $res = ["status"=>"error","mensaje"=>"Ocurrio un error al registrar la venta."];
                // echo json_encode($res);
                // $respuesta = "DataError+-_-+Ocurrio un error al registrar la venta";
                // echo $respuesta;
                $res = ["status"=>"error","mensaje"=>"Ocurrio un error al registrar la venta."];
                echo json_encode($res);
              }

            }else{
              // $respuesta = "DataError+-_-+Articulo Sin Inventario en Sucursal";
              // echo $respuesta;
              $res = ["status"=>"error","mensaje"=>"Articulo sin inventario en sucursal."];
                echo json_encode($res);
            }
          }else{
            //error al cosnultar los datos del articulo
            // $respuesta = "DataError+-_-+Ocurrio un error al consultar la informacion";
            // echo $respuesta;
            $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar la informacion."];
            echo json_encode($res);
          }
        }else{
          //se trata de una coincidencia
          $datos[0] = $fetchExt1;
          $res = ["status"=>"ok","data"=>$datos];
          echo json_encode($res);
        }
      }else{
        //es mas de uno o ninguno, hacemos el while
        while($fetchExt = mysqli_fetch_assoc($queryExt)){
          $datos[$x] = $fetchExt;
          $x++;
        }//fin del while
        $res = ["status"=>"ok","data"=>$datos];
        echo json_encode($res);
      }
      

      // $res = ["status"=>"ok","data"=>$datos];
      // echo json_encode($res);
    } catch (\Throwable $th) {
      //error en la consulta de datos
      $res = ["status"=>"error","mensaje"=>"Error al consultar la informacion. ".$th];
      echo json_encode($res);
    }

    unset($data,$res);

  }elseif(!empty($_POST['addArti'])){
    $articulo = $_POST['addArti'];
    //antes de ingresar el articulo consultamos la sucursale de operacion
    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;

    //vverificamos que el articulo exista en la sucursal de operacion
    $existen = getArtiSucursal($idSucursal,$articulo);
    $datExit = json_decode($existen);
    if($datExit->status == "ok"){
      if($datExit->data > 0){
        //registramos el movimiento de la venta y lo ingresamos directamente 
        //a la tabla detalleventa
        $infoArti = getInfoproducto($idEmprersa,$articulo);
        $infoArti = json_decode($infoArti);
        $precio = $infoArti->data->precioUnitario;
        //antes de insertar el producto, verificamos si ya existe
        $sqlExt = "SELECT * FROM DETALLEVENTA WHERE articuloID = '$articulo' AND usuarioVenta = '$usuario' AND 
        ventaID IS NULL";
        $queryExt = mysqli_query($conexion, $sqlExt);
        if(mysqli_num_rows($queryExt) > 0){
          //ya esta agregado el producto, por lo que agregamos uno mas
          $fetchExt = mysqli_fetch_assoc($queryExt);
          $cantidad = $fetchExt['cantidadVenta'];
          $monto = $fetchExt['precioUnitario'];

          $nuevaCantidad = $cantidad+1;
          $subTotal = $nuevaCantidad * $monto;
          $sql = "UPDATE DETALLEVENTA SET cantidadVenta = '$nuevaCantidad', subtotalVenta = '$subTotal' 
          WHERE articuloID = '$articulo' AND usuarioVenta = '$usuario' AND ventaID IS NULL";
          
        }else{
          //no existe el articulo
          $sql = "INSERT INTO DETALLEVENTA (cantidadVenta,precioUnitario,subtotalVenta,descuento,usuarioVenta,
          sucursalID,articuloID) VALUES ('1','$precio','$precio','0','$usuario','$idSucursal','$articulo')";
          
        }
        // $sql = "INSERT INTO DETALLEVENTA (cantidadVenta,precioUnitario,subtotalVenta,descuento,usuarioVenta,
        // sucursalID,articuloID) VALUES ('1','$precio','$precio','0','$usuario','$idSucursal','$articulo')";
        try {
          $query = mysqli_query($conexion, $sql);
          //si se inserto respondemos exitoso, consultamos los productos para mostrarlos
          $sql2 = "SELECT * FROM DETALLEVENTA a WHERE a.usuarioVenta = '$usuario' AND a.ventaID IS NULL";
          try {
            $queryVen = mysqli_query($conexion, $sql2);
            $nArti = mysqli_num_rows($queryVen);
            $contenido = "";
            $total = 0;
            $totalArticulos = 0;
            while($fetchVen = mysqli_fetch_assoc($queryVen)){
              //verificamos si es un articulo o servicio
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
                $totalArticulos = $totalArticulos + $cantidadVenta;
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
                //es servicio
                $cadenaTruncada = "Cobro de Servicio";
                $cantidadVenta = "1";
                $subTotal = $fetchVen['subtotalVenta'];
                $total = $total + $subTotal;
                $idProdVenta = $fetchVen['idDetalleVenta'];
                $totalArticulos = $totalArticulos + $cantidadVenta;
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

              // 


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
              // $totalArticulos = $totalArticulos + $cantidadVenta;
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
            // $datos = ["totalVenta"=>$total,"contenido"=>$contenido];
            // $res = ["status"=>"ok","data"=>$datos];

            // echo json_encode($res);
            $respuesta = "operationSuccess+-_-+".$contenido."+-_-+".$total."+-_-+".$totalArticulos;
            echo $respuesta;
          } catch (Throwable $th) {
            $respuesta = "DataError+-_-+Ocurrio un error al registrar la venta";
            echo $respuesta;
          }
          // $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
          // echo json_encode($res);
        } catch (Throwable $th) {
          //throw $th;
          // $res = ["status"=>"error","mensaje"=>"Ocurrio un error al registrar la venta."];
          // echo json_encode($res);
          $respuesta = "DataError+-_-+Ocurrio un error al registrar la venta";
          echo $respuesta;
        }

      }else{
        $respuesta = "DataError+-_-+Articulo Sin Inventario en Sucursal";
        echo $respuesta;
      }
    }else{
      //error al cosnultar los datos del articulo
      $respuesta = "DataError+-_-+Ocurrio un error al consultar la informacion";
      echo $respuesta;
    }
    


    
  }elseif(!empty($_POST['addOneProd'])){
    $idProdVenta = $_POST['addOneProd'];
    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    
    $sql = "SELECT * FROM DETALLEVENTA WHERE idDetalleVenta = '$idProdVenta'";
    try {
      $query = mysqli_query($conexion, $sql);
      $fetch = mysqli_fetch_assoc($query);
      $cantActual = $fetch['cantidadVenta'];
      $precioUni = $fetch['precioUnitario'];
      
      if(!empty($_POST['cambioCantidad'])){
        $cantNueva = $_POST['cambioCantidad'];
      }elseif(!empty($_POST['delOneProd'])){
        $cantNueva = $cantActual-1;
      }else{
        //no se detecto una cantidad fija, asi que siumamos 1
        $cantNueva = $cantActual+1;
      }
      
      $subtotal = $cantNueva*$precioUni;

      $sql2 = "UPDATE DETALLEVENTA SET cantidadVenta = '$cantNueva', subtotalVenta = '$subtotal' 
      WHERE idDetalleVenta = '$idProdVenta'";
      try {
        $query2 = mysqli_query($conexion, $sql2);
        //se actualizo la venta, regresaremos los datos necesario
        //subtotal del produto  y el total de la venta
        $totVenta = getTotalVenta($usuario,$idSucursal);
        $totVenta = json_decode($totVenta);
        if($totVenta->status == 'ok'){
          $totalVenta = $totVenta->data;
          $totalArti = getTotalArti($usuario,$idSucursal);
          $totalArticulos = json_decode($totalArti)->data;

          $datos = ["subtotal"=>$subtotal,"totalVenta"=>$totalVenta,"cantidadVenta"=>$cantNueva,"totalArti"=>$totalArticulos];
          $res = ["status"=>"ok","data"=>$datos];
  
          echo json_encode($res);
        }else{
          //error al consultar el total de venta
          $res = ["status"=>"error","mensaje"=>$totVenta->mensaje];
          echo json_encode($res);
        }
        

      } catch (Throwable $th) {
        $res = ["status"=>"error","mensaje"=>"Ocurrio un error al actualizar la cantidad: ".$th];
        echo json_encode($res);
      }
    } catch (Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar informacion del pedido: ".$th];
      echo json_encode($res);
    }
  }elseif(!empty($_POST['delAllProd'])){
    //seccion para eliminar el producto del carrito
    $idProdVenta = $_POST['delAllProd'];
    $usuario = $_SESSION['usuarioPOS'];

    $sql = "DELETE FROM DETALLEVENTA WHERE idDetalleVenta = '$idProdVenta'";
    try {
      $query = mysqli_query($conexion, $sql);
      //er elimina por completo, y consultamos los productos que se tienen de nuevo
      //consultamos de nuevo los productos
      $sql2 = "SELECT * FROM DETALLEVENTA a  INNER JOIN ARTICULOS b ON a.articuloID = b.idArticulo 
      WHERE a.usuarioVenta = '$usuario' AND a.ventaID IS NULL";
      try {
        $queryVen = mysqli_query($conexion, $sql2);
        $nArti = mysqli_num_rows($queryVen);
        $contenido = "";
        $total = 0;
        $totalArti = 0;
        while($fetchVen = mysqli_fetch_assoc($queryVen)){
          $nombreProdVenta = $fetchVen['nombreArticulo'];
          $cantidadVenta = $fetchVen['cantidadVenta'];
          $subTotal = $cantidadVenta * $fetchVen['precioUnitario'];
          $total = $total + $subTotal;
          $idProdVenta = $fetchVen['idDetalleVenta'];
          if (strlen($nombreProdVenta) > 20) {
            $cadenaTruncada = substr($nombreProdVenta, 0, 20) . "...";
          } else {
              $cadenaTruncada = $nombreProdVenta;
          }
          $totalArti = $totalArti + $cantidadVenta;
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

            <td style='height: 58px;' id='subTotVenta$idProdVenta'>$subTotal</td>

            <td class='text-center' style='height: 58px;'>
              <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' onclick='delProd($idProdVenta)' class='bi bi-trash-fill text-danger' viewBox='0 0 16 16'>
                <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>
              </svg>
            </td>
          </tr>";
        }//fin del while
        // $datos = ["totalVenta"=>$total,"contenido"=>$contenido];
        // $res = ["status"=>"ok","data"=>$datos];

        // echo json_encode($res);
        $respuesta = "operationSuccess+-_-+".$contenido."+-_-+".$total."+-_-+".$totalArti;
        echo $respuesta;
      } catch (\Throwable $th) {
        //throw $th;
      }
    } catch (\Throwable $th) {
      //throw $th;
    }
  }elseif(!empty($_POST['sendToTras'])){
    //eliminaremos todos los produyctos del usuario
    $usuario = $_SESSION['usuarioPOS'];
    $sql = "DELETE FROM DETALLEVENTA WHERE usuarioVenta = '$usuario' AND ventaID IS NULL";
    try {
      $query = mysqli_query($conexion, $sql);
      //se elimino corrcatmente los productos
      $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
      echo json_encode($res);
    } catch (\Throwable $th) {
      //ocurrio un error al eliminar los productos
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al vaciar el carrito"];
      echo json_encode($res);
    }
  }elseif(!empty($_POST['porceDesc'])){
    //seccion para hacer el calculo del porcentaje
    $porcentaje = $_POST['porceDesc'];
    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    $idUsuario = json_decode($datosUsuario)->idUsuario;

    $total = getTotalVenta($usuario,$idSucursal);
    $totalVenta = json_decode($total);

    if($totalVenta->status == "ok"){
      // $porcentaje = 10; // Porcentaje de descuento (ejemplo: 10%)
      $cantidad = $totalVenta->data; // Cantidad original

      $descuento = ($porcentaje / 100) * $cantidad;
      $total = $cantidad - $descuento;
      $total = number_format($total,2);
      $res = ["status"=>"ok","data"=>$total];
      echo json_encode($res);
    }else{
      $res = ["status"=> "ok","mensaje"=>"Ha ocurrido un error: ".$totalVenta->mensaje];
    }
    
    
  }elseif(!empty($_POST['totalPago'])){
    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    $idUsuario = json_decode($datosUsuario)->idUsuario;

    //seccion para realizar el cobro de la venta
    $cliente = $_POST['clienteVenta'];
    $totalCobro = $_POST['totalPago'];
    $descuento = $_POST['descuentoPago'];
    $montoPagoTotal = "";

    //solo se puede definir un tipo de pago
    $tiposPago = ["montoPagoEfe","montoPagoTarjeta","montoPagoTransferencia","montoPagoCredito"];
    $tipoPagoName = ["Efectivo","Tarjeta","Transferencia","Credito"];
    $tipoPago = "";
    $pasa = 1;
    for($x = 0; $x < count($tiposPago); $x++){
      $campopago = $tiposPago[$x];
      if($_POST[$campopago] >= $totalCobro){
        $tipoPago = $tipoPagoName[$x];
        $montoPagoTotal = $_POST[$campopago];
        $pasa = 0;
      }
    }//fin del for

    //antes de continuar, verificamos si las cantidades indicadas existen en la empresa
    $sqlAux = "SELECT * FROM DETALLEVENTA a INNER JOIN ARTICULOSUCURSAL b ON 
    a.articuloID = b.articuloID WHERE a.ventaID IS NULL AND a.usuarioVenta = '$usuario'";
    $queryAux = mysqli_query($conexion, $sqlAux);
    $cantidadSuperada = 0;
    while($fetchAux = mysqli_fetch_assoc($queryAux)){
      //buscaremos en los productos, si soporta la cantidad indicada
      // $articulo = $fetchAux['articulo'];
      $cantidadVenta = $fetchAux['cantidadVenta'];
      $cantidadExiste = $fetchAux['existenciaSucursal'];
      if($cantidadVenta > $cantidadExiste){
        //la cantidad supera y no se puede vender
        $pasa = 1;
        $cantidadSuperada = 1;
      }else{
        //la cantidad deseada es igual o menor a la deseada
        //de momento no hacemos nada, pero en la siguiente operacion 
        //actualizaremos la cantidad
        
      }
    }//fin del while

    if($pasa == 0){
      //consultamos el consecutivo del ticket el cual estara compuesto por
      $sql = "SELECT COUNT(*) AS numVentasByUser FROM VENTAS WHERE usuarioID = '$idUsuario' AND empresaID = '$idEmprersa'";
      try {
        $query = mysqli_query($conexion, $sql);
        $fetch = mysqli_fetch_assoc($query);
        $numVenta = $fetch['numVentasByUser']+1;
        $fecha = date('Y-m-d');
        $hora = date('H:m:i');
        $feria = $montoPagoTotal - $totalCobro;

        $sql2 = "INSERT INTO VENTAS (num_comprobante,fechaVenta,horaVenta,totalVenta,estatusVenta,descuentoVenta,
        montoPago,cambioPago,tipoPago,clienteID,empresaID,usuarioID) VALUES ('$numVenta','$fecha','$hora',
        '$totalCobro','Finalizada','$descuento','$montoPagoTotal','$feria','$tipoPago','$cliente','$idEmprersa','$idUsuario')";
        try {
          $query2 = mysqli_query($conexion, $sql2);
          //se inserto correctamente la venta, mostramos el id de la venta
          $idVenta = mysqli_insert_id($conexion);
          //una vez obtenido el id de la venta, actualizaremos el
          $sql3 = "UPDATE DETALLEVENTA SET ventaID = '$idVenta' WHERE usuarioVenta = '$usuario' AND ventaID IS NULL";
          try {
            $query3 = mysqli_query($conexion, $sql3);
            //una vez proc4esado el ticket, se actualizaran las cantidades en el inventario
            //para ello realizaremos la consulta anterior
            $sqlAux2 = "SELECT * FROM DETALLEVENTA a INNER JOIN ARTICULOSUCURSAL b ON 
            a.articuloID = b.articuloID WHERE a.ventaID = '$idVenta' AND a.usuarioVenta = '$usuario'";
            $queryAux2 = mysqli_query($conexion, $sqlAux2);
            while($fetchAux2 = mysqli_fetch_assoc($queryAux2)){
              //obtendremos los datos de los articulos
              $cantidadVenta = $fetchAux2['cantidadVenta'];
              $cantidadExiste = $fetchAux2['existenciaSucursal'];
              $nuevaCantidad = $cantidadExiste-$cantidadVenta;
              $idArticulo = $fetchAux2['articuloID'];
              //hacemos el update a la sucursal
              $sqlCant = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$nuevaCantidad' 
              WHERE articuloID = '$idArticulo' AND sucursalID = '$idSucursal'";
              $queryCant = mysqli_query($conexion, $sqlCant);

              
            }//fin del whileAux2

            //antes de finalizar el proceso, consultamos si se cuenta con servicios
            $sqlTra = "SELECT * FROM DETALLEVENTA WHERE ventaID = '$idVenta' AND trabajoID IS NOT NULL";
            $queryTra = mysqli_query($conexion, $sqlTra);
            if(mysqli_num_rows($queryTra) > 0){
              //si se detectaron trabajos, los actualizamos la fecha de cobro
              while($fetchTra = mysqli_fetch_assoc($queryTra)){
                $idTrabajo = $fetchTra['trabajoID'];
                $fechaTra = date('Y-m-d');
                $horaTra = date('H:i:s');

                $sqlUpdateTra = "UPDATE TRABAJOS SET fechaCobro = '$fechaTra', horaCobro = '$horaTra', 
                usuarioCobro = '$usuario' WHERE idTrabajo = '$idTrabajo'";
                $queryUpdateTra = mysqli_query($conexion, $sqlUpdateTra);
              }//fin del while
              //ahora si se completo el proceso de guardar la ficha
              $res = ["status"=>"ok","mensaje"=>"operationSuccess","data"=>$idVenta];
              echo json_encode($res);
            }else{
              //no se localizaron trabajos en esta venta, podemos dar por terminado el proceso
              $res = ["status"=>"ok","mensaje"=>"operationSuccess","data"=>$idVenta];
              echo json_encode($res);
            }
            //ahora si se completo el proceso de guardar la ficha
            // $res = ["status"=>"ok","mensaje"=>"operationSuccess","data"=>$idVenta];
            // echo json_encode($res);
          } catch (\Throwable $th) {
            //error al actuaslizar el detalle de ficha
            $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al procesar el detalle de venta: ".$th];
            echo json_encode($res);
          }
          
        } catch (\Throwable $th) {
          //error al inserta la venta
          $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al guardar la venta: ".$th];
          echo json_encode($res);
        }
        
      } catch (\Throwable $th) {
        //error al consultar el numero de ticket
      }

    }else{
      if($cantidadSuperada == 1){
        //la cantidad del inventario no coincide
        $res = ["status"=>"error","mensaje"=>"Las cantidades indicadas superan el inventario actual."];
        echo json_encode($res);
      }else{
        //no se indico un monto correcto
        $res = ["status"=>"error","mensaje"=>"No se indico un monto valido para cubrir el pago."];
        echo json_encode($res);
      }
    }
    
  }elseif(!empty($_POST['usuarioMov'])){
    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    $idUsuario = json_decode($datosUsuario)->idUsuario;

    $usuarioMov = $_POST['usuarioMov'];
    $fechaMov = $_POST['fechaMov'];
    $tipoMov = $_POST['tipoMov'];
    $montoMov = $_POST['montoMov'];
    $observ = $_POST['observMov'];
    //empezamos con las validaciones
    if($usuarioMov == $usuario){

      $apertura = setApertura($fechaMov,$montoMov,$idUsuario,$idSucursal,$idEmprersa,$tipoMov,$observ);
      // $apert = json_decode($apertura);
      echo $apertura;

    }else{
      //incongruencia en el usuario, no se debe aperturar
      //un usuario ageno
      $res = ["status"=>"error","mensaje"=>"Incongruencia en el usuario"];
      echo json_encode($res);
    }
  }elseif(!empty($_POST['observCierre'])){
    //seccion para realizar el cierre de un cajero
    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    $idUsuario = json_decode($datosUsuario)->idUsuario;



    $efecTivoTot = $_POST['efectivoTotCaja'];
    $montoRetira = $_POST['montoRetiraEfe'];
    $obserCierre = $_POST['observCierre'];
    $totalVenta = $_POST['totalVenta'];
    $montoDig = $_POST['montoDigital'];
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');
    
    //copnsultamos la apertura del dia
    $sqlAper = "SELECT * FROM MOVCAJAS WHERE usuarioMov = '$idUsuario' AND conceptoMov = '1' AND 
    sucursalMovID = '$idSucursal' AND fechaMovimiento = '$fecha'";
    try {
      $queryAper = mysqli_query($conexion, $sqlAper);
      if(mysqli_num_rows($queryAper) == 1){
        $fetchAper = mysqli_fetch_assoc($queryAper);
        $montoApertura = $fetchAper['montoMov'];
        //comenzamos con las validaciones para aplicar el cierre
        $paso1 = 0;

        $error = "";
        if($montoRetira > 0){
          $montoQuedaCaja = $efecTivoTot - $montoRetira;
          $concep1 = "Concentracion excedente final";
          $mov1 = guardaMovCaja($montoRetira,$fecha,$hora,$idUsuario,'6',$concep1,$idSucursal,'S',$idEmprersa);
          $mov1 = json_decode($mov1);
          if($mov1->status == "ok"){
            //insertamos el final del cajero
            $mov2 = guardaMovCaja($montoQuedaCaja,$fecha,$hora,$idUsuario,'4',$obserCierre,$idSucursal,'S',$idEmprersa);
            $mov2 = json_decode($mov2);
            if($mov2->status == "ok"){
              $paso1 = 1;
            }else{
              //ocurrio un erro en el procesop del cierre de cajero
              $error = "Error en el proceso del cierre de cajero";
            }
          }else{
            //ocurrio un error en el proceso del excedente
            $error = "Error en el proceso de excedente de caja";
          }

        }else{
          $montoQuedaCaja = $efecTivoTot;
          // $concep1 = "Concentracion excedente final";
          $mov1 = guardaMovCaja($montoQuedaCaja,$fecha,$hora,$idUsuario,'4',$obserCierre,$idSucursal,'S',$idEmprersa);
          $mov1 = json_decode($mov1);
          if($mov1->status == "ok"){
            $paso1 = 1;
          }else{
            //error al insertar el cierre del cajero
            $error = "Error en el proceso del cierre de cajero 2";
          }
        }

        //comenzamos con el paso2 (mandar el saldo a las cuentas concentradoras)
        $paso2 = 0;
        if($montoDig > 0){
          //registro de pago con medios digitales
          $concep3 = "Cierre de pagos digitales";
          $mov3 = guardaMovCaja($montoDig,$fecha,$hora,$idUsuario,'5',$concep3,$idSucursal,'S',$idEmprersa);
          $mov3 = json_decode($mov3);
          if($mov3->status == "ok"){
            //ahora registramos la entrada de esos medios digitales
            $concep4 = "Deposito de pago con medios digitales";
            $mov4 = guardaMovCaja($montoDig,$fecha,$hora,$idUsuario,'8',$concep4,$idSucursal,'E',$idEmprersa);
            $mov4 = json_decode($mov4);
            if($mov4->status == "ok"){
              //ahora depositamos el saldo en efectivo
              $concep5 = "Deposito de pago en efectivo";
              $mov5 = guardaMovCaja($montoRetira,$fecha,$hora,$idUsuario,'7',$concep5,$idSucursal,'E',$idEmprersa);
              $mov5 = json_decode($mov5);
              if($mov5->status == "ok"){
                //depositamos los dineros
                $saldoTrans = datoEmpresaSesion($usuario,"saldoTransferencia");
                $saldoTrans = json_decode($saldoEfe)->dato;

                $nuevoSalDigital = $saldoTrans + $montoDig;
                
                $updateSalDig = sumaSaldo($nuevoSalDigital,$saldoTrans,$idEmprersa,"saldoTransferencia");
                $updateSalDig = json_decode($updateSalDig);
                if($updateSalDig->status == "ok"){
                  $paso2 = 1;
                }else{
                  $error = "No fue posible actualizar el saldo digital de la empresa";
                }
              }else{
                //error en el deposito de excedente por pago en efectivo
                $error = "Error en el proceso de deposito de excedente";
              }
            }else{
              //error en el deposito de pagos por medios digitlaes 
              $error = "Error en el proceso de deposito de pagos digitales";
            }
          }else{
            //ocurrio un error al registrar la salida digital
            $error = "Error en el proceso del salida digital";
          }
        }else{
          if($montoRetira > 0){
            //no registro medios digitales, asi que solo registramos la venta en efectivo
            $concep6 = "Deposito de pago en efectivo";
            $mov6 = guardaMovCaja($montoRetira,$fecha,$hora,$idUsuario,'7',$concep6,$idSucursal,'E',$idEmprersa);
            $mov6 = json_decode($mov6);
            if($mov6->status == "ok"){
              
              $saldoEfe = datoEmpresaSesion($usuario,"saldoEfectivo");
              $saldoEfe = json_decode($saldoEfe)->dato;

              $nuevoSaldo = $saldoEfe+$montoRetira;
              $updateSaldo = sumaSaldo($nuevoSaldo,$saldoEfe,$idEmprersa,"saldoEfectivo");
              $updateSaldo = json_decode($updateSaldo);
              if($updateSaldo->status == "ok"){
                $paso2 = 1;  
              }else{
                $error = "No se actualizo el saldo de la empresa, reportar a soporte.";
              }
            }else{
              //error al registrar el deposito en efectivo
              $error = "Error en el proceso de deposito en efectivo 2";
            }
          }else{
            //no registro salida para la empresa, terminaoms el proceso
            $paso2 = 1;
          }
          
        }


        if($paso1 == 1 && $paso2 == 1){
          $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
          echo json_encode($res);
        }else{
          //ocurrio un error durante el proceso de registro
          $res = ["status"=>"error","mensaje"=>"Ocurrio un error durante el proceso de registro de ventas."];
          echo json_encode($res);
        }


      }else{
        //no cuenta con apertura
        $res = ["status"=>"error","mensaje"=>"El cajero no cuenta con una apertura del dia. Movimiento no procesado"];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //throw $th;
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar los movimientos de caja. ".$th];
      echo json_encode($res);
    }

  }elseif(!empty($_POST['addTrabajoCobro'])){
    // seccion para registar el cobro de un trabajo / servicio
    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    $idTrabajo = $_POST['addTrabajoCobro'];
    //verificamos que exista el trabajo
    $sql = "SELECT * FROM TRABAJOS WHERE idTrabajo = '$idTrabajo' AND empresaID = '$idEmprersa' AND 
    sucursalID = '$idSucursal'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) == 1){
        //antes de agregarlo a DETALLEVENTA verificamos que no este ya agregado al carrito
        $sql2 = "SELECT * FROM DETALLEVENTA WHERE trabajoID = '$idTrabajo'";
        try {
          $query2 = mysqli_query($conexion, $sql2);
          if(mysqli_num_rows($query2) == 0){
            //no se encuentra agregado ahora lo agregamos a DETALLEVENTA
            $fetchTrabajo = mysqli_fetch_assoc($query);
            $precioFinal = $fetchTrabajo['costoFinal'];
            $anticipo = $fetchTrabajo['anticipo'];

            $costoFinal = $precioFinal-$anticipo;

            $sql3 = "INSERT INTO DETALLEVENTA (cantidadVenta,precioUnitario,subtotalVenta,usuarioVenta,
            sucursalID,trabajoID) VALUES ('1','$costoFinal','$costoFinal','$usuario','$idSucursal','$idTrabajo')";
            try {
              $query3 = mysqli_query($conexion, $sql3);
              //se inserto correctamente, ahora, se dice que debemos consultar todos los registros de
              //DETALLEVENTA que cuenten con el ventaID en nulo
              $sql4 = "SELECT * FROM DETALLEVENTA WHERE usuarioVenta = '$usuario' AND ventaID IS NULL";
              try {
                $query4 = mysqli_query($conexion, $sql4);
                //como es un metodo para agregar, deben de exisitir registros
                $contenido = "";
                $total = 0;
                $totalArticulos = 0;
                while($fetch4 = mysqli_fetch_assoc($query4)){
                  //recorreremos todos los registros
                  $nombreProd;
                  $cantidadVenta;
                  $subTotal;
                  $idProdVenta;
                  $cadenaTruncada;
                  $esServicio;
                  if($fetch4['articuloID'] != NULL || $fetch4['articuloID']  > 0){
                    //se trata de un articulo
                    $idArti = $fetch4['articuloID'];
                    $sql5 = "SELECT * FROM ARTICULOS WHERE idArticulo = '$idArti'";
                    $query5 = mysqli_query($conexion, $sql5);
                    $fetch5 = mysqli_fetch_assoc($query5);
                    
                    $nombreProd = $fetch5['nombreArticulo'];
                    $cantidadVenta = $fetch4['cantidadVenta'];
                    $subTotal = $cantidadVenta * $fetch4['precioUnitario'];
                    $total = $total + $subTotal;
                    $idProdVenta = $fetch4['idDetalleVenta'];
                    if (strlen($nombreProd) > 20) {
                      $cadenaTruncada = substr($nombreProd, 0, 20) . "...";
                    } else {
                        $cadenaTruncada = $nombreProd;
                    }
                    $totalArticulos = $totalArticulos + $cantidadVenta;
                    // 
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
                    // 
                  }else{
                    //se trata de un servicio
                    $idTra = $fetch4['trabajoID'];
                    $sql6 = "SELECT * FROM TRABAJOS WHERE idTrabajo = '$idTra'";
                    $query6 = mysqli_query($conexion, $sql6);
                    $fetch6 = mysqli_fetch_assoc($query6);

                    $nombreProd = "Cobro de Servicio";
                    $cantidadVenta = '1';
                    $subTotal = $fetch6['costoFinal'];
                    $anticipo = $fetch6['anticipo'];
                    $subTotal = $subTotal-$anticipo;
                    $total = $total + $subTotal;
                    $idProdVenta = $fetch4['idDetalleVenta'];
                    $cadenaTruncada = $nombreProd;
                    $totalArticulos = $totalArticulos + 1;
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
                    // 
                  }
                  
                }//fin del while
                $respuesta = "operationSuccess+-_-+".$contenido."+-_-+".$total."+-_-+".$totalArticulos;
                echo $respuesta;
              } catch (\Throwable $th) {
                //error al cosnultar las ventas
                $respuesta = "DataError+-_-+Ocurrio un error al consultar las ventas ".$th;
                echo $respuesta;
              }
            } catch (\Throwable $th) {
              //error al insertar el traajo en DETALLEVENTA
              $respuesta = "DataError+-_-+Ocurrio un error al registrar la venta ".$th;
              echo $respuesta;
            }
          }else{
            //ya se encuentra agregado, 
            $respuesta = "DataError+-_-+El servicio ya se encuentra en el carrito";
            echo $respuesta;
          }
        } catch (\Throwable $th) {
          //error 
          $respuesta = "DataError+-_-+Ocurrio un error al consultar la venta ".$th;
          echo $respuesta;
        }
      }else{
        //trabajo nho localizado
        echo "No data";
      }
    } catch (\Throwable $th) {
      //throw $th;
      echo "error";
    }
  }
}

?>