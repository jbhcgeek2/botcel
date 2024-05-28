<?php 
session_start();
//consultaremos los datos de la empresa
if(!empty($_SESSION['usuarioPOS'])){
  // include("includes/header.php");
  include("includes/empresas.php");
  include("includes/conexion.php");
  include("includes/usuarios.php");
  include("includes/cliente.php");
  include("includes/ventas.php");
  

  $usuario = $_SESSION['usuarioPOS'];
  $ticket = $_GET['t'];
  //consultamos los datos del usuario
  $empresa = datoEmpresaSesion($usuario,"id");
  $idEmprersa = json_decode($empresa)->dato;
  $datosUsuario = getDataUser($usuario,$idEmprersa);
  $idSucursal = json_decode($datosUsuario)->sucursalID;
  $idUsuario = json_decode($datosUsuario)->idUsuario;
  // echo $idUsuario;

  $nombreEmpresa = datoEmpresaSesion($usuario,"nombre");
  $nombreEmpresa = json_decode($nombreEmpresa)->dato;

  //verificamos que la venta exista
  $trabajo = ticketTrabajo($ticket,$idEmprersa);
  $trabajo = json_decode($trabajo);
  // print_r($venta);718037884639
  // print_r($trabajo);

  if($trabajo->status == "ok"){
    
    // $datosTrab = json_decode($trabajo);
    $nombreSuc = $trabajo->data->nombreSuc;
    $domSuc = $trabajo->data->calleSuc;
    $telSuc = $trabajo->data->telefonoSuc;
    $numServ = $trabajo->data->numTrabajo;
    $numServ = str_pad($numServ,3 ,'0',STR_PAD_LEFT);
    $nombreCliente = $trabajo->data->nombreCliente;
    $fechaRegistro = $trabajo->data->fechaRegistro;
    $horaRegistro = $trabajo->data->horaRegistro;
    $nombreUsuario = $trabajo->data->nombreUsuario;

    $dispositivo = $trabajo->data->tipoDispositivo." ".$trabajo->data->marca." ".$trabajo->data->modelo;
    $tipoServicio = $trabajo->data->nombreServicio;
    $problema = $trabajo->data->problema;
    $observaciones = $trabajo->data->observaciones;
    $accesorios = $trabajo->data->accesorios;
    $costoAprox = $trabajo->data->costoInicial;
    $anticipo = $trabajo->data->anticipo;
    $fechaEntrega = $trabajo->data->fechaEntrega;

    
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
            Sucursal: <?php echo $nombreSuc; ?><br>
            Domicilio: <?php echo $domSuc; ?><br>
            Telefono: <?php echo $telSuc; ?><br>
            <br>
          </div>
          

          <table style="width:100%;">
            <thead>
              <tr>
                <th colspan="3" style="text-align:left;">Servicio No. <?php echo $numServ; ?></th>
              </tr>
              <tr style="font-size:13px;">
                <th colspan="3" style="font-weight:100;">Cliente - <?php echo $nombreCliente; ?></th>
              </tr>
              <tr style="font-size:13px;">
                <th colspan="3" style="font-weight:100;">Fecha y hora - <?php echo $fechaRegistro." - ".$horaRegistro; ?></th>
              </tr>
              <tr style="font-size:13px;">
                <th colspan="3" style="font-weight:100;">Empleado - <?php echo $nombreUsuario; ?></th>
              </tr>
              <tr>
                <th colspan="3" style="border-top: 1px solid;"></th>
              </tr>
              <tr>
                <th>Datos del Servicio</th>
              </tr>
              <tr>
                <th colspan="3" style="border-top: 1px solid;"></th>
              </tr>
            </thead>
            <tbody style="text-align:left;">
              <tr>
                <td style="font-weight:bold;">Dispositivo:</td>
              </tr>
              <tr>
                <td><?php echo $dispositivo; ?></td>
              </tr>
              <tr>
                <th style="border-top: 1px dotted;"></th>
              </tr>
              <tr>
                <td style="font-weight:bold;">Tipo de Servicio:</td>
              </tr>
              <tr>
                <td><?php echo $tipoServicio; ?></td>
              </tr>
              <tr>
                <th style="border-top: 1px dotted;"></th>
              </tr>
              <tr>
                <td style="font-weight:bold;">Descripcion del problema:</td>
              </tr>
              <tr>
                <td><?php echo $problema; ?></td>
              </tr>
              <tr>
                <th style="border-top: 1px dotted;"></th>
              </tr>
              <tr>
                <td style="font-weight:bold;">Observaciones:</td>
              </tr>
              <tr>
                <td><?php echo $observaciones; ?></td>
              </tr>
              <tr>
                <th style="border-top: 1px dotted;"></th>
              </tr>
              <tr>
                <td style="font-weight:bold;">Accesorios:</td>
              </tr>
              <tr>
                <td><?php echo $accesorios; ?></td>
              </tr>
              <tr>
                <th style="border-top: 1px dotted;"></th>
              </tr>
              <tr>
                <td style="font-weight:bold;">Costo Aproximado:</td>
              </tr>
              <tr>
                <td>$<?php echo $costoAprox; ?></td>
              </tr>
              <tr>
                <th style="border-top: 1px dotted;"></th>
              </tr>
              <tr>
                <td style="font-weight:bold;">Anticipo:</td>
              </tr>
              <tr>
                <td>$<?php echo $anticipo; ?></td>
              </tr>
              <tr>
                <th style="border-top: 1px dotted;"></th>
              </tr>
              <tr>
                <td style="font-weight:bold;">Fecha Estimada de entrega:</td>
              </tr>
              <tr>
                <td><?php echo $fechaEntrega; ?></td>
              </tr>
              <tr>
                <th style="border-top: 1px dotted;"></th>
              </tr>
              <tr>
                <td style="font-weight:bold;text-align:center;">Condiciones del Servicio</td>
              </tr>
              <tr>
                <th style="text-align: justify;font-size:13px;">
                  - Toda Revision causa honorarios ($100.00).<br>
                
                  - NOTA:Riesgo de dano en pantallas (LCD-Touch) en la intervencion del equipo 
                  sin responsabilidad del establecimiento notificacion al cliente oral y escrito.<br>
                  
                  - Despues de 60 dias de la fecha de entrega otorgo mi(s) equipo(s) y accesorios a Telcel
                  para que disponga de ellos.<br>
                  - Sin comprobante no se entregara su articulo.<br>
                  - No nos hacemos responsables por articulos no registrados en su nota.
                </th>
              </tr>
                
            </tbody>
          </table>
          <p style="margin-top:70px;text-align:center;border-top:1px solid;">
            <span>Firma del cliente</span>
          </p>
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
