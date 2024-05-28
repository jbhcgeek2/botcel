<?php 
//cargamos la esion
session_start();

if(!empty($_SESSION['usuarioPOS'])){
  include("usuarios.php");
  include("conexion.php");
  include("empresas.php");
  
  $usuario = $_SESSION['usuarioPOS'];
  //verificamos que tengamos los datos para actulizar el usuario
  if(!empty($_POST['usuarioMod'])){

    $idUsuarioMod = $_POST['usuarioMod'];
    $nombre = $_POST['nombreUser'];
    $paterno = $_POST['apPaterno'];
    $materno = $_POST['apMaterno'];
    $telUser = $_POST['telUser'];
    $mailUser = $_POST['mailUser'];
    $userName = $_POST['userName'];
    $pass = $_POST['passwordUser'];
    $sucUser = $_POST['scUsuario'];
    $tipoUser = $_POST['tipoUser'];
    $statusUser = $_POST['estatusUser'];

    $sql = "UPDATE USUARIOS SET nombreUsuario = '$nombre', apPaternoUsuario = '$paterno',
    apMaternoUsuario = '$materno', telUsuario = '$telUser', correoUsuario = '$mailUser', 
    passwordUser = '$pass', sucursalID = '$sucUser', statusUsuario = '$statusUser', rolID = '$tipoUser' 
    WHERE idUsuario = '$idUsuarioMod'";
    try {
      $query = mysqli_query($conexion, $sql);
      //podemos dar por completada la sentencia
      $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
      echo json_encode($res);
      
    } catch (\Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar la informacion: ".$th];
      echo json_encode($res);
    }
  }
}

?>