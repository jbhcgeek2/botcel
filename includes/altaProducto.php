<?php 
// ini_set('display_errors', 1);

session_start();


if(!empty($_SESSION['usuarioPOS'])){
  require("usuarios.php");
  require("empresas.php");
  require("articulos.php");
  require("documentos.php");

  
  if(!empty($_POST['nombreArticulo'])){
    //hacemos la validacion de datos
    
    $camposObligatorios = ['nombreArticulo','precioMenudeo','estatus','descripcion','categoria'];
    $camposMal = 0;
    for($x = 0; $x < count($camposObligatorios); $x++){
      if(empty($_POST[$camposObligatorios[$x]])){
        $camposMal++;
      }
    }//fin del for
    
    if($camposMal == 0){
      //tiene los campos capturados
      //el primer paso es insertar los datos del producto base en la tabla ARTICULOS
      $nombreArti = htmlentities($_POST['nombreArticulo']);
      $pMenudeo = $_POST['precioMenudeo'];
      $pMayoreo = $_POST['precioMayoreo'];
      $descr = $_POST['descripcion'];
      $estatus = $_POST['estatus'];
      $usuario = $_SESSION['usuarioPOS'];
      $categoria = $_POST['categoria'];
      $mayoDesde = $_POST['mayoreoDesde'];
      $codigo = $_POST['codigoProducto'];
      $proveedor = $_POST['provProducto'];
      
      $empresa = datoEmpresaSesion($usuario,"id");
      $empresa = json_decode($empresa);
      $idEmpresaSesion = $empresa->dato;
      $imgArti = "";
      if(!empty($_FILES['imagenProducto']['name'])){
        //se detecto que el producto tiene imagen cargada
        $img = $_FILES['imagenProducto'];
        $ruta = '../assets/images/productos';
        $subida = uploadDoc($img,'imagen','producto_',$ruta,$idEmpresaSesion);
        $imgSubida = json_decode($subida);
        if($imgSubida->mensaje == "operationSuccess"){
          $imgArti = $imgSubida->dato;  
        }
        // $imgArti = "";
      }

      $guardarProd = guardarProducto($nombreArti,$descr,$estatus,$idEmpresaSesion,
      $categoria,$imgArti,$pMenudeo,$pMayoreo,$mayoDesde,$codigo,$proveedor);
      $guardarProd = json_decode($guardarProd);
      if($guardarProd->mensaje == "operationSuccess"){
        //verificamos el id de articulo
        $idArticulo = $guardarProd->dato;
        //procedemos a insertar los articulos de las sucursales
        $sucursales = verSucursales($usuario,'');
        $sucursales = json_decode($sucursales);
        
        for($x = 0; $x < count($sucursales->dato); $x++){ 
          //buscamos el campos post de la sucursal
          $idSucursal = $sucursales->dato[$x]->idSucursal;
          $campo = "cantidadSuc".$idSucursal;
          $cantidadSuc = $_POST[$campo];

          if($cantidadSuc > 0){
            //insertamos la cantidad en la sucursal
            $cantidadSuc = guardarArticuloSuc($cantidadSuc,$idSucursal,$idArticulo);
            $cantidadSuc = json_decode($cantidadSuc);
            if($cantidadSuc->mensaje == "operationSuccess"){

            }else{
              //error al registrar la cantidad de la sucursal
              $camposMal++;
            }
          }else{
            //no tiene cantidad, no hacemos nada
          }
        }//fin del for sucursales

        if($camposMal == 0){
          //se inserto todo correctamente
          $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
          echo json_encode($res);
        }else{
          //error al insertar algun campo
          $error = $cantidadSuc->mensaje;
          $res = ["status"=>"error","mensaje"=>"Ocurrio un error al insertar la cantidad: ".$error];
          echo json_encode($res);
        }
      }else{
        //ocurrio un error al insertar el prodcuto
        $error = $guardarProd->mensaje;
        $res = ["status"=>"error","mensaje"=>"Ocurrio un error al insertar el producto: ".$error];
        echo json_encode($res);
      }
    }else{
      //se detectaron campos en blanco
      $res = ["status"=>"error","mensaje"=>"El formulario contiene espacion vacios, verificalo."];
      echo json_encode($res);
    }
  }
}else{
  //sin sesioninisada
}
?>