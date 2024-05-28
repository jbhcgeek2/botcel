<?php 

function guardarProducto($nombreArti,$descArti,$estatus,$empresa,$categoria,$img,$pUni,$pMayo,$mayoDes,$codigo,$proveedor){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  $fecha = date('Y-m-d');
  $sql = "INSERT INTO ARTICULOS (nombreArticulo,descripcionArticulo,estatusArticulo,
  empresaID,categoriaID,fechaAlta,imgArticulo,precioUnitario,precioMayoreo,mayoreoDesde,
  codigoProducto,proveedorID) VALUES ('$nombreArti','$descArti','$estatus',
  '$empresa','$categoria','$fecha','$img','$pUni','$pMayo','$mayoDes','$codigo','$proveedor')";
  try {
    $query = mysqli_query($conexion, $sql);
    $idArticulo = mysqli_insert_id($conexion);
    $res = ["status"=>"ok","mensaje"=>"operationSuccess","dato"=>$idArticulo];
    return json_encode($res);
  } catch (Throwable $th) {
    //no fue posible guardar el producto
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al procesar el producto: ".$th];
    return json_encode($res);
  }

}//fin funcion guardarProducto

function guardarArticuloSuc($existencia,$sucursal,$articulo){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "INSERT INTO ARTICULOSUCURSAL (existenciaSucursal,sucursalID,articuloID) 
  VALUES ('$existencia','$sucursal','$articulo')";
  try {
    $query = mysqli_query($conexion, $sql);
    //se inserto el articulosuicursal
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (Throwable $th) {
    //ocurrio un error al insertar el articuloSucursal
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error asignar las cantidades del articulo: ".$th];
    return json_encode($res);
  }
}

function getProductos($empresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  // $sql = "SELECT * FROM ARTICULOS WHERE empresaID = '$empresa'";
  $sql = "SELECT *,(SELECT SUM(b.existenciaSucursal) FROM ARTICULOSUCURSAL b 
  WHERE b.articuloID = a.idArticulo ) AS cantSucur FROM ARTICULOS a 
  WHERE a.empresaID = '$empresa' ORDER BY nombreArticulo ASC";
  try {
    $query = mysqli_query($conexion,$sql);
    $data = [];
    $i = 0;
    while($fetch = mysqli_fetch_assoc($query)){
      $data[$i] = $fetch;
      $i++;
    }//fin del while

    $res = ["status"=>"ok","data"=>$data,"mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (\Throwable $th) {
    //error en la consulta a los articulos
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar la informacion: ".$th];
    return json_encode($res);
  }

}

function setCategoria($idEmpresa,$nombreCat,$estatusCat,$descripCat){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "INSERT INTO CATEGORIA (nombreCategoria,estatusCategoria,descripcionCategoria,
  empresaID) VALUES ('$nombreCat','$estatusCat','$descripCat','$idEmpresa')";
  try {
    $query = mysqli_query($conexion, $sql);
    //se inserto correctamente la categoria
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (\Throwable $th) {
    //error al consultar la categoria
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar la informacion: ".$th];
    return json_encode($res);
  }
}

function getInfoproducto($empresa,$producto){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT * FROM ARTICULOS WHERE idArticulo = '$producto' AND empresaID = '$empresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    //se ejecuti correctamenet la informacion
    $fetch = mysqli_fetch_assoc($query);
    $res = ["status"=>"ok","data"=>$fetch];
    return json_encode($res);
  } catch (\Throwable $th) {
    //error al consultar la infroamcion
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar el producto: ".$th];
    return json_encode($res);
  }

}

