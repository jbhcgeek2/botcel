<?php
session_start();


if(!empty($_SESSION['usuarioPOS'])){
  require("usuarios.php");
  require("empresas.php");
  require("articulos.php");
  require("conexion.php");
  require("documentos.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;

  if(!empty($_POST['nombreUser'])){
    //seccion para registrar a un usuario nuevo
    $nombre = $_POST['nombreUser'];
    $paterno = $_POST['apPaterno'];
    $materno = $_POST['apMaterno'];
    $tel = $_POST['telUser'];
    $correo = $_POST['mailUser'];
    $nombreUsuario = $_POST['userName'];
    $contra = $_POST['passwordUser'];
    $sucUsuario = $_POST['scUsuario'];
    $tipoUsuario = $_POST['tipoUser'];

    //antes de continuar, verificamos el numero de usuario de la empresa
    //para ver si aun cuenta con disponibilidad
    $numUs = getNumUsers($idEmpresaSesion);
    $numUs = json_decode($numUs);
    if($numUs->status == "ok"){
      $totUs = $numUs->mensaje;

      if($totUs == "continua"){
        //podemos registrar el usuario
        //verificamos si el usuario ya existe
        $sqlUs = "SELECT * FROM USUARIOS WHERE userName = '$nombreUsuario' AND empresaID = '$idEmpresaSesion'";
        try {
          $queryUs = mysqli_query($conexion, $sqlUs);
          if(mysqli_num_rows($queryUs) == 0){
            //podemos continuar
            $altaUs = nuevoUsuario($idEmpresaSesion,$nombre,$paterno,$materno,$tel,$correo,
            $nombreUsuario,$contra,$sucUsuario,$tipoUsuario);
            $altaUs = json_decode($altaUs);
            if($altaUs->status == "ok" && $altaUs->dato == "operationSuccess"){
              $res = ["status"=>"ok","data"=>"operationSuccess"];
              echo json_encode($res);
            }else{
              //ocurrio un error al insertar el usuario
              $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error desconocido al insertar el usuario."];
              echo json_encode($res);
            }
          }else{
            //el usuario ya existe
            $res = ["status"=>"error","mensaje"=>"userExist"];
            echo json_encode($res);
          }
        } catch (\Throwable $th) {
          //throw $th;
          $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar existencia de usuarios. ".$th];
          echo json_encode($res);
        }
        
      }else{
        //ya no tiene espacio
        $res = ["status"=>"ok","mensaje"=>"planInsuficiente"];
        echo json_encode($res);
      }
    }else{
      //error al consultar el datos del los usuario
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar la empresa."];
      echo json_encode($res);
    }
  }
}
?>