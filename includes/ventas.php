<?php 

function getTotalVenta($usuario,$sucursal){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT SUM(subtotalVenta) AS totalVenta FROM DETALLEVENTA WHERE usuarioVenta = '$usuario' 
  AND sucursalID = '$sucursal' AND ventaID IS NULL";
  try {
    $query = mysqli_query($conexion, $sql);
    $fetch = mysqli_fetch_assoc($query);
    $totalVenta = $fetch['totalVenta'];
    $res = ["status"=>"ok","data"=>$totalVenta];
    return json_encode($res);
  } catch (Throwable $th) {
    //error al consultar el total
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar el total: ".$th];
    return json_encode($res);
  }

}//fin getTotalVenta

function getTotalArti($usuario,$sucursal){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  $sql = "SELECT SUM(cantidadVenta) AS nArticulos FROM DETALLEVENTA WHERE usuarioVenta = '$usuario'
  AND sucursalID = '$sucursal' AND ventaID IS NULL";
  try {
    $query = mysqli_query($conexion, $sql);
    $fetch = mysqli_fetch_assoc($query);
    $totalArti = $fetch['nArticulos'];

    $res = ["status"=>"ok","data"=>$totalArti];
    return json_encode($res);
  } catch (\Throwable $th) {
    //error de consulta
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar los articulos"];
    return json_encode($res);
  }
}

function verTicket($ticket,$idUsuario){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT * FROM VENTAS WHERE idVenta = '$ticket' AND usuarioID = '$idUsuario'";
  try {
    $query = mysqli_query($conexion, $sql);
    $fetch = mysqli_fetch_assoc($query);
    if(mysqli_num_rows($query) > 0){
      $idVenta = $fetch['idVenta'];
      // $sql2 = "SELECT * FROM DETALLEVENTA a INNER JOIN ARTICULOS b ON a.articuloID = b.idArticulo  WHERE ventaID = '$idVenta'";
      $sql2 = "SELECT * FROM DETALLEVENTA WHERE ventaID = '$idVenta'";
      try {
        $query2 = mysqli_query($conexion, $sql2);
        $data2 = [];
        $x = 0;
        $idSucursal = "";
        while($fetch2 = mysqli_fetch_assoc($query2)){
          //verificamos si el registro es articulo o servicio
          if($fetch2['articuloID'] != NULL || $fetch2['articuloID'] > 0){
            //es articulo
            $idDetalle = $fetch2['idDetalleVenta'];
            $sqlExt2 = "SELECT * FROM DETALLEVENTA a INNER JOIN ARTICULOS b 
            ON a.articuloID = b.idArticulo  WHERE ventaID = '$idVenta' AND idDetalleVenta = '$idDetalle'";
            $queryExt2 = mysqli_query($conexion, $sqlExt2);
            $fetchExt2 = mysqli_fetch_assoc($queryExt2);
            //
            $data2[$x] = $fetchExt2;
          }else{
            //es trabajo/servicio
            $data2[$x] = $fetch2;
          }
          // $data2[$x] = $fetch2;
          $idSucursal = $fetch2['sucursalID'];
          $x++;
        }//fin del while
        $sql3 = "SELECT * FROM SUCURSALES WHERE idSucursal = '$idSucursal'";
        try {
          $query3 = mysqli_query($conexion,$sql3);
          $fetch3 = mysqli_fetch_assoc($query3);

          $data3 = $fetch3;
          $res = ["status"=>"ok","venta"=>$fetch,"detalleVenta"=>$data2,"sucursalVenta"=>$data3];
          return json_encode($res);
        } catch (\Throwable $th){
          $res = ["status"=>"error","mensaje"=>"Error al consultar la sucursal de venta: ".$th];
          return json_encode($res);
        }

      } catch (\Throwable $th) {
        //throw $th;
        $res = ["status"=>"error","mensaje"=>"Error al consultar el detalle venta: ".$th];
        return json_encode($res);
      }
    }else{
      $res = ["status"=>"error","mensaje"=>"noData"];
      return json_encode($res);
    }
    
    
  } catch (\Throwable $th) {
    $res = ["status"=>"error","mensaje"=>"Error al consultar el ticket: ".$th];
    return json_encode($res);
  }

}

function verTicketByCliente($cliente){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT *,(SELECT SUM(b.cantidadVenta) FROM DETALLEVENTA b 
  WHERE b.ventaID = a.idVenta) AS numArti,(SELECT c.nombreUsuario FROM 
  USUARIOS c WHERE c.idUsuario = a.usuarioID) AS nombreUsuario FROM VENTAS a WHERE a.clienteID = '$cliente'";

  try {
    $query = mysqli_query($conexion, $sql);
    $data = [];
    $i = 0;
    while($fetch = mysqli_fetch_assoc($query)){
      $data[$i] = $fetch;
      $i++;
    }//fin del while
    $res = ["status"=>"ok","data"=>$data];
    return json_encode($res);
  } catch (\Throwable $th) {
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultas las ventas: ".$th];
    return json_encode($res);
  }
}

function ticketTrabajo($idTrabajo,$idEmpresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT * FROM TRABAJOS a INNER JOIN CLIENTES b ON a.clienteID = b.idClientes 
  INNER JOIN USUARIOS c ON a.usuarioID = c.idUsuario INNER JOIN SUCURSALES d 
  ON a.sucursalID = d.idSucursal INNER JOIN EMPRESAS e ON e.idEmpresa = a.empresaID 
  INNER JOIN SERVICIOS f ON f.idServicio = a.servicioID
  WHERE a.idTrabajo = '$idTrabajo' AND a.empresaID = '$idEmpresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      $fetch = mysqli_fetch_assoc($query);

      $res = ['status'=>'ok','data'=>$fetch];
      return json_encode($res);
    }else{
      //trabajo no lozalizado
      $res = ['status'=>'error','mensaje'=>'No fue posible localizar el trabajo'];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    //throw $th;
    $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar el trabajo: '.$th];
    return json_encode($res);
  }
}

?>