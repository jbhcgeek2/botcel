<?php 
  session_start();

  if(!empty($_SESSION['usuarioPOS'])){
    //se detecto la sesion, cargamos las funciones
    include("empresas.php");
    include("conexion.php");
    include("usuarios.php");
    include("trabajos.php");
    include("articulos.php");

    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
		$empresa = json_decode($empresa);
		$idEmpresaSesion = $empresa->dato;

    $dataUSer = getDataUser($usuario,$idEmpresaSesion);
		$dataUSer = json_decode($dataUSer);
		$idSucursalN = $dataUSer->sucursalID;
    $idUsuario = $dataUSer->idUsuario;
    

    //verificamos la existencia de metodos
    if(!empty($_POST['servCheck'])){
      //verificamos la existencia y precio de un servicio
      $idServ = $_POST['servCheck'];

      $sql = "SELECT * FROM SERVICIOS WHERE empresaID = '$idEmpresaSesion' 
      AND idServicio = '$idServ'";
      try {
        $query = mysqli_query($conexion, $sql);

        if(mysqli_num_rows($query) == 1){
          $fetch = mysqli_fetch_assoc($query);
          $precio = "";
          if($fetch['precioFijo'] == 1){
            $precio = $fetch['precioFijo'];
          }else{
            $precio = "0";
          }
          $res = ['status'=>'ok','mensaje'=>'operationComplete','data'=>$precio];
          echo json_encode($res);
        }else{
          //servicio no localizado
          $res = ['status'=>'error','mensaje'=>'No se localizo el servicio'];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        //error de consulta BD
        $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al consultar el servicio'];
        echo json_encode($res);
      }
    }elseif(!empty($_POST['clienteTrabajo'])){
      //seccion para dar de alta un nuevo trabajo
      //verificamos que esten capturados todos los campos
      $requeridos = ['clienteTrabajo','fechaServicio','tipoDispositivo','tipoServicio','marcaServicio',
      'modeloServicio','numberDevice','descripcionProblema'];
      $pasa = 0;

      for ($i=0; $i < count($requeridos); $i++) { 
        $valorCampo = $_POST[$requeridos[$i]];

        if(empty($valorCampo)){
          //campo vacio
          $pasa = 1;
        }
      }//fin del for

      if($pasa == 0){
        //captuiramos los datos en variables
        $cliente = $_POST['clienteTrabajo'];
        $fechaAlta = $_POST['fechaServicio'];
        $tipoDispo = $_POST['tipoDispositivo'];
        $tipoServ = $_POST['tipoServicio'];
        $marca = $_POST['marcaServicio'];
        $modeloServicio = $_POST['modeloServicio'];
        $serieDispo = $_POST['numberDevice'];
        $accesorios = $_POST['accesorioServicio'];
        $problema = $_POST['descripcionProblema'];
        $observacion = $_POST['observServicio'];
        $contraDispo = $_POST['contraDisp'];
        $fechaEntrega = $_POST['fechaEntrega'];
        $costoServ = $_POST['costoServicio'];
        $anticipo = $_POST['anticipoServicio'];
        //verificamos que la fecha no sea pasada
        $fechaActual = date('Y-m-d');
        $horaActual = date('H:i:s');

        if($fechaAlta <= $fechaActual){
          if($fechaEntrega >= $fechaActual){
            //las fechas son correectas, podemos registrar el producto
            $nuevoTrabajo = altaTrabajo($cliente,$fechaAlta,$tipoDispo,$tipoServ,$marca,$modeloServicio,
            $serieDispo,$accesorios,$problema,$observacion,$contraDispo,$fechaEntrega,$costoServ,$anticipo,
            $usuario,$idSucursalN,$idEmpresaSesion,$idUsuario);

            //verificamos la respuesta para registrar el ticket
            if($anticipo > 0){
              //el trabajo tiene anticipo, asi que registramos el movimiento en cajas
              //vamos a ahcer las cosas al revez, primero insertaremos la venta y despues el detalle venta
              $sqlAux1 = "SELECT COUNT(*) AS numVentasByUser FROM VENTAS WHERE usuarioID = '$idUsuario' AND empresaID = '$idEmpresaSesion'";
              $queryAux1 = mysqli_query($conexion, $sqlAux1);
              $fetchAux1 = mysqli_fetch_assoc($queryAux1);
              $numT = $fetchAux1['numVentasByUser']+1;

              $sqlAux2 = "INSERT INTO VENTAS (num_comprobante,fechaVenta,horaVenta,totalVenta,montoPago,cambioPago,
              tipoPago,clienteID,empresaID,usuarioID) VALUES ('$numT','$fechaActual','$horaActual','$anticipo',
              '$anticipo','0','Efectivo','$cliente','$idEmpresaSesion','$idUsuario')";
              try {
                $queryAux2 = mysqli_query($conexion, $sqlAux2);
                $idVenta = mysqli_insert_id($conexion);
                $datosTrab = json_decode($nuevoTrabajo);
                $idTrabajo = $datosTrab->data;
                //ahora insertamos el detalleventa
                $sqlAux3 = "INSERT INTO DETALLEVENTA (cantidadVenta,precioUnitario,subtotalVenta,usuarioVenta,
                sucursalID,trabajoID,ventaID) VALUES ('1','$anticipo','$anticipo','$usuario','$idSucursalN',
                '$idTrabajo','$idVenta')";
                try {
                  $queryAux3 = mysqli_query($conexion, $sqlAux3);
                  //hasta aqui podemos tar por terminado el alta del trabajo
                  echo $nuevoTrabajo;
                } catch (\Throwable $th) {
                  //ocurrio un error al insertar el detalleventa
                  $res =['status'=>'error','mensaje'=>'Ocurrio un error al insertar el detalle de venta: '.$th];
                  echo json_encode($res);
                }
              } catch (\Throwable $th) {
                //ocurrio un error al insertar la venta
                $res =['status'=>'error','mensaje'=>'Ocurrio un error al insertar la venta: '.$th];
                echo json_encode($res);
              }
            }else{
              echo $nuevoTrabajo;
            }
            
          }else{
            //fecha de entrega incorrecta
            $res = ['status'=>'error','mensaje'=>'Asegurate de ingresar una fecha de entrega correcta.'];
            echo json_encode($res);
          }
        }else{
          //fecha de alta incorrecta
          $res = ['status'=>'error','mensaje'=>'No se pueden ingresar fechas futuras.'];
          echo json_encode($res);
        }
        
      }else{
        //ocurrieron errores en los campos
        $res = ['status'=>'error','mensaje'=>'Asegurate de capturar todos los campos requeridos'];
        echo json_encode($res);
      }

    }elseif(!empty($_POST['trabajoStatus'])){
      //metodo para actualizar el estatus de un trabajo
      $idTrabajo = $_POST['trabajoStatus'];
      $status = $_POST['nuevoStatus'];

      $sql = "UPDATE TRABAJOS SET estatusTrabajo = '$status' WHERE idTrabajo = '$idTrabajo' AND 
      empresaID = '$idEmpresaSesion'";
      try {
        $query = mysqli_query($conexion, $sql);
        //se completo el registro
        $res = ['status'=>'ok','mensaje'=>'operationComplete'];
        echo json_encode($res);

      } catch (\Throwable $th) {
        //error al actualizar el estatus
        $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al actualizar el estatus: '.$th];
        echo json_encode($res);
      }
    }elseif(!empty($_POST['getAtArti'])){
      $categoria = $_POST['getAtArti'];
      
      $sql = "SELECT * FROM ARTICULOS a INNER JOIN ARTICULOSUCURSAL b ON a.idArticulo = b.articuloID 
      WHERE a.categoriaID = '$categoria' AND b.sucursalID = '$idSucursalN' AND b.existenciaSucursal > '0'";
      try {
        $query = mysqli_query($conexion, $sql);
        if(mysqli_num_rows($query) > 0){
          $datos = [];
          $x = 0;
          while($fetch = mysqli_fetch_assoc($query)){
            $datos[$x] = $fetch;
            $x++;
          }//fin del while
          $res = ['status'=>'ok','data'=>$datos];
          echo json_encode($res);
        }else{
          //sin articulos
          $res = ['status'=>'ok','data'=>'noData'];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al consultar los articulos disponibles'];
        echo json_encode($res);
      }
    }elseif(!empty($_POST['artiServicio'])){
      //seccion para registrar la pieza a un servicio/trabajo
      $articulo = $_POST['artiServicio'];
      $precioArticulo = $_POST['precioArtiServ'];
      $cantidadArti = $_POST['cantidadArtiServ'];
      $totalArti = $_POST['totalArtiServ'];
      $trabajo = $_POST['trabajoArtiServ'];
      $fecha = date('Y-m-d');
      $hora = date('H:i:s');

      $existenciaActual = getArtiSucursal($idSucursalN,$articulo);
      $existenciaActual = json_decode($existenciaActual);
      if($existenciaActual->status == "ok" && $existenciaActual->mensaje == "operationSuccess"){
        //verificamos si cuenta con la cantidad necesaria
        if($existenciaActual->data >= $cantidadArti){
          //si se cuenta con el inventario suficiente
          //primero insertaremos y despues descontaremos la pieza
          $sql = "INSERT INTO DETALLETRABAJO (articuloID,cantidad,precioUnitario,subTotalArticulo,
          sucursalID,empresaID,trabajoID,usuarioUtiliza,fechaMovimiento,horaMovimiento) VALUES 
          ('$articulo','$cantidadArti','$precioArticulo','$totalArti','$idSucursalN','$idEmpresaSesion',
          '$trabajo','$usuario','$fecha','$hora')";
          try {
            $query = mysqli_query($conexion,$sql);
            //se inserto ahora, vamos a descontar el articulo del inventario
            $nuevaCantidad = $existenciaActual->data - $cantidadArti;

            $updateCant = setCantidad($nuevaCantidad,$articulo,$idSucursalN);
            $updateCant = json_decode($updateCant);
            if($updateCant->status == 'ok'){
              $res = ['status'=>'ok','mensaje'=>'operationComplete'];
              echo json_encode($res);
            }else{
              //error, le decimos que contacte a soporte
              $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error fatal, contacta a soporte tecnico.'];
              echo json_encode($res);
            }
          } catch (\Throwable $th) {
            //no se proceso la consulta
            $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al insertar el articulo: '.$th];
            echo json_encode($res);
          }
        }else{
          //no cuenta con el inventario suficiente
          $res = ['status'=>'error','mensaje'=>'No cuentas con el inventario suficiente para registrar el articulo.'];
          echo json_encode($res);
        }
      }else{
        //no tiene inventario
        $res = ['status'=>'error','mensaje'=>'No se cuenta con inventario en la sucursal.'];
        echo json_encode($res);
      }

      

    }elseif(!empty($_POST['terminaTrabajo'])){
      $idTrabajo = $_POST['terminaTrabajo'];
      $precioFinal = $_POST['precioFinalTer'];
      $fecha = date('Y-m-d');
      $hora = date('H:i:s');

      $sql = "UPDATE TRABAJOS SET estatusTrabajo = 'Finalizado', fechaTermino = '$fecha', 
      horaTermino = '$hora', usuarioTermino = '$usuario', costoFinal = '$precioFinal' WHERE idTrabajo = '$idTrabajo'";
      try {
        $query = mysqli_query($conexion, $sql);
        //podemos dar por terminado
        $res = ['status'=>'ok','mensaje'=>'operationComplete'];
        echo json_encode($res);
      } catch (\Throwable $th) {
        $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al actualizar el trabajo: '.$th];
        echo json_encode($res);
      }
    }elseif(!empty($_POST['estatusBusqueda'])){
      //seccion para buscar trabajos por el estatus y nombre
      $estatus = $_POST['estatusBusqueda'];
      $cliente = $_POST['nombreCli'];

      // $sql = "SELECT * FROM TRABAJOS WHERE estatusTrabajo = '$estatus' AND empresaID = '$idEmpresaSesion' 
      // AND sucursalID = '$idSucursalN'";
      if(empty($cliente)){
        $sql = "SELECT * FROM TRABAJOS a INNER JOIN CLIENTES b ON a.clienteID = b.idClientes 
        INNER JOIN SERVICIOS c ON a.servicioID = c.idServicio WHERE 
        a.empresaID = '$idEmpresaSesion' AND a.sucursalID = '$idSucursalN' AND a.estatusTrabajo = '$estatus'";
      }else{
        $sql = "SELECT * FROM TRABAJOS a INNER JOIN CLIENTES b ON a.clienteID = b.idClientes 
        INNER JOIN SERVICIOS c ON a.servicioID = c.idServicio WHERE 
        a.empresaID = '$idEmpresaSesion' AND a.sucursalID = '$idSucursalN' AND a.estatusTrabajo = '$estatus' 
        AND b.nombreCliente LIKE '%$cliente%'";
      }
      
      try {
        $query = mysqli_query($conexion, $sql);
        if(mysqli_num_rows($query)> 0){
          $datos = [];
          $x = 0;
          while($fetch = mysqli_fetch_assoc($query)){
            $datos[$x] = $fetch;
            $x++;
          }//fin del while
          $res = ['status'=>'ok','data'=>$datos,'mensaje'=>'dataOk'];
          echo json_encode($res);
        }else{
          //sin resultados
          $res = ['status'=>'ok','mensaje'=>'noData'];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar los trabajos: '.$th];
        echo json_encode($res);
      }
    }elseif(!empty($_POST['buscarByCliente'])){
      $nombreCliente = $_POST['buscarByCliente'];
      $estatusTra = $_POST['estatusTraCli'];

      if(empty($estatusTra)){
        $sql = "SELECT * FROM TRABAJOS a INNER JOIN CLIENTES b ON a.clienteID = b.idClientes 
        INNER JOIN SERVICIOS c ON a.servicioID = c.idServicio WHERE 
        a.empresaID = '$idEmpresaSesion' AND a.sucursalID = '$idSucursalN' AND  b.nombreCliente LIKE '%$nombreCliente%'";
      }else{
        $sql = "SELECT * FROM TRABAJOS a INNER JOIN CLIENTES b ON a.clienteID = b.idClientes 
        INNER JOIN SERVICIOS c ON a.servicioID = c.idServicio WHERE 
        a.empresaID = '$idEmpresaSesion' AND a.sucursalID = '$idSucursalN' AND a.estatusTrabajo = '$estatusTra' 
        AND b.nombreCliente LIKE '%$nombreCliente%'";
      }
      try {
        $query = mysqli_query($conexion, $sql);
        if(mysqli_num_rows($query)> 0){
          $datos = [];
          $x = 0;
          while($fetch = mysqli_fetch_assoc($query)){
            $datos[$x] = $fetch;
            $x++;
          }//fin del while
          $res = ['status'=>'ok','data'=>$datos,'mensaje'=>'dataOk'];
          echo json_encode($res);
        }else{
          //sin resultados
          $res = ['status'=>'ok','mensaje'=>'noData'];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar los trabajos: '.$th];
        echo json_encode($res);
      }
    }
  }
?>