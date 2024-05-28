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
    include("includes/articulos.php");
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Ventas del Dia</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="reportesCaja.php">Ver Reportes</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="reportes">
      
                    <div class="row">
                      <div class="col-sm-12 col-md-3">
                        <label for="form-label">Fecha Inicio</label>
                        <input type="date" id="fechaIni" class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-3">
                        <label for="form-label">Fecha Fin</label>
                        <input type="date" id="fechaFin" class="form-control">
                      </div>
                      
                      <div class="col-sm-12 col-md-3 mt-4">
                        <a href="#!" class="btn btn-primary" role="buttom" id="btnBuscar">Buscar</a>
                      </div>
                      
                    </div>

                    <hr clas="my-4">

                    <table class="table">
                      <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Producto</th>
                          <th>Cantidad</th>
                          <th>Total</th>
                          <th>Usuario</th>
                          <th>Sucursal</th>
                          <th>Ticket</th>
                        </tr>
                      </thead>
                      <tbody id="bodyTableReport">
                        <?php 
                          //Por default consultamos la venta del dia de todas
                          //las sucursales pero si no tiene persmisos de admin
                          //solo podra ver las ventas de su usuario y sucursal
                          $fecha = date('Y-m-d');
                          // $fecha = '2024-05-25';
                          $sql = "";
                          if($rolUsuario == "Administrador"){
                            $sql = "SELECT * FROM VENTAS a  INNER JOIN DETALLEVENTA b ON a.idVenta = b.ventaID 
                            WHERE a.fechaVenta = '$fecha' AND a.empresaID = '$idEmpresaSesion'";
                          }elseif($rolUsuario == "Vendedor"){
                            //solo podra ver las ventas de su usuario y sucursal
                            $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
                            INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
                            WHERE b.fechaVenta = '$fecha' AND a.usuarioVenta = '$usuario' 
                            AND a.sucursalID = '$idSucursalN'";
                          }else{
                            //el usuario encargado podra ver las ventas de todos
                            //los usuarios, pero solo de su susucrsal
                            $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
                            INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
                            WHERE b.fechaVenta = '$fecha' AND a.sucursalID = '$idSucursalN'";
                          }

                          try {
                            $query = mysqli_query($conexion, $sql);
                            $totalVenta = 0;
                            if(mysqli_num_rows($query) > 0){
                              while($fetch = mysqli_fetch_assoc($query)){
                                $nombreCosa = "";
                                $fechaVenta = $fetch['fechaVenta'];
                                //verificamos si la venta es un producto o servicio
                                if(!empty($fetch['articuloID'])){
                                  //se trata de un articulo
                                  $idProd = $fetch['articuloID'];
                                  $sqlExt = "SELECT * FROM ARTICULOS WHERE idArticulo = '$idProd' AND empresaID = '$idEmpresaSesion'";
                                  $queryExt = mysqli_query($conexion, $sqlExt);
                                  $fetchExt = mysqli_fetch_assoc($queryExt);
                                  $nombreCosa =  $fetchExt['nombreArticulo'];
                                }else{
                                  //es un servicio
                                  $idServ = $fetch['trabajoID'];
                                  $sqlExt2 = "SELECT a.costoFinal,b.nombreServicio FROM TRABAJOS a INNER JOIN SERVICIOS b ON a.servicioID = b.idServicio 
                                  WHERE a.idTrabajo = '$idServ'";
                                  $queryExt2 = mysqli_query($conexion, $sqlExt2);
                                  $fetchExt2 = mysqli_fetch_assoc($queryExt2);
                                  $nombreCosa = $fetchExt2['nombreServicio'];
                                }

                                // $nombreprod = $fetch['nombreArticulo'];
                                $usuarioVent = $fetch['usuarioVenta'];
                                $sucVenta = $fetch['sucursalID'];
                                $idVenta = $fetch['idVenta'];
                                $cantVenta = $fetch['cantidadVenta'];

                                //Verificamos si tiene descuento para mostrar el p[resupuesto total]
                                $subtotal = floatval($fetch['subtotalVenta']);
                                $total = "0";
                                $descuento = floatval($fetch['descuentoVenta']);
                                if($descuento != "0.00"){
                                  //tiene descuento y consultraremos el total
                                  $descu = ($descuento * $subtotal) / 100;
                                  $total = number_format($subtotal - $descu,2);
                                }else{
                                  //sin descuento
                                  $total = number_format($subtotal,2);
                                }

                                

                                $totalVenta = $totalVenta + $total;

                                $dataSuc = getSucById($sucVenta);
                                $nombreSucVenta = json_decode($dataSuc)->dato;
                                echo "<tr>
                                  <td>$fechaVenta</td>
                                  <td>$nombreCosa</td>
                                  <td>$cantVenta</td>
                                  <td>$$total</td>
                                  <td>$usuarioVent</td>
                                  <td>$nombreSucVenta</td>
                                  <td>
                                    <a target='_blank' href='print.php?t=$idVenta' class='btn btn-success'>Ver Ticket</a>
                                  </td>
                                </tr>";
                              }//fin del while
                              echo "<tr>
                              <td colspan='3' class='fw-bold' style='text-align:right'>Total</td>
                              <td class='fw-bold'>$".number_format($totalVenta,2)."</td>
                              <td colspan='3'> </td>
                              </tr>";
                            }else{
                              //sin resultados
                              echo "<tr>
                              <td colspan='6' style='text-align:center;'>Sin ventas registradas</td>
                              </tr>";
                            }
                          } catch (\Throwable $th) {
                            //error en la consulta
                            echo "<tr>
                              <td colspan='6'>Error de consulta</td>
                            </tr>";
                          }
                        ?>
                      </tbody>
                    </table>

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
    <script src="assets/js/ventasDelDia.js"></script>
</body>
</html> 

