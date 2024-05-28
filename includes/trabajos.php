<?php 

function altaTrabajo($cliente,$fechAlta, $tipoDispo,$tipoServ, $marca,$modeloServicio,
$serieDispo,$accesorios,$problema,$observacion,$contraDispo,$fechaEntrega,$costoServ,$anticipo,
$usuario,$sucursal,$empresa,$idUsuario){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  //antes de insertar el trabajo, devemos consultar el consecutivo
  $sql = "SELECT COUNT(*) AS numServicios FROM TRABAJOS WHERE empresaID = '$empresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    $fetch = mysqli_fetch_assoc($query);

    $numServ = $fetch['numServicios'];
    $numServ = $numServ+1;
    $fechaActual = date('Y-m-d');
    $horaActual = date('H:i:s');

    //ahora si insertamos el trabajo
    $sql2 = "INSERT INTO TRABAJOS (numTrabajo,estatusTrabajo,fechaTrabajo,fechaRegistro,horaRegistro,clienteID,
    sucursalID,usuarioID,empresaID,tipoDispositivo,servicioID,marca,modelo,imeiClave,
    accesorios,problema,observaciones,contraDispo,fechaEntrega,costoInicial,anticipo) 
    VALUES ('$numServ','En Espera','$fechAlta','$fechaActual','$horaActual','$cliente','$sucursal','$idUsuario','$empresa','$tipoDispo',
    '$tipoServ','$marca','$modeloServicio','$serieDispo','$accesorios','$problema','$observacion',
    '$contraDispo','$fechaEntrega','$costoServ','$anticipo')";
    try {
      $query2 = mysqli_query($conexion, $sql2);
      //hasta este punto se da por terminao y completado
      $idTrabajo = mysqli_insert_id($conexion);
      $res = ['status'=>'ok','mensaje'=>'operationComplete','data'=>$idTrabajo];
      return json_encode($res);
    } catch (\Throwable $th) {
      $res = ['status'=>'error','mensaje'=>'Ocurrio un error al insertar el trabajo: '.$th];
      return json_encode($res);
    }
  }catch (\Throwable $th) {
    $res = ['status'=>'error','mensaje'=>'Ocurrio un error al asignar el numero de trabajo: '.$th];
    return json_encode($res);
  }

}


function getTrabajo($idTrabajo,$idEmpresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT * FROM TRABAJOS WHERE idTrabajo = '$idTrabajo' AND empresaID = '$idEmpresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_row($query) == 1){
      //si existe el trabajo
      
    }else{
      //trabajo no localizado
    }
  } catch (\Throwable $th) {
    //throw $th;
  }
}
?>