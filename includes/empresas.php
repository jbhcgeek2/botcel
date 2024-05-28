<?php
//funciones para empresas


function verSucursales($usuario,$empresa){
  require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }
  
  //verificamos que dato nos estan mandadno para obtener las sucursales
  if(!empty($usuario)){
    //consultamos por usuario
    $sql = "SELECT empresaID FROM USUARIOS WHERE userName = '$usuario'";
    try {
      $query = mysqli_query($conexion, $sql);
      $fetch = mysqli_fetch_assoc($query);
      $idEmpresa = $fetch['empresaID'];

      $sql2 = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresa'";
      try {
        $query2 = mysqli_query($conexion, $sql2);
        $data = [];
        $i = 0;
        while($fetch2 = mysqli_fetch_assoc($query2)){
          $data[$i] = $fetch2;
          $i++;
        }//fin del while
        $res = ["estatus"=>"ok","dato"=>$data];
        return json_encode($res);
      } catch (\Throwable $th) {
        //error al consultar las sucursales
        $res = ["status"=>"error","mensaje"=>"Error al consultar las sucursales."];
        return json_encode($res);
      }
    } catch (\Throwable $th) {
      //error en la consulta del id empresa
      $res = ["status"=>"error","mensaje"=>"Error al consultar la empresa."];
      return json_encode($res);
    }
  }else{
    $sql3 = "SELECT * FROM SUCURSALES WHERE empresaSucId = '$empresa'";
    try {
      $query = mysqli_query($conexion, $sql3);
      $data =[];
      $i = 0;
      while($fetch3 = mysqli_fetch_assoc($query3)){
        $data[$i] = $fetch3;
        $i++;
      }
      $res = ["estatus"=>"ok","dato"=>$data];
      return json_encode($res);
    } catch (\Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"Error al consultar la empresa 2."];
      return json_encode($res);
    }
  }

}//fin funcion verSucursales

function updateCapital($idEmpresa,$montoAfecta,$tipoMov,$idUsuario,$conceptoMov){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  //primero consultamos el capital actual para sumarlo
  $sql = "SELECT * FROM EMPRESAS WHERE idEmpresa = '$idEmpresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      $fetch = mysqli_fetch_assoc($query);
      $saldoAnterior = $fetch['saldoEfectivo'];
      if($tipoMov == "Salida"){
        $nuevoCap2 = $saldoAnterior - $montoAfecta;
      }
      
      $sql2 = "UPDATE EMPRESAS SET saldoEfectivo = '$nuevoCap2', saldoEfeAnterior = '$saldoAnterior' 
      WHERE idEmpresa = '$idEmpresa'";
      try {
        $query2 = mysqli_query($conexion, $sql2);
        //se actualizo correctamente
        $res = ["status"=>"ok","mensaje"=>'operationSuccess'];
        return json_encode($res);
      } catch (\Throwable $th) {
        //throw $th;
        $res = ["status"=>"error","mensaje"=>'Ha ocurrido un error al actuaslizar los saldos: '.$th];
        return json_encode($res);
      }
    }else{
      //empresa no localizada
      $res = ["status"=>"error","mensaje"=>'La empresa indicada no existe'];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    //throw $th;
    $res = ["status"=>"error","mensaje"=>'Ha ocurrido un error al localizar la empresa: '.$th];
    return json_encode($res);
  }
  

}

function getNumUsers($idEmpresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  //buscamos los usuarios y el plan de la empresa
  $sql = "SELECT *,(SELECT COUNT(*) FROM USUARIOS c WHERE c.empresaID = a.idEmpresa AND c.statusUsuario = '1') 
  AS numUsers FROM EMPRESAS a INNER JOIN SUSCRIPCION b ON 
  a.suscripcionID = b.idSuscripcion WHERE a.idEmpresa = '$idEmpresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    $fetch = mysqli_fetch_assoc($query);
    $numUsers = $fetch['numUsers'];
    $maxUsers = $fetch['maxUsers'];
    if($maxUsers == "0"){
      //tiene plan ilimitado y si podra registrrar
      $mensaje = "continua";
    }else{
      if($numUsers < $maxUsers){
        //si puede registrar
        $mensaje = "continua";
      }else{
        //ya no puede registrar, llego al limite
        $mensaje = "full";
      }
    }
    $res = ["status"=>"ok","data"=>$numUsers,"mensaje"=>$mensaje];
    return json_encode($res);
  } catch (\Throwable $th) {
    //error de consulta
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar la empresa"];
    return json_encode($res);
  }
}


?>