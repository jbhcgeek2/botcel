<?php 

function verClientes($idEmpresa){
  require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }

    $sql = "SELECT * FROM CLIENTES WHERE clienteEmpresaID = '$idEmpresa'";
    try {
      $query = mysqli_query($conexion, $sql);
      $data = [];
      $i = 0;
      while($fetch = mysqli_fetch_assoc($query)){
        $data[$i] = $fetch;
        $i++;
      }//fin del while
      
      $res = ["status"=>"ok","data"=>$data];
      return json_encode($res);
    } catch (\Throwable $th) {
      //error al consultare los clientes
      $res =["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar los clientes: ".$th];
      return json_encode($res);
    }

}

function altaCliente($nombre,$tel,$mail,$dir,$rfc,$idEmpresa){
  require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }
    $fecha = date('Y-m-d');
    $van = 0;
    $sql = "INSERT INTO CLIENTES (nombreCliente,direccionCliente,telefonoCliente,emailCliente,rfcCliente,
    fechaAlta,clienteEmpresaID) VALUES ('$nombre','$dir','$tel','$mail','$rfc','$fecha','$idEmpresa')";

    try {
      $query = mysqli_query($conexion, $sql);
      $res = ['status'=>'ok','mensaje'=>'operationSuccess'];
      return json_encode($res);
    } catch (\Throwable $th) {
      $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al insertar el cliente'];
      return json_encode($res);
    }
}

function verCliente($idCliente,$idEmpresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  if($idCliente == 1){
    //publico en general
    $sql = "SELECT * FROM CLIENTES WHERE idClientes = '$idCliente'";
  }else{
    $sql = "SELECT * FROM CLIENTES WHERE idClientes = '$idCliente' AND clienteEmpresaID = '$idEmpresa'";
  }
  
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      //si existe el cliente en la empresa
      $fetch = mysqli_fetch_assoc($query);
      $res = ["status"=>"ok","data"=>$fetch];
      return json_encode($res);
    }else{
      $res = ["status"=>"error","mensaje"=>"No fue posible localizar el cliente."];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    //error al consultar el cliente
    $res = ["status"=>"error","mensaje"=>"Ocurrio error al consultar el cliente: ".$th];
    return json_encode($res);

  }
}

function updateCliente($nombre,$direccion,$tel,$mail,$rfc,$idCliente){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  //actualizaremos los datos del cliente
  $sql = "UPDATE CLIENTES SET nombreCliente = '$nombre', direccionCliente = '$direccion', telefonoCliente = '$tel',
  emailCliente = '$mail', rfcCliente = '$rfc' WHERE idClientes = '$idCliente'";
  try {
    $query = mysqli_query($conexion, $sql);
    //se actualizo correctamente
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (\Throwable $th) {
    //throw $th;
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar el cliente: ".$th];
    return json_encode($res);
  }
}

function buscarCliente($dato,$empresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT * FROM CLIENTES WHERE (nombreCliente LIKE '%$dato%' OR telefonoCliente LIKE '%$dato%') 
  AND clienteEmpresaID = '$empresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    $data = [];
    $i = 0;
    while($fetch = mysqli_fetch_assoc($query)){
      $data[$i] = $fetch;
      $i++;
    }//fin del while
    $res = ["status"=>"ok","data"=>$data];
    return json_encode($res);
  } catch (\Throwable $th) {
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar el cliente: ".$th];
    return json_encode($res);
  }

}
?>