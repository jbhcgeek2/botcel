<!DOCTYPE html>
<html lang="en"> 
<?php
session_start();


 	include("includes/head.php");
?>

<body class="app">   	
  <?php
    include("includes/header.php");
    include("includes/empresas.php");
    include("includes/conexion.php");
    include("includes/cliente.php");
    include("includes/ventas.php");

    //verificamos que este la informacion del cliente y pertenesca a la emprsa
    if(!empty($_GET['cliente'])){
      $idCliente = $_GET['cliente'];
      //verificaremos que el cliente exista en la empresa
      $cliente = verCliente($idCliente,$idEmpresaSesion);
      $cliente = json_decode($cliente);
      if($cliente->status == "ok"){
        // print_r($cliente);
        $idCliente = $cliente->data->idClientes;
        $nombreCliente = $cliente->data->nombreCliente;
        $telefono = $cliente->data->telefonoCliente;
        $email = $cliente->data->emailCliente;
        $direccion = $cliente->data->direccionCliente;
        $rfc = $cliente->data->rfcCliente;

      }else{
        //error en la consulta
        header("Location: clientes.php");
        ?>
        <script>
          window.location = 'clientes.php';
        </script>
        <?php
      }

    }else{
      //no se dice nda, lo mandamos pa atras
      header("Location: clientes.php");
      ?>
      <script>
        window.location = 'clientes.php';
      </script>
      <?php
    }

  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Informacion del Cliente</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="clientes.php">Ver Clientes</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoForm">
                    <form action="" id="datosClienteUpdate">
                      <div class="containe row">
                        <input type="hidden" name="clienteUpdate" id="cliente" value="<?php echo $idCliente; ?>">
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label class="form-label" for="nombreClienteUpdate">Nombre Cliente</label>
                          <input type="text" id="nombreClienteUpdate" name="nombreClienteUpdate" class="form-control" value="<?php echo $nombreCliente; ?>">
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="telefonoCliente" class="form-label">Telefono</label>
                          <input type="text" id="telefonoCliente" name="telefonoCliente" class="form-control" value="<?php echo $telefono; ?>">
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="emailCliente" class="form-label">Email</label>
                          <input type="text" id="emailCliente" name="emailCliente" class="form-control" value="<?php echo $email; ?>">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                          <label for="direccionCliente" class="form-label">Direccion</label>
                          <input type="text" id="direccionCliente" name="direccionCliente" class="form-control" value="<?php echo $direccion; ?>">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                          <label for="rfcCliente" class="form-label">RFC</label>
                          <input type="text" id="rfcCliente" name="rfcCliente" class="form-control" value="<?php echo $rfc; ?>">
                        </div>

                      </div>
                      
                        <a href="#!" class="btn btn-success" id="btnUpdateCliente">Actualizar datos</a>
                  
                    </form>
					        </div><!--//app-card-body-->
				        </div><!--//app-card-->

                <!-- ingresaremos los posibles compras que ha realizado el usuario -->
                
			        </div><!--//col-->

              
          <hr class="my-4">
        
          <div class="app-card app-card-chart h-100 shadow-sm">
            
            <div class="app-card-header p-3">
              <div class="row justify-content-between align-items-center">

                <div class="col-auto">
                  <h4 class="app-card-title text-center">Tickets del Cliente</h4>
                </div><!--//col-->
              </div><!--//row-->
            </div><!--//app-card-header-->

            <div class="app-card-body p-3 p-lg-4" >
              <div class="row">
                <div class="col-sm-12">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>No. Ticket</th>
                        <th>Fecha Venta</th>
                        <th>Usuario</th>
                        <th>Articulos</th>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        //consultaremos las ventas del cliente
                        $ventas = verTicketByCliente($idCliente);
                        // print_r($ventas);
                        $ventas = json_decode($ventas);
                        if($ventas->status == "ok"){
                          //mostramos las ventas, si es que tiene
                          if(count($ventas->data)> 0){
                            for ($i=0; $i < count($ventas->data); $i++) { 
                              $ticket = $ventas->data[$i]->num_comprobante;
                              $fechaVenta = $ventas->data[$i]->fechaVenta;
                              $articulos = $ventas->data[$i]->numArti;
                              $total = $ventas->data[$i]->totalVenta;
                              $usuarioVenta = $ventas->data[$i]->nombreUsuario;
                              // $fechaVenta = $ventas->data[$i]->fechaVenta;
                              echo "<tr>
                                <td>$ticket</td>
                                <td>$fechaVenta</td>
                                <td>$usuarioVenta</td>
                                <td>$articulos</td>
                                <td>$".number_format($total,2)."</td>
                              </tr>";
                            }//fin del for
                          }else{
                            //sin resultados
                            echo "<tr>
                              <td colspan='5'><h5 class='text-center'>Sin Resultados</h5></td>
                            </tr>";
                          }
                        }else{
                          //error en la consulta
                          $error = $ventas->mensaje;
                          echo "<tr><td colspan='5' class='text-center'>$error</td></tr>";
                        }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

			    
	    
	    <?php 
        include("includes/footer.php");
      ?>
    </div><!--//app-wrapper-->    					

 
    <!-- Javascript -->          
    <script src="assets/plugins/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>


    
    <!-- Page Specific JS -->
    <script src="assets/js/app.js"></script> 
    <script src="assets/js/swetAlert.js"></script>
    <script src="assets/js/verCliente.js"></script>
</body>
</html> 

