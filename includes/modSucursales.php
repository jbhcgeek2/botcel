<?php 
session_start();

if(!empty($_SESSION['usuarioPOS'])){
  //insertamos los archivos que necesitamos
  include("articulos.php");
  include("usuarios.php");
  include("documentos.php");
  include("conexion.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;


  if(!empty($_POST['dataSuc'])){
    $idSuc = $_POST['dataSuc'];
    $nombreSuc = htmlentities($_POST['nameSucursal']);
    $domicilio = htmlentities($_POST['domSuc']);
    $tel = htmlentities($_POST['telSuc']);
    $statusSuc = $_POST['estatusSuc'];

    if(is_numeric($idSuc)){
      $sql = "UPDATE SUCURSALES SET nombreSuc = '$nombreSuc', calleSuc = '$domicilio', telefonoSuc = '$tel',
      estatusSuc = '$statusSuc' WHERE idSucursal = '$idSuc'";
      try {
        $query = mysqli_query($conexion, $sql);
        //hasta este punto podemos dar por terminaod el proceso de actualizar
        $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
        echo json_encode($res);
      } catch (\Throwable $th) {
        //throw $th;
        $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar la sucursal. ".$th];
        echo json_encode($res);
      }
    }else{
      //alv de aqui
    }
  }elseif(!empty($_POST['nombreAltaSuc'])){
    $nombreSuc = htmlentities($_POST['nombreAltaSuc']);
    $domSuc =htmlentities($_POST['domAltaSuc']);
    $telSuc = $_POST['telAltaSuc'];
    $statusSuc = $_POST['statusAltaSuc'];

    $sql = "INSERT INTO SUCURSALES (nombreSuc,calleSuc,telefonoSuc,estatusSuc,empresaSucID) 
    VALUES ('$nombreSuc','$domSuc','$telSuc','$statusSuc','$idEmpresaSesion')";
    try {
      $query = mysqli_query($conexion, $sql);
      //se inserto la sucursal
      $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
      echo json_encode($res);
    } catch (\Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al insertar la sucursal. ".$th];
      echo json_encode($res);
    }
  }
}