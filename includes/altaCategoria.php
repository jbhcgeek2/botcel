<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){
  
  include("articulos.php");
  include("usuarios.php");
  include("conexion.php");

  if(!empty($_POST['nombreCat'])){
    //seccion para insertar la nueva categoria

    $nombreCat = $_POST['nombreCat'];
    $estatusCat = $_POST['estatus'];
    $descripcion = $_POST['descripcionCat'];
    $usuario = $_SESSION['usuarioPOS'];

    $empresa = datoEmpresaSesion($usuario,"id");
    $empresa = json_decode($empresa);
    if($empresa->status == "ok"){
      $idEmpresa = $empresa->dato;

      $guardar = setCategoria($idEmpresa,$nombreCat,$estatusCat,$descripcion);
      $guardar = json_decode($guardar);

      if($guardar->status == "ok"){
        $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
        echo json_encode($res);
      }else{
        //ocurrio un error al insertar
        $error = $guardar->mensaje;
        $res = ["status"=>"error","mensaje"=>$error];
        echo json_encode($res);
      }
    }else{
      //error al consultar el id de le empresa
      $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar la informacion del usuario."];
      echo json_encode($res);
    }
    




  }elseif(!empty($_POST['nombreCatServ'])){
    //seccion para registrar la categoria de servicio
    $nombreCat = $_POST['nombreCatServ'];
    $descCat = $_POST['descripcionCatServ'];
    $usuario = $_SESSION['usuarioPOS'];

    $empresa = datoEmpresaSesion($usuario,"id");
    $empresa = json_decode($empresa);
    $idEmpresa = $empresa->dato;


    $sqlCatServ = "INSERT INTO CATEGORIASERVICIO (nombreCatServ,estatusCategoriaServ,
    descripcionCategoriaServ,empresaID) VALUE ('$nombreCat','1','$descCat','$idEmpresa')";

    try {
      $queryCatServ = mysqli_query($conexion, $sqlCatServ);
      //se inserto correctamente la categoria
      $res = ['status'=>'ok','mensaje'=>'operationComplete'];
      echo json_encode($res);
    } catch (\Throwable $th) {
      //ocurrio un error al insertar la categoria
      $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al insertar la categoria de servicio: '.$th];
      echo json_encode($res);
    }
  }elseif(!empty($_POST['nombreCatServUpdate'])){
    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $empresa = json_decode($empresa);
    $idEmpresa = $empresa->dato;
    //seccion para modificar una categoria de servicio
    $idCatServ = $_POST['dataCatServUpate'];
    $nombreCatUpdate = $_POST['nombreCatServUpdate'];
    $estatusCat = $_POST['estatusCatServUpdate'];
    $descripcion = $_POST['descripcionCatServUpdate'];
    //verificamos la existencia
    $sqlCatServ = "SELECT * FROM CATEGORIASERVICIO WHERE idCategoriaServ = '$idCatServ' AND empresaID = '$idEmpresa'";
      try {
        $queryCatServ = mysqli_query($conexion,$sqlCatServ);
        if(mysqli_num_rows($queryCatServ) == 1){
          //si existe la categoria
         //hacemos la actualizacion
         $sqlUpdate = "UPDATE CATEGORIASERVICIO SET nombreCatServ = '$nombreCatUpdate', 
         estatusCategoriaServ = '$estatusCat', descripcionCategoriaServ = '$descripcion' 
         WHERE idCategoriaServ = '$idCatServ'";
         try {
          $queryUpdate = mysqli_query($conexion, $sqlUpdate);
          //se hizo la actualizacion
          $res = ['status'=>'ok','mensaje'=>'operationComplete'];
          echo json_encode($res);
         } catch (\Throwable $th) {
          $res = ['status'=>'error','mensaje'=>'Ocurrio un error al actualizar: '.$th];
          echo json_encode($res);
         }

        }else{
          //categoria no localizable
          $res = ['status'=>'error','mensaje'=>'No fue posible localizar la categoria.'];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar la categoria a modificar'];
        echo json_encode($res);
      }
  }
}
?>