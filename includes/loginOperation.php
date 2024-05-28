<?php
session_start();

//si ya existe una sesion, la persona no tiene nada que hacer aca
if(empty($_SESSION['usuarioPOS'])){ 
  //agregamos las conexiones
  include("conexion.php");

  //verificamos la informacion recibida
  if(!empty($_POST['mail']) && !empty($_POST['pass'])){
    $mail = htmlentities($_POST['mail']);
    $pass = htmlentities($_POST['pass']);

    //verificamos en la base de datos
    $sql = "SELECT * FROM USUARIOS WHERE userName = '$mail' AND passwordUser = '$pass'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) == 1){
        //verificamos 
        $fetch = mysqli_fetch_assoc($query);
        if($mail == $fetch['userName'] && $pass == $fetch['passwordUser']){
          //ahora si podemos iniciar la sesion
          $_SESSION['usuarioPOS'] = $fetch['userName'];
          $res = ["status"=>"ok","mensaje"=>"inicio de sesion correcto"];
          echo json_encode($res);
        }else{
          //algo no coincidio
          $res = ["status"=>"error","mensaje"=>"Correo y/o contrasena incorrectos"];
          echo json_encode($res);
        }
      }else{
        //usuario o contrasena incorrectos
        $res = ["status"=>"error","mensaje"=>"Correo y/o contrasena incorrectos"];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //error en la base de datos
      $res = ["status"=>"error","mensaje"=>"Ocurrio un error al realizar la consulta a la base de datos"];
      echo json_encode($res);
    }

  }else{
    //sin informacion para validar
      $res = ["status"=>"error","mensaje"=>"No se cuenta con informacion para procesar."];
      echo json_encode($res);

  }
}else{
  //nada de nada
  $res = ["status"=>"error","mensaje"=>"No se cuenta con informacion para procesar."];
  echo json_encode($res);

}
?>