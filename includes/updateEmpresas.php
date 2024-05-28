<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){
  include('conexion.php');
  include("usuarios.php");
  include("documentos.php");


  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;

  if(!empty($_POST['nombreEmpresa'])){
    $nombreEmpresa = $_POST['nombreEmpresa'];
    $suscripcion = $_POST['planEmpresa'];

    //antes de continuar, verificamos si cambio de plan
    $sqlEmp = "SELECT * FROM EMPRESAS WHERE idEmpresa = '$idEmpresaSesion'";
    try {
      $queryEmp = mysqli_query($conexion, $sqlEmp);
      if(mysqli_num_rows($queryEmp) == 1){
        $fetchEmp = mysqli_fetch_assoc($queryEmp);
        $susActual = $fetchEmp['suscripcionID'];
        $sqlUpdate = "";
        if($suscripcion != $susActual){
          //se actualizara el plan
          $sqlUpdate = "UPDATE EMPRESAS SET nombreEmpresa = '$nombreEmpresa', 
          nuevaSuscripcion = '$suscripcion', estatusNuevaSus = 'Espera' 
          WHERE idEmpresa = '$idEmpresaSesion'";
        }else{
          //no se actualiza el plan
          $sqlUpdate = "UPDATE EMPRESAS SET nombreEmpresa = '$nombreEmpresa' 
          WHERE idEmpresa = '$idEmpresaSesion'";
        }
        //aplicamos el update
        try {
          $queryUpdate = mysqli_query($conexion, $sqlUpdate);
          //ahora verificamos si se actualiza el logotipo
          //verificamos si se subio imagen para procesarla
          if(!empty($_FILES['logotipo']['tmp_name'])){
            //verificamos el tipo de imagen
            $tmpFile = $_FILES['logotipo'];
            $nombreFile = "logotipo";
            $ruta = "../imgEmpr";
            $subir = uploadDoc($tmpFile,'imagen',$nombreFile,$ruta,$idEmpresaSesion);
            $subir = json_decode($subir);
            if($subir->estatus == "ok"){
              //ahora actualizamos el campo de la foto
              $rutaCompleta = $subir->dato;
              $sqlUpdate2 = "UPDATE EMPRESAS SET imgLogoEmpresa = '$rutaCompleta' WHERE 
              idEmpresa = '$idEmpresaSesion'";
              try {
                $queryUpdate2 = mysqli_query($conexion, $sqlUpdate2);
                //se aplico correctamente
                $res = ["status"=>"ok","mensaje"=>"operationComplete"];
                echo json_encode($res);
              } catch (\Throwable $th) {
                //error al actualizar el logo
                $res = ["status"=>"error","mensaje"=>"No fue posible actualizar el logotipo: ".$th];
                echo json_encode($res);
              }
              // $res = ["status"=>"ok","mensaje"=>"operationComplete"];
              // echo json_encode($res);
            }else{
              //error al subir la imagen
              $err = $subir->mensaje;
              $res = ["status"=>"error","mensaje"=>$err];
              echo json_encode($res);
            }
          }else{
            //no tiene logo terminamos el proceso
            $res = ["status"=>"ok","mensaje"=>"operationComplete"];
            echo json_encode($res);
          }
          
        } catch (\Throwable $th) {
          //error al actualizar 
          $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar los datos de la empresa: ".$th];
          echo json_encode($res);
        }
      }else{
        //empresa no definida correctamente
        $res = ["status"=>"error","mensaje"=>"No fue posible localizar la empresa."];
        echo json_encode($res);
      }
      
    } catch (\Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"No fue posible consultar los datos de la empresa: ".$th];
      echo json_encode($res);
    }
    // $queryEmp = mysqli_query($conexion, $sqlEmp);
  }
}else{

}

?>