<?php 
session_start();
//consultaremos los datos de la empresa
if(!empty($_SESSION['usuarioPOS'])){
  // include("includes/header.php");
  include("includes/empresas.php");
  include("includes/conexion.php");
  include("includes/articulos.php");
  include("includes/usuarios.php");
  include("includes/ventas.php");
  include("includes/cliente.php");

  $usuario = $_SESSION['usuarioPOS'];
  $ticket = $_GET['t'];
  //consultamos los datos del usuario
  $empresa = datoEmpresaSesion($usuario,"id");
  $idEmprersa = json_decode($empresa)->dato;
  $datosUsuario = getDataUser($usuario,$idEmprersa);
  $idSucursal = json_decode($datosUsuario)->sucursalID;
  $idUsuario = json_decode($datosUsuario)->idUsuario;
  // echo $idUsuario;
  $nombreUsuario = json_decode($datosUsuario)->nombreUsuario." ".json_decode($datosUsuario)->apPaternoUsuario." ".json_decode($datosUsuario)->apMaternoUsuario;

  $nombreEmpresa = datoEmpresaSesion($usuario,"nombre");
  $nombreEmpresa = json_decode($nombreEmpresa)->dato;

  //verificamos que la venta exista
  $venta = verTicket($ticket,$idUsuario);
  $venta = json_decode($venta);
  // print_r($venta);

  if($venta->status == "ok"){
    // print_r($venta);
    $nombreSucursal = $venta->sucursalVenta->nombreSuc;
    $fechaHora = $venta->venta->fechaVenta." - ".$venta->venta->horaVenta;
    $ticketNo = $venta->venta->num_comprobante;
    //para obtener los datos del cliente
    $cliente = $venta->venta->clienteID;
    if($cliente == 1){
      //publico en general
      $cliente = "Publico en General";
    }else{
      $dataCliente = verCliente($cliente,$idEmprersa);
      // $cliente = $venta->venta->clienteID;
      $dataCliente = json_decode($dataCliente);
      $cliente = $dataCliente->data->nombreCliente;
    }
    ?> 
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Impresion de Ticket</title>
        <link href='http://fonts.googleapis.com/css?family=Ubuntu&subset=cyrillic,latin' rel='stylesheet' type='text/css' />
      </head>
      <body>
        <div style="border: 0px solid #000; width:250px; text-align:center; font-family: 'Ubuntu', sans-serif;">
          <div style="font-size: 15px;">
            <?php echo $nombreEmpresa; ?><br>
            Sucursal: <?php echo $nombreSucursal; ?><br>
            Domicilio: <?php echo $venta->sucursalVenta->calleSuc; ?><br>
            Telefono: <?php echo $venta->sucursalVenta->telefonoSuc; ?><br>
            <br>
          </div>
          

          <table style="width:100%;">
            <thead>
              <tr>
                <th colspan="3" style="text-align:left;">Ticket No. <?php echo $ticketNo; ?></th>
              </tr>
              <tr style="font-size:13px;">
                <th colspan="3" style="font-weight:100;">Cliente - <?php echo $cliente; ?></th>
              </tr>
              <tr style="font-size:13px;">
                <th colspan="3" style="font-weight:100;">Fecha y hora - <?php echo $fechaHora; ?></th>
              </tr>
              <tr style="font-size:13px;">
                <th colspan="3" style="font-weight:100;">Cajero - <?php echo $nombreUsuario; ?></th>
              </tr>
              <tr>
                <th colspan="3" style="border-top: 1px solid;"></th>
              </tr>
              <tr>
                <th>UND</th>
                <th>ARTICULO</th>
                <th>TOTAL</th>
              </tr>
              <tr>
                <th colspan="3" style="border-top: 1px solid;"></th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $sumaTotal = 0;
                $descuento = $venta->venta->descuentoVenta;
                $totalVenta = $venta->venta->totalVenta;
                $montoPago = $venta->venta->montoPago;
                $cambioPago = $venta->venta->cambioPago;
                for($x = 0; $x < count($venta->detalleVenta); $x++){
                  if($venta->detalleVenta[$x]->trabajoID > 0){
                    $nombreArti = "Cobro de Servicio";
                  }else{
                    $nombreArti = $venta->detalleVenta[$x]->nombreArticulo;
                  }
                  $cantidad = $venta->detalleVenta[$x]->cantidadVenta;
                  $subtotal = $venta->detalleVenta[$x]->subtotalVenta;
                  $sumaTotal = $sumaTotal + $subtotal;
                  

                  echo "<tr>
                    <td style='text-align:center;'>$cantidad</td>
                    <td style='font-size:12px;'>$nombreArti</td>
                    <td style='text-align: right;'>$".number_format($subtotal,2)."</td>
                  </tr>";
                }//fin del for
                //indicamos si cuenta con descuento
                echo "
                
                <tr>
                  <td colspan='2' style='text-align: right;'>Descuentos</td>
                  <td style='text-align: right;'>$descuento%</td>
                </tr>
                <tr>
                  <td colspan='2' style='text-align: right;'>Subtotal</td>
                  <td style='text-align: right;'>$".number_format($sumaTotal,2)."</td>
                </tr>
                <tr>
                  <th colspan='3' style='border-top: 1px solid;'></th>
                </tr>
                <tr style='font-weight: bold; font-size:20px;'>
                  <td colspan='2' style='text-align: center;'>Total a Pagar</td>
                  <td style='text-align: right;'>$".number_format($totalVenta,2)."</td
                </tr>
                <tr>
                  <th colspan='3' style='border-top: 1px solid;'></th>
                </tr>
                <tr style='border-button: 1px solid;'>
                  <td colspan='2' style='text-align: right;'>Monto Recibido</td>
                  <td style='text-align: right;'>$".number_format($montoPago,2)."</td>
                </tr>
                <tr>
                  <td colspan='2' style='text-align: right;'>Cambio</td>
                  <td style='text-align: right;'>$".number_format($cambioPago,2)."</td>
                </tr>
                ";
              ?>
            </tbody>
          </table>
          <p>
            Gracias por su preferencia<br>
            Vuelva pronto
          </p>
        </div>
      </body>
      <script>
        window.print();
      </script>
    </html>
    <?php
  }else{
    //error al consultar el ticket
    echo "error";
  }

}
?>
