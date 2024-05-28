<?php 
session_start();

if(!empty($_SESSION['usuarioPOS'])){
  //insertamos los archivos que necesitamos
  include("articulos.php");
  include("usuarios.php");
  include("documentos.php");

  if(!empty($_POST['nombreArticulo'])){
    //realizamos la actualizacion del producto
    $campos = ['dataProd','nombreArticulo','precioMenudeo','estatus','categoria','descripcion'];
    //verificamos los campos importantes
    $usuario = $_SESSION['usuarioPOS'];
    $mal = 0;
    for($x = 0; $x < count($campos); $x++){
      $valorCampo = $_POST[$campos[$x]];
      // echo $campos[$x]." = ".$valorCampo."--";
      if($valorCampo == "" || $valorCampo == " "){
        echo $campos[$x];
        $mal = $mal +1;
      }
    }//fin del for

    if($mal == 0){
      $empresa = datoEmpresaSesion($usuario,"id");
      $empresa = json_decode($empresa);
      $idEmpresaSesion = $empresa->dato;
      // echo $idEmpresaSesion;

      $idProd = $_POST['dataProd'];
      $nombreProd = $_POST['nombreArticulo'];
      $precioMenu = $_POST['precioMenudeo'];
      $precioMayo = $_POST['precioMayoreo'];
      $mayoDesde = $_POST['mayoreoDesde'];
      $estatus = $_POST['estatus'];
      $categoria = $_POST['categoria'];
      $descripcion = $_POST['descripcion'];
      $codigo = $_POST['codigoProducto'];
      $proveedor = $_POST['proveedor'];

      //verificamos si se va a cambiar de imagen
      $imgArti = "";
      $statusImg = 0;
      if(!empty($_FILES['imagenProducto']['name'])){
        //actualizamos la imagen del producto
        //no existe la imagenm asi que generamos la ruta desde 0
        $ruta = '../assets/images/productos';
        $img = $_FILES['imagenProducto'];
        $subida = uploadDoc($img,'imagen','producto_',$ruta,$idEmpresaSesion);
        $imgSubida = json_decode($subida);
        // echo $imgSubida->mensaje;
        if($imgSubida->mensaje == "operationSuccess"){
          $imgArti = $imgSubida->dato;
        }else{
          $statusImg = $imgSubida->mensaje;
        }
        
      }

      if($statusImg == 0){
        $datos = ["nombre"=>$nombreProd,"descri"=>$descripcion,
        "estatusProd"=>$estatus,"pUnitario"=>$precioMenu,"pMayo"=>$precioMayo,
        "mayoreoDesde"=>$mayoDesde,"categoria"=>$categoria,"producto"=>$idProd,
        "imagen"=>$imgArti,"codigo"=>$codigo,"proveedor"=>$proveedor];
        $datos = json_encode($datos);
  
        $update = actualizaProducto($datos);
  
        $respuesta = json_decode($update);
  
        if($respuesta->status == "ok"){
          //se actualizo el producto
          $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
          echo json_encode($res);
        }else{
          //error al actualizar el producto
          $res = ["status"=>"error","mensaje"=>$statusImg];
          echo json_encode($res);
        }
      }else{
        //ocurrio un error al actualizar la imagen
        $res = ["status"=>"error","mensaje"=>$respuesta->mensaje];
        echo json_encode($res);
      }

      
    }else{
      //ocurrio error en los campos
       $res = ["status"=>"error","mensaje"=>"Verifica que los campos esten capturados correctamente."];
      echo json_encode($res);
    }
  }
}

?>