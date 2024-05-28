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
     
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Listado de Clientes</h1>
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="altaCliente.php">Registrar Cliente</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="listadoClientes">

                  <div class="col-sm-12 input-group mb-3">
                    <span class="input-group-text">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                      </svg>
                    </span>
                    <input type="text" class="form-control" id="buscarCliente" placeholder="Buscar cliente">
                  </div>

                  <div class="row">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Nombre</th>
                          <th>Telefono</th>
                          <th>Correo</th>
                          <th>Ver</th>
                        </tr>
                      </thead>
                      <tbody id="resultBusqueda">
                        <?php
                          //realizaremos la consulta de clientes
                          $verClientes = verClientes($idEmpresaSesion);
                          // print_r($verClientes);
                          $verClientes = json_decode($verClientes);
                          if($verClientes->status == "ok"){
                            if(count($verClientes->data) > 0){
                              for ($i=0; $i < count($verClientes->data) ; $i++) { 
                                $nombre = $verClientes->data[$i]->nombreCliente;
                                $telefono = $verClientes->data[$i]->telefonoCliente;
                                $correo = $verClientes->data[$i]->emailCliente;
                                $idCliente = $verClientes->data[$i]->idClientes;

                                echo "<tr>
                                  <td>$nombre</td>
                                  <td>$telefono</td>
                                  <td>$correo</td>
                                  <td>
                                    <a href='verCliente.php?cliente=$idCliente'>Ver</a>
                                  </td>
                                </tr>";
                              }
                            }else{
                              //sin clientes registrados
                              echo "<tr>
                                <td colspan='4' class='text-center'><h5>Sin Clientes Registrados</h5></td>
                              </tr>
                              <tr>
                                <td colspan='4' class='text-center'>
                                  <img src='../assets/images/no-data.png'>
                                </td>
                              </tr>
                              ";
                            }
                          }else{
                            //error al consultar los clientes
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>
      
					        </div><!--//app-card-body-->
				        </div><!--//app-card-->
			        </div><!--//col-->
          <hr class="my-4">
        


			    
	    
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
    <script src="assets/js/clientes.js"></script>
    <!-- <script src="assets/js/altaProducto.js"></script> -->
</body>
</html> 

