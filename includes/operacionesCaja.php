<?php 

function existeApertura($usuario,$sucursal,$empresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  //tendremos que buscar la apertura del dia,
  $fecha = date('Y-m-d');
  
  $sql = "SELECT * FROM MOVCAJAS WHERE usuarioMov = '$usuario' AND conceptoMov = '1' 
  AND sucursalMovID = '$sucursal' AND empresaMovID = '$empresa' AND fechaMovimiento = '$fecha'";

  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      $res = ["status"=>"ok","mensaje"=>"DataOk"];
      return json_encode($res);
    }else{
      //no se detecto inicio del dia
      $res = ["status"=>"ok","mensaje"=>"noData"];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    //error de consulta de datos
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar los movimientos de caja. ".$th];
    return json_encode($res);
  }
}

function setApertura($fecha,$monto,$usuario,$sucural,$empresa,$concepto,$observ){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $hora = date('H:i:s');

  //antes de insertar consultamos si existe una apertura del dia ya registrada
  $sqlX = "SELECT * FROM MOVCAJAS WHERE fechaMovimiento = '$fecha' AND usuarioMov = '$usuario' AND 
  SucursalMovID = '$sucural' AND conceptoMov = '1'";
  try {
    $queryX = mysqli_query($conexion, $sqlX);
    if(mysqli_num_rows($queryX) == 0){
      //ya existe una apertura del usuario en la sucursal
      $sql = "INSERT INTO MOVCAJAS (fechaMovimiento,horaMovimiento,usuarioMov,montoMov,
      conceptoMov,observacionMov,sucursalMovID,tipoMov,empresaMovID) VALUES 
      ('$fecha','$hora','$usuario','$monto','$concepto','$observ','$sucural',
      'E','$empresa')";
      try {
        $query = mysqli_query($conexion, $sql);

        $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
        return json_encode($res);
      } catch (\Throwable $th) {
        //no se inserto la apertura
        $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al guardar la apertura."];
        return json_encode($res);
      }
    }else{
      $res = ["status"=>"ok","mensaje"=>"dataExist"];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    //throw $th;
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar la informacion. ".$th];
    return json_encode($res);
  }

  
}

function guardaMovCaja($monto,$fecha,$hora,$cajero,$concepto,$observ,$sucursal,$tipo,$empresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "INSERT INTO MOVCAJAS (fechaMovimiento,horaMovimiento,usuarioMov,montoMov,
  conceptoMov,observacionMov,sucursalMovID,tipoMov,empresaMovID) VALUES ('$fecha',
  '$hora','$cajero','$monto','$concepto','$observ','$sucursal','$tipo','$empresa')";
  try {
    $query = mysqli_query($conexion, $sql);
    //se completo el registro
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (\Throwable $th) {
    //throw $th;
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al guardar el movimiento. ".$th];
    return json_encode($res);
  }
}

function sumaSaldo($nuevoSaldo,$saldoAnterior,$idEmpresa,$tipoCampo){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $nombreCampo = "";
  $nombreCampo2 = "";
  if($tipoCampo == "saldoEfectivo"){
    $nombreCampo = "saldoEfectivo";
    $nombreCampo2 = "saldoEfeAnterior";
  }else{
    $nombreCampo = "saldoTransferencia";
    $nombreCampo2 = "saldoTransAnterior";
  }

  $sql = "UPDATE EMPRESAS SET $nombreCampo = '$nuevoSaldo', $nombreCampo2 = '$saldoAnterior' 
  WHERE idEmpresa = '$idEmpresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    //se termino el proceso
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (\Throwable $th) {
    //throw $th;
    $res = ["status"=>"error","mensaje"=>"No fue posible actualizar el monto. ".$th];
    return json_encode($res);
  }

}

function existeCierre($fecha,$usuario,$sucursal){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT * FROM MOVCAJAS WHERE fechaMovimiento = '$fecha' AND usuarioMov = '$usuario' AND 
  conceptoMov = '4' AND sucursalMovID = '$sucursal'";
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      //ya existe el cierre
      $res = ["status"=>"ok","mensaje"=>"cierreExiste"];
      return json_encode($res);
    }else{
      //no existe el cierre
      $res = ["status"=>"ok","mensaje"=>"noData"];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    //error en la consulta del cierre
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error en la consulta de movimientos. ".$th];
    return json_encode($res);
  }
}
?>