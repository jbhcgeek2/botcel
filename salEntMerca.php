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

    if($rolUsuario == "Administrador"){
      
    }else{
      ?>
      <script>
        window.location = "reportesCaja.php";
      </script>
      <?php
    }
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Salidas y Entradas de Mercancia</h1>
			    			    
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
                        <input type="date" id="fechaIniMov" class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-3">
                        <label for="form-label">Fecha Fin</label>
                        <input type="date" id="fechaFinMov" class="form-control">
                      </div>
                      
                      <div class="col-sm-12 col-md-3 mt-4">
                        <a href="#!" class="btn btn-primary" role="buttom" id="btnBuscarMovs">Buscar</a>
                      </div>
                      
                    </div>

                    <hr clas="my-4">

                    <table class="table">
                      <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Producto</th>
                          <th>Monto Unitario Compra</th>
                          <th>Cantidad</th>
                          <th>Tipo Mov</th>
                          <th>Usuario</th>
                          <th>Sucursal</th>
                        </tr>
                      </thead>
                      <tbody id="bodyTableReport">
                        <?php 
                          //Por default consultamos la venta del dia de todas
                          //las sucursales pero si no tiene persmisos de admin
                          //solo podra ver las ventas de su usuario y sucursal
                          $fecha = date('Y-m-d');
                          $sql = "";
                          if($rolUsuario == "Administrador"){
                            //el administrador vera todas los movimientos de todos los usuario y todas las sucursales
                            $sql = "SELECT a.idSucursal,a.nombreSuc,b.*,
                            (SELECT c.nombreArticulo FROM ARTICULOS c WHERE c.idArticulo = b.prodMov) AS nombreProdMov 
                            FROM SUCURSALES a  INNER JOIN DETALLEINGRESO b 
                            ON a.idSucursal = b.sucursalID WHERE a.empresaSucID = '$idEmpresaSesion' AND b.fechaMov = '$fecha'";
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
                                $fechaMov = $fetch['fechaMov'];
                                $nombreProd = $fetch['nombreProdMov'];
                                $montoUnitario = $fetch['precioCompra'];
                                $cantidad = $fetch['cantidad'];
                                $sucursalMov = $fetch['nombreSuc'];
                                $usuarioMov = $fetch['usuarioMov'];

                                $totalVenta = $totalVenta + $total;

                                $dataSuc = getSucById($sucVenta);
                                $nombreSucVenta = json_decode($dataSuc)->dato;
                                echo "<tr>
                                  <td>$fechaMov</td>
                                  <td>$nombreProd</td>
                                  <td>$$montoUnitario</td>
                                  <td>$cantidad</td>
                                  <td>$usuarioMov</td>
                                  <td>$sucursalMov</td>
                                </tr>";
                              }//fin del while
                              // echo "<tr>
                              // <td colspan='3' class='fw-bold' style='text-align:right'>Total</td>
                              // <td class='fw-bold'>$".number_format($totalVenta,2)."</td>
                              // <td colpan='2'> </td>
                              // </tr>";
                            }else{
                              //sin resultados
                              echo "<tr>
                              <td colspan='7' style='text-align:center;'>Sin Movimientos registrados</td>
                              </tr>";
                            }
                          } catch (\Throwable $th) {
                            //error en la consulta
                            echo "<tr>
                              <td colspan='7'>Error de consulta</td>
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
    <script src="assets/js/alEntMerca.js"></script>
</body>
</html> 

