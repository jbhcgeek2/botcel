<?php
session_start();

if(!empty($_SESSION['usuarioPOS'])){
  //insertamos los archivos que necesitamos
  include("articulos.php");
  include("usuarios.php");
  include("documentos.php");
  include("conexion.php");
  include("operacionesCaja.php");
  include("empresas.php");
  $usuario = $_SESSION['usuarioPOS'];

  $empresa = datoEmpresaSesion($usuario,"id");
  $idEmprersa = json_decode($empresa)->dato;
  $datosUsuario = getDataUser($usuario,$idEmprersa);
  $idSucursal = json_decode($datosUsuario)->sucursalID;
  $idUsuario = json_decode($datosUsuario)->idUsuario;

  if(!empty($_POST['totCompra'])){
    //seccion de operaciones para entradas y salidas de mercancia
    // $tipoMov = $_POST['tipoMov'];
    // $producto = $_POST['producto'];
    // $codigo = $_POST['codProducto'];
    // $sucursal = $_POST['sucursal'];
    // $cantidad = $_POST['cantidadMov'];
    // $precioCompra = $_POST['precioCompra'];
    // $totalCompra = $_POST['totalCompra'];
    // $updatePrecio = $_POST['cambiarPrecio'];
    // $nuevoPrecio = $_POST['preActual'];

    //los movimientos estaran conformados por 1 o mas movimientos
    //los cuales se guardaran en 2 tablas INGRESO y DETALLEINGRESO

    $totalCompra = $_POST['totCompra'];
    $totalArticulos = $_POST['numTotArti'];
    $numOperaciones = $_POST['numRowsTab'];
    $numeroComprobante = $_POST['numCompro'];
    $tipoComprobante = $_POST['tipoCompro'];
    $proveedor = $_POST['proveedorMov'];
    $fecha = $_POST['fechaMov'];
    $hora = date('H:i:s');
    $totalArti = $_POST['numTotArti'];
    //verificamos los campos obligatorios
    //para este caso seran: Proveedor, Comprobante,

    if(!empty($proveedor) && !empty($numeroComprobante) && !empty($tipoComprobante) && $numOperaciones > 0){
      //primero ingresamos la venta
      $sql = "INSERT INTO INGRESO (numComprobante,tipoComprobante,fechaIngreso,
      horaIngreso,totalIngreso,totArticulos,proveedorID,empresaID) VALUES ('$numeroComprobante',
      '$tipoComprobante','$fecha','$hora','$totalCompra','$totalArticulos','$proveedor','$idEmprersa')";
      try {
        $query = mysqli_query($conexion, $sql);
        $idIngreso = mysqli_insert_id($conexion);
        $montoTotal = 0;
        $restaCap = 0;
        $salMerca = 0;
        $montoSalida = 0;
        //continuamos a realizar los ingresos detallados
        //buscamos los articulos que se afectaron
        $van = 0;
        for($x = 1; $x <= $numOperaciones; $x++){
          $prodId = $_POST['prodIdTab'.$x];
          if(!empty($prodId)){
            //se indicaron datos
            $cantidad = $_POST['cantMovTab'.$x];
            $sucursalId = $_POST['sucIdMovTab'.$x];
            $tipoMov = $_POST['tipoMovTab'.$x];
            $precioCompraMov = $_POST['precioComTab'.$x];
            $idProdMov = $_POST['prodIdTab'.$x];
            $nuevoprecio = $_POST['valorNuevoPre'.$x];
            $sql2 = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,
            fechaMov,usuarioMov,tipoMov,prodMov) VALUES ('$cantidad','$precioCompraMov','$idIngreso','$sucursalId',
            '$fecha','$usuario','$tipoMov','$idProdMov')";

            try {
              $query2 = mysqli_query($conexion, $sql2);
              //se inserto el detalle del movimiento, ahora actualizamos las cantidades del producto
              $numExisteData = getArtiSucursal($sucursalId,$idProdMov);
              $numArti = json_decode($numExisteData)->data;
              if($tipoMov == "Entrada"){
                $nuevosArti = $numArti+$cantidad;
                $montoTotal = $montoTotal + ($precioCompraMov * $cantidad);
                $restaCap = 1;
              }else{
                $nuevosArti = $numArti-$cantidad;
                if($tipoMov == "Salida"){
                  $salMerca = 1;
                  $montoSalida = $precioCompraMov * $cantidad;
                }
                
              }
              
              $actualizaCant = setCantidad($nuevosArti,$idProdMov,$sucursalId);
              $resActualiza = json_decode($actualizaCant);
              if($resActualiza->status == "ok"){
                //verificamos si se actualiza el precio de venta y si el precio de compra es mayor al anterior
                //para tambien actualizarlo
                if($tipoMov == "Entrada"){
                  //unicamente en las entradas es donde actualizaremos los precios
                  $camnioPreX = $_POST['cambiaPrecio'.$x];
                  $procesa = procesaMovsProd($camnioPreX,$nuevoprecio,$precioCompraMov,$idProdMov,$idEmprersa);
                  $procesaRes = json_decode($procesa);
                  if($procesaRes->status == "ok"){
                    //todo paso correctamente
                  }else{
                    $van = 1;
                  }
                }elseif($tipoMov == "Salida"){
                  //aplicamos directamente la salida de la mercancia en el saldo de la empresa
                  //PROCESAMOS
                  //verificamos si se da alguna salida de mercancia
                  
                  $salidaCap = updateCapital($idEmprersa,$montoSalida,"Salida",$sucursalId,"10");
                  $salidaCap = json_decode($salidaCap);
                  if($salidaCap->status == "ok"){
                    //insrtamos el movimiento en cajas
                    $observ = "Salida de Mercancia";
                    $setMovSal = guardaMovCaja($montoSalida,$fecha,$hora,$idUsuario,'10',$observ,$sucursalId,'S',$idEmprersa);
                    $setMovSal = json_decode($setMovSal);
                    if($setMovSal->status == "ok"){
                      //se proceso la salida correctamente
                    }else{
                      //error al procear la salida
                    }
                  }
                  
                }
                
              }else{
                //ocurrio un error al actualizar las cantidades
                $van = 1;
              }
            } catch (\Throwable $th) {
              $van = 1;
            }
          }
        }//fin del for

        if($van == 0){
          //se insertaron los movimientos correcamente
          //si el movimiento fue entrada, restamos de nuestro capital, esa cantidad
          if($restaCap == 1){
            //restamos el total del moviiento al capital
            // $nuevopCapSuma = 
            $updateCap = updateCapital($idEmprersa,$montoTotal,"Salida",$idUsuario,"9");
            $updateCap = json_decode($updateCap);
            if($updateCap->status == "ok"){
              //insrtamos el movimiento en cajas
              $observ = "Adquisicion de Mercancia";
              $setMov = guardaMovCaja($montoTotal,$fecha,$hora,$idUsuario,'9',$observ,$idSucursal,'S',$idEmprersa);
              $setMov = json_decode($setMov);
              if($setMov->status == "ok"){
                $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
                echo json_encode($res);
              }else{
                $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar los saldos 2."];
                echo json_encode($res);
              }
            }else{
              //ocurrio un error en la actualizacion del saldo
              $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar los saldos."];
              echo json_encode($res);
            }
          }else{
            $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
            echo json_encode($res);
          }
        }else{
          //ocurrieron errores al insertar los movimiento detallados
          $res = ["status"=>"error","mensaje"=>"Ocurrio un error al insertar el detallado de ingresos. ".$th];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        $res = ["status"=>"error","mensaje"=>"Ocurrio un error al procesar el movimiento. ".$th];
        echo json_encode($res);
      }
    }else{
      //campos vacios
      $res = ["status"=>"error","mensaje"=>"Verifica que todos los campos esten capturados."];
      echo json_encode($res);
    }
    
    

    


    

    // $err = 0;

    // //consultamos los datos del producto con el combo o el codigo
    // if($producto != ""){
    //   //buscamos por producto
    //   $sql = "SELECT * FROM ARTICULOS a INNER JOIN ARTICULOSUCURSAL b ON a.idArticulo = b.articuloID
    //   WHERE a.idArticulo = '$producto' AND a.empresaID = '$idEmprersa' AND b.sucursalID = '$sucursal'";
    // }elseif(!empty($codigo)){
    //   //buscamos por codigo
    // }else{
    //   //no se indico nada, marcamos error
    // }

    
    // try {
    //   $query = mysqli_query($conexion, $sql);
    //   if(mysqli_num_rows($query) > 0){
    //     $fetch = mysqli_fetch_assoc($query);
    //     $existenciaActual = $fetch['existenciaSucursal'];
    //     $idArtiSuc = $fetch['idArtiSuc'];
    //     $idArticuloReal = $fetch['articuloID'];

    //     if($tipoMov == "Entrada"){
    //       //si el movimiento es entrrada, sumanos la cantidad actual y la nueva
    //       $nuevaCant = $existenciaActual + $cantidad;
    //       //ahora hacemos el update
    //       $sqlUp = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$nuevaCant' WHERE 
    //       sucursalID = '$sucursal' AND idArtiSuc = '$idArtiSuc'";
    //       try {
    //         $queryUp = mysqli_query($conexion, $sqlUp);
    //         //verificamos si se va a actualizar el precio de venta
    //         if($updatePrecio == "on"){
    //           //se actualiza el precio de venta
    //           $sqlUp2 = "UPDATE ARTICULOS SET precioUnitario = '$nuevoPrecio' WHERE idArticulo = '$idArticuloReal'
    //           AND empresaID = '$idEmprersa'";
    //           try {
    //             $queryUp2 = mysqli_query($conexion, $sqlUp2);
    //             //aqui damos por terminado el proceso de actualizacion
    //             $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    //             echo json_encode($res);
    //           } catch (\Throwable $th) {
    //             //ocurrio un error ala ctualizar
    //             $res = ["status"=>"error","mensaje"=>"Ocurrio un error al actualizar el producto, 2."];
    //             echo json_encode($res);
    //           }
    //         }else{
    //           //no es necesario actualizar el precio de venta, por lo que finalizamos
    //           $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    //           echo json_encode($res);
    //         }
    //         if($pasa = 1){
    //           //aqui podremos insertar el movimiento historio
    //           $sqlingre = "INSERT INTO ";

    //           $sqlDetalle  = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,
    //           fechaMov,usuarioMov,tipoMov) VALUES ('$cantidad','$precioCompra','')";
    //         }else{
    //           //
    //         }
    //       } catch (Throwable $th) {
    //         $res = ["status"=>"error","mensaje"=>"Ocurrio un error al  actualizar las cantidades."];
    //         echo json_encode($res);
    //       }
          
    //     }elseif($tipoMov == "Salida"){
    
    //     }
    //   }else{
    //     //error producto no localizado
    //     $res = ["status"=>"error","mensaje"=>"No fue posible localizar el producto indicado."];
    //     echo json_encode($res);
    //   }
    // } catch (\Throwable $th) {
    //   //error en la consulta del ARTICULO
    //   $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar el inventario."];
    //   echo json_encode($res);
    // }

    
  }elseif(!empty($_POST['campoBus'])){
    //seccion para consultar la informacion de un articulo
    $campo = $_POST['campoBus'];
    $valor = $_POST['valorBus'];
    $sucural = 0;
    if(!empty($_POST['sucursalBus'])){
      $sucural = $_POST['sucursalBus'];
    }else{
      $sucural = $idSucursal;
    }
    $sql = "";

    if($campo == "codProducto"){
      $sql = "SELECT * FROM ARTICULOS a INNER JOIN ARTICULOSUCURSAL b ON 
      a.idArticulo = b.articuloID WHERE a.codigoProducto = '$valor' AND a.empresaID = '$idEmprersa'
      AND b.sucursalID = '$sucural'";
    }else{
      $sql = "SELECT * FROM ARTICULOS a INNER JOIN ARTICULOSUCURSAL b ON 
      a.idArticulo = b.articuloID WHERE a.idArticulo = '$valor' AND a.empresaID = '$idEmprersa'
      AND b.sucursalID = '$sucural'";
    }
    //hacemos la consulta
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) == 1){
        //se mando todo ok
        $fetch = mysqli_fetch_assoc($query);
        $res = ["status"=>"ok","data"=>$fetch];
        echo json_encode($res);
      }else{
        //producto no localizado
        $res = ["status"=>"error","mensaje"=>"Producto no localizado"];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //throw $th;
      $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar la informacion del producto. ".$th];
      echo json_encode($res);
    }
  }elseif(!empty($_POST['prodModalTras'])){
    //seccion para hacer que se realicen los traspasos de sucursal
    $producto = $_POST['prodModalTras'];
    $sucOrigi = $_POST['sucOriTras'];
    $sucDesti = $_POST['sucDesTras'];
    $cantidad = $_POST['cantidadTras'];
    $comproTras = $_POST['numComproTras'];
    $fechaTras = $_POST['fechaTras'];
    $tipoComp = $_POST['tipoComproTras'];
    $horaTras = date('H:i:s');

    //verificamos la existencia del producto
    //en la sucursal origen
    $cantidadSuc = getArtiSucursal($sucOrigi,$producto);
    $cantidadSuc = json_decode($cantidadSuc);
    if($cantidadSuc->status == "ok"){
      //verificamos si la cantidad a traspasar es igual o superior a lo que se desea
      if($cantidadSuc->data > 0){
        if($cantidadSuc->data >= $cantidad){
          $cantidadOrigen = $cantidadSuc->data;
          $infoArti = getInfoproducto($idEmprersa,$producto);
          $infoArti = json_decode($infoArti);
          if($infoArti->status == "ok"){
            $precioCompra = $infoArti->data->precioCompra;
            $montoMov = $precioCompra * $cantidad;
            //si se cuenta con stok, asi que procesamos el movimiento
            //primero insertamos en el INGRESO
            $sql = "INSERT INTO INGRESO (numComprobante,tipoComprobante,fechaIngreso,horaIngreso,
            totalIngreso,totArticulos,empresaID) VALUES ('$comproTras','$tipoComp','$fechaTras',
            '$horaTras','$montoMov','$cantidad','$idEmprersa')";
            try {
              $query = mysqli_query($conexion, $sql);
              $idMovTras = mysqli_insert_id($conexion);
              //ahora insertamos el detalle movimiento, primero la salida
              $sql2 = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,fechaMov,usuarioMov,tipoMov,prodMov) 
              VALUES ('$cantidad','$precioCompra','$idMovTras','$sucDesti',
              '$fechaTras','$usuario','Salida','$producto')";
              $sql22 = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,fechaMov,usuarioMov,tipoMov,prodMov) 
              VALUES ('$cantidad','$precioCompra','$idMovTras','$sucOrigi',
              '$fechaTras','$usuario','Entrada','$producto')";
              try {
                $query2 = mysqli_query($conexion, $sql2);
                $query22 = mysqli_query($conexion, $sql22);
                //ahora afectamos las cantidades del inventario
                $nuevaCantidad = $cantidadOrigen - $cantidad;
                
                $cantNuevaSuc = getArtiSucursal($sucDesti,$producto);
                $cantNuevaSuc = json_decode($cantNuevaSuc);
                if($cantNuevaSuc->status == "ok"){
                  if($cantNuevaSuc->mensaje == "noData"){
                    //el producto no existe, se insertara
                    $nuevaCantidad2 = $cantidad;
                    $sql3 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$nuevaCantidad' 
                    WHERE sucursalID = '$sucOrigi' AND articuloID = '$producto'";
                    $sql33 = "INSERT INTO ARTICULOSUCURSAL (existenciaSucursal,sucursalID,articuloID) 
                    VALUES ('$nuevaCantidad2','$sucDesti','$producto')";
                  }else{
                    //el producto existe, lo actualizamos
                    $cantSucDes = $cantNuevaSuc->data;
                    $nuevaCantidad2 = $cantSucDes + $cantidad;

                    $sql3 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$nuevaCantidad' 
                    WHERE sucursalID = '$sucOrigi' AND articuloID = '$producto'";
                    $sql33 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$nuevaCantidad2' WHERE 
                    sucursalID = '$sucDesti' AND articuloID = '$producto'";
                  }

                  try {
                    $query3 = mysqli_query($conexion, $sql3);
                    $query33 = mysqli_query($conexion, $sql33);
                    //hasta este punto se puede decir que ya esta procesado toda la info
                    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
                    echo json_encode($res);
                  } catch (\Throwable $th) {
                    //error al actualizar las cantidades
                    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar las cantidades, reporta a soporte tecnico. ".$th];
                    echo json_encode($res);
                  }
                }else{
                  //error al consultar la cantidad
                  $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar las cantidades en inventario. "];
                  echo json_encode($res);
                }
                
              } catch (\Throwable $th) {
                $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al registrar la transaccion. ".$th];
                echo json_encode($res);
              }

            } catch (Throwable $th) {
              $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al insertar el detalle de movimiento. ".$th];
              echo json_encode($res);
            }
          }else{
            //error al consultar la informacion del estatus
            $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar la informacion del articulo. ".$th];
            echo json_encode($res);
          }
          
          // $query = mysqli_query($conexion, $sql);
        }else{
          //se quiere traspasar mas de lo que se cuenta en stok
          $cantSuc = $cantidadSuc->data;
          $res = ["status"=>"error","mensaje"=>"Cuidado, actualmente cuentas con ".$cantSuc." articulos en la sucursal origen."];
          echo json_encode($res);  
        }
      }else{
        //sin productos disponibles en la sucursal origen
        $res = ["status"=>"error","mensaje"=>"No se cuenta con stock en la sucursal origen."];
        echo json_encode($res);
      }
    }else{
      //error en la consulta del articulo
      $res = ["status"=>"error","mensaje"=>"Articulo no localizado en la sucursal origen"];
      echo json_encode($res);
    }
  }
}

?>