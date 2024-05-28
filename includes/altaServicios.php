<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){

  include('conexion.php');
  include('usuarios.php');

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;

  if(!empty($_POST['nombreServ'])){
    $nombreServ = $_POST['nombreServ'];
    $tipoPrecio = $_POST['precioFijo'];
    $precioServ = $_POST['precioServ'];
    $categoriaServ = $_POST['catServicio'];
    if($tipoPrecio == "1"){
      $precioServ = $_POST['precioServ'];
    }else{
      $precioServ = "0";
    }

    $sql = "INSERT INTO SERVICIOS (nombreServicio,categoriaServicio,estatusCategoria,
    precioServicio,precioFijo,empresaID) VALUES ('$nombreServ','$categoriaServ',
    '1','$precioServ','$tipoPrecio','$idEmpresaSesion')";
    try {
      $query = mysqli_query($conexion, $sql);
      //se inserto correctamente
      $res = ["status"=>"ok","mensaje"=>"operationComplete"];
      echo json_encode($res);
    } catch (\Throwable $th) {
      //error al insertar
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al guardar el servicio: ".$th];
      echo json_encode($res);
    }
  }

}else{

}
?>