<?php 


if(!empty($_POST['contra1'])){
  include("conexion.php");
  include("usuarios.php");

  //seccion para validar y registrar a un usuario nuevo
  $nCamposMal = 0;
  $campos = ['nombreEmpresa','userName','nombreUser','contra1','contra2','suscripcion','emailUser'];
  

  for($x = 0; $x < count($campos); $x++){
    //seccion para validar los campos existentes
    if(empty($_POST[$campos[$x]])){
      //campo vacio
      $nCamposMal = $nCamposMal+1;
    }
  }
  if($nCamposMal == 0){
    $userName = $_POST['userName'];
    //verificamos que el usuario no exista
    $validaUser = json_decode(usuarioExiste($userName));
    
    if($validaUser->dato == "usuarioNoExiste"){
      //podemos continuar con el registro
      //verificamos que la empresa no exista
      $nombreEmpresa = trim($_POST['nombreEmpresa']);
      $nombreEmpresa = htmlentities($nombreEmpresa);
      $sql = "SELECT nombreEmpresa FROM EMPRESAS WHERE nombreEmpresa = '$nombreEmpresa'";
      try {
        $query = mysqli_query($conexion, $sql);
        if(mysqli_num_rows($query) == 0){
          //la empresa no existe, podemos continuar
          //primero registrartemos a la empresa, y continuaremos con el usuario
          //el cual sera el administrador
          $nombres = htmlentities($_POST['nombreUser']);
          $apPaterno = htmlentities($_POST['apPaterno']);
          $apMaterno = htmlentities($_POST['apMaterno']);
          $contra1 = htmlentities($_POST['contra1']);
          $contra2 = htmlentities($_POST['contra2']);
          $plan = $_POST['suscripcion'];
          $correo = htmlentities($_POST['emailUser']);
          //antes de proceder verificamos que las contrasenas coincidan
          if($contra1 == $contra2){
            $sql2 = "INSERT INTO EMPRESAS (nombreEmpresa,suscripcionID) VALUES ('$nombreEmpresa','$plan')";
            try {
              $query2 = mysqli_query($conexion, $sql2);
              $idEmpresa = mysqli_insert_id($conexion);
              //generamos una sucursal automaticamente como matriz
              $sql3 = "INSERT INTO SUCURSALES (nombreSuc,calleSuc,telefonoSuc,empresaSucID) 
              VALUES ('Principal',' ',' ',$idEmpresa)";
              try {
                $query3 = mysqli_query($conexion, $sql3);
                //procedemos a insertar el usuario y lo asosiamos con la empresa
                $idSuc = mysqli_insert_id($conexion);
                $nuevoUser = nuevoUsuario($idEmpresa,$nombres,$apPaterno,$apMaterno,'',$correo,$userName,$contra1,$idSuc,'1');

                $nuevoUser2 = json_decode($nuevoUser);
                if($nuevoUser2->dato == "operationSuccess"){
                  //se inserto el usuario, mandamos el correo de validacion
                  $res = ["estatus"=>"ok","mensaje"=>"Se ha registrado la empresa."];
                  echo json_encode($res);
                }else{
                  //ocurrio un error en el proceso
                  $res = ["estatus"=>"error","mensaje"=>"Ha ocurrido un error falta, contacte a soporte tecnico. ".$nuevoUser];
                  echo json_encode($res);
                }
              } catch (\Throwable $th) {
                //error al insertar la sucursal
              }
              
            } catch (Throwable $th) {
              //error al insertar la empresa
              $res = ["estatus"=>"error","mensaje"=>"Ha ocurrido un error al guardar la empresa. ".$th];
              echo json_encode($res);
            }
          }else{
            //contrasenas no coinciden
            $res = ["estatus"=>"error","mensaje"=>"Las contrasenas no coinciden, verificalo"];
            echo json_encode($res);
          }
        }else{
          //la empresa ya existe, le desimos que nel pastel
          $res = ["estatus"=>"error","mensaje"=>"La empresa indicada ya existe, ingrese otro nombre."];
          echo json_encode($res);    
        }
      } catch (Throwable $th) {
        //error al consultar las empresas
        $res = ["estatus"=>"error","mensaje"=>"Ha ocurrido un error al consultar la base de datos."];
        echo json_encode($res);    
      }
    }else{
      $res = ["estatus"=>"error","mensaje"=>"El usuario indicado ya existe, ingrese otro nombre"];
      echo json_encode($res);
    }
  }else{
    //se detectaron campos invalidos
    $res = ["estatus"=>"error","mensaje"=>"Se han detectado que algunos campos no estan capturados."];
    echo json_encode($res);
  }
  
}

?>