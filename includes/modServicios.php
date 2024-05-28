<?php 
session_start();

if(!empty($_SESSION['usuarioPOS'])){
  include('usuarios.php');
  include('conexion.php');
  $usuario = $_SESSION['usuarioPOS'];

  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;
  
  if(!empty($_POST['nombreServ'])){
    //seccion para modificar un servicio
    //pero antes de actualizarlo verificamos el estatuis actaul,
    //si cambia verificaremos el numero de servicios disponibles para blokear
    $idServicio = $_POST['servData'];
    $nombreServ = $_POST['nombreServ'];
    $tipoPrecio = $_POST['precioFijo'];
    $catServicio = $_POST['catServicio'];
    $precio = $_POST['precioServ'];
    $statusServ = $_POST['estatusServ'];
    if($tipoPrecio == "0"){
      $precio = "0";
    }else{
      $precio = $precio;
    }


    $sqlServ = "SELECT * FROM SERVICIOS WHERE idServicio = '$idServicio' AND 
    empresaID  = '$idEmpresaSesion'";
    try {
      $queryServ = mysqli_query($conexion, $sqlServ);
      if(mysqli_num_rows($queryServ) == 1){
        //si se localizo el servicio
        $fetchServ = mysqli_fetch_assoc($queryServ);

        $statusActualServ = $fetchServ['estatusCategoria'];
        if($statusActualServ == $statusServ || $statusServ == '0'){
          //no ay cambio de status, por lo que hacemos una modificacion normal
          $sqlServ2 = "UPDATE SERVICIOS SET nombreServicio = '$nombreServ',
          categoriaServicio = '$catServicio', estatusCategoria = '$statusServ',
          precioServicio = '$precio', precioFijo = '$tipoPrecio' WHERE idServicio = '$idServicio' 
          AND empresaID = '$idEmpresaSesion'";
          try {
            $queryServ2 = mysqli_query($conexion, $sqlServ2);
            //se proceso la actualizacion
            $res = ['status'=>"ok",'mensaje'=>'operationComplete'];
            echo json_encode($res);
          } catch (\Throwable $th) {
            //error de consulta a la base de datos
            $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al actualizar: '.$th];
            echo json_encode($res);
          }
        }else{
          //si ay cambio de estatus, por lo que verificamos cuanos servicios podemos tener
          $sqlServ3 = "SELECT *,(SELECT count(*) FROM SERVICIOS c WHERE 
          c.empresaID = a.idEmpresa AND c.estatusCategoria = '1') AS numServAlta FROM EMPRESAS a INNER JOIN SUSCRIPCION b ON 
          a.suscripcionID = b.idSuscripcion WHERE a.idEmpresa = '$idEmpresaSesion'";
          try {
            $queryServ3 = mysqli_query($conexion, $sqlServ3);
            $fetchServ3 = mysqli_fetch_assoc($queryServ3);
            $numActivos = $fetchServ3['numServAlta'];
            $numPErmitodos = $fetchServ3['maxServicios'];
            if($numActivos < $numPErmitodos || $numPErmitodos == '0'){
              //podemos gacer la modificacion
              $sqlServ4 ="UPDATE SERVICIOS SET nombreServicio = '$nombreServ',
              categoriaServicio = '$catServicio', estatusCategoria = '$statusServ',
              precioServicio = '$precio', precioFijo = '$tipoPrecio' WHERE idServicio = '$idServicio' 
              AND empresaID = '$idEmpresaSesion'";
              try {
                $queryServ4 = mysqli_query($conexion, $sqlServ4);
                //se completo todo correctamente
                $res = ['status'=> 'ok','mensaje'=>'operationComplete'];
                echo json_encode($res);
              } catch (\Throwable $th) {
                //ocurrio un error al actualizar 
                $res = ['status'=> 'error','mensaje'=>'Ha ocurrido un error al actualizar el servicio 2: '.$th];
                echo json_encode($res);
              }
            }else{
              //ya no puede registrtar mas
              $res = ['status'=>'error','mensaje'=>'Se ha llegado al limite de servicios permitidos'];
              echo json_encode($res);
            }
          } catch (\Throwable $th) {
            //throw $th;
          }

        }

        

      }else{
        //ocurrio un error al tratar de localizar el servicio
      }
    } catch (\Throwable $th) {
      //error al realizar la consulta
    }
  }
}
?>