function actualizaProducto($datos){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $data = json_decode($datos);

  $nombre = $data->nombre;
  $descr = $data->descri;
  $estatus = $data->estatusProd;
  $pUnitario = $data->pUnitario;
  $pMayo = $data->pMayo;
  $mayoreo = $data->mayoreoDesde;
  $cat = $data->categoria;
  $idArti = $data->producto;
  $codig = $data->codigo;
  $proveedor = $data->proveedor;

  if(!empty($data->imagen)){
    //si existe ruta de imagen actualizamos
    $imagen = $data->imagen;
    $sql = "UPDATE ARTICULOS SET nombreArticulo = '$nombre', descripcionArticulo='$descr',
    estatusArticulo = '$estatus', precioUnitario = '$pUnitario', precioMayoreo = '$pMayo', 
    mayoreoDesde = '$mayoreo', categoriaID = '$cat', imgArticulo = '$imagen',
    codigoProducto = '$codig', proveedorID = '$proveedor' WHERE idArticulo = '$idArti'";
  }else{
    //si no existe ruta de imagen no hacemos nada
    $sql = "UPDATE ARTICULOS SET nombreArticulo = '$nombre', descripcionArticulo='$descr',
    estatusArticulo = '$estatus', precioUnitario = '$pUnitario', precioMayoreo = '$pMayo', 
    mayoreoDesde = '$mayoreo', categoriaID = '$cat', codigoProducto = '$codig',
    proveedorID = '$proveedor' WHERE idArticulo = '$idArti'";
  }


  // $sql = "UPDATE ARTICULOS SET nombreArticulo = '$nombre', descripcionArticulo='$descr',
  // estatusArticulo = '$estatus', precioUnitario = '$pUnitario', precioMayoreo = '$pMayo', 
  // mayoreoDesde = '$mayoreo', categoriaID = '$cat' WHERE idArticulo = '$idArti'";

  try {
    $query = mysqli_query($conexion, $sql);
    //se actualizao el producto
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (Throwable $th) {
    //no fue posible actualizar el producto
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al actualizar el producto: ".$th];
    return json_encode($res);
  };

}

function getArtiSucursal($sucursal,$articulo){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT existenciaSucursal FROM ARTICULOSUCURSAL WHERE sucursalID = '$sucursal' AND articuloID = '$articulo'";
  try {
    $query = mysqli_query($conexion, $sql);
    //nueva seccion, si da error, quitarla
    if(mysqli_num_rows($query) > 0){
      $fetch = mysqli_fetch_assoc($query);
      $cantidad = $fetch['existenciaSucursal'];
      $res = ["status"=>"ok","mensaje"=>"operationSuccess","data"=>$cantidad];
      return json_encode($res);
    }else{
      $res = ["status"=>"ok","mensaje"=>"noData"];
      return json_encode($res);
    }
    //fin de seccion agregada
    
  } catch (Throwable $th) {
    //error al consultar el dato
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar: ".$th];
    return json_encode($res);
  }
}

function getProductosEmpresa($empresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT * FROM ARTICULOS WHERE empresaID = '$empresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    $i = 0;
    $data = [];
    if(mysqli_num_rows($query) > 0){
      while($fetch = mysqli_fetch_assoc($query)){
        $data[$i] = $fetch;
        $i++;
      }//fin del while
      $res = ["status"=>"ok","mensaje"=>"operationSuccess","data"=>$data];
      return json_encode($res);
    }else{
      //no se teiene productos capturados
      $res = ["status"=>"ok","mensaje"=>"noData"];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    //throw $th;
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar: ".$th];
    return json_encode($res);
  }
}

function setCantidad($cantidad,$articulo,$sucursal){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  
  $sql = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$cantidad' 
  WHERE sucursalID = '$sucursal' AND articuloID = '$articulo'";
  try {
    $query = mysqli_query($conexion, $sql);
    //entendemos que se guardo la nueva cantidad
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (\Throwable $th) {
    //ocurrio un error
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar las cantidades: ".$th];
    return json_encode($res);
  }
}

function procesaMovsProd($cambia,$nuevoPre,$precioCompra,$idProd,$idEmpresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  //esta funcion procesara la entra de un producto y actualizara
  //el precio de compra si excede el precio anterior
  $sql = "SELECT * FROM ARTICULOS WHERE idArticulo = '$idProd' AND empresaID = '$idEmpresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      $fetch = mysqli_fetch_assoc($query);
      $precioCompraActual = $fetch['precioCompra'];
      if($precioCompraActual < $precioCompra){
        //en este caso, el precio de compra actual es menor al nuevo, por lo tanto
        //tendremos que actualizar a este nuevo valor
        $concat = ", precioCompra = '$precioCompra'";
      }else{
        //si no lo supera, no ponemos nada
        $concat = "";
      }
    }else{
      //producto no localizado
    }
  } catch (\Throwable $th) {
    //throw $th;
  }
  
  if($cambia == "si"){
    //aqui cambiamos el precio de compra\
    $sql2 = "UPDATE ARTICULOS SET precioUnitario = '$nuevoPre' $concat WHERE idArticulo = '$idProd'
    AND empresaID = '$idEmpresa'";
  }else{
    //no se cambia el precio de compra, verificamos si se cambia el precio
    if($concat != ""){
      $sql2 = "UPDATE ARTICULOS SET precioCompra = '$precioCompra' WHERE idArticulo = '$idProd'
      AND empresaID = '$idEmpresa'";
    }else{
      //si tampoco entra no hacemos nada
    }
  }

  if($sql2 != ""){
    try {
      $query2 = mysqli_query($conexion,$sql2);
      $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
      return json_encode($res);
    } catch (\Throwable $th) {
      //error al actualizar la infromacion
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar la informacion del producto: ".$th];
      return json_encode($res);
    }
    
  }else{
    //literal no se hizo nada xD
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  }

}
// precioUnitario = 17
// precioCOmpra = 12.20
// existencia = 150
?>