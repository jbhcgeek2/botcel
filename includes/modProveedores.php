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

  if(!empty($_POST['nombreProvAlta'])){
    //seccion para registrar un proveedor
    $nombreProv = htmlentities($_POST['nombreProvAlta']);
    $telProvAlta = $_POST['telProvAlta'];
    $mailProvAlta = htmlentities($_POST['mailProvAlta']);
    $direccionProv = htmlentities($_POST['direProvAlta']);
    $fecha = date('Y-m-d');

    //verificamos si los datros del proveddor ya estan registrados
    $sql1 = "SELECT * FROM PROVEEDORES WHERE (telProveedor = '$telProvAlta' OR mailProveedor = '$mailProvAlta') 
    AND provEmpresaID = '$idEmpresaSesion'";
    try {
      $query1 = mysqli_query($conexion, $sql1);
      if(mysqli_num_rows($query1) == 0){
        //procedemos a registrrar el proveedor
        $sql2 = "INSERT INTO PROVEEDORES (nombreProveedor,telProveedor,mailProveedor,direccionProv,
        fechaAltaProv,provEmpresaID,estatusProveedor) VALUES ('$nombreProv','$telProvAlta','$mailProvAlta',
        '$direccionProv','$fecha','$idEmpresaSesion','1')";
        try {
          $query2 = mysqli_query($conexion,$sql2);
          //hasta aqui podemos dar por terminado el proceso de manera correcta
          $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
          echo json_encode($res);
        } catch (\Throwable $th) {
          //error al guardar el proveedor
          $res = ["status"=>"error","mensaje"=>"Ocurrio un error al guardar los datos del proveedor. ".$th];
          echo json_encode($res);
        }
      }else{
        //ya se tiene un proveedor con esos datos
        $res = ["status"=>"error","mensaje"=>"Los datos ingresados ya pertenecen a un proveedor, verificalo."];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //throw $th;
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar datos de proveedores. ".$th];
      echo json_encode($res);
    }
    
  }elseif(!empty($_POST['dataProvEdit'])){
    //seccio para actualizar los datos de un proveedor
    $idProv = $_POST['dataProvEdit'];
    $nombreProv = htmlentities($_POST['nombreProvEdit']);
    $telprov = $_POST['telProvEdit'];
    $mailProv = htmlentities($_POST['mailProvEdit']);
    $statusProv = $_POST['estatusProvEdit'];
    $dirProv = htmlentities($_POST['dirProvEdit']);

    $sql = "SELECT * FROM PROVEEDORES WHERE (telProveedor = '$telprov' OR mailProveedor = '$mailProv') AND 
    provEmpresaID = '$idEmpresaSesion'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) == 0){
        //sin coincidencias actualizamos
        $sql2 = "UPDATE PROVEEDORES SET nombreProveedor = '$nombreProv', telProveedor = '$telprov', 
        mailProveedor = '$mailProv', direccionProv = '$dirProv', estatusProveedor = '$statusProv' 
        WHERE idProveedor = '$idProv' AND provEmpresaID = '$idEmpresaSesion'";
        try {
          $query2 = mysqli_query($conexion, $sql2);
          $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
          echo json_encode($res);
        } catch (\Throwable $th) {
          $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar el proveedor. ".$th];
          echo json_encode($res);
        }
      }else{
        $fetch = mysqli_fetch_assoc($query);
        if($idProv == $fetch['idProveedor']){
          //se tratav del mismo registro, asi que lo actualizamos
          $sql3 = "UPDATE PROVEEDORES SET nombreProveedor = '$nombreProv', telProveedor = '$telprov', 
          mailProveedor = '$mailProv', direccionProv = '$dirProv', estatusProveedor = '$statusProv' 
          WHERE idProveedor = '$idProv' AND provEmpresaID = '$idEmpresaSesion'";
          try {
            $query3 = mysqli_query($conexion, $sql3);
            $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
            echo json_encode($res);
          } catch (\Throwable $th) {
            $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar la informacion edl proveedor. ".$th];
            echo json_encode($res);
          }
        }else{
          //ya existen el correo y el email
          $res = ["status"=>"error","mensaje"=>"Los datos del proveedor ya existen, verifiquelo."];
          echo json_encode($res);
        }
      }
    } catch (\Throwable $th) {
      //throw $th;
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar datos de proveedores. ".$th];
      echo json_encode($res);
    }
  }

}
?>