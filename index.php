<!DOCTYPE html>
<html lang="en"> 
<?php
 	include("includes/head.php");
?>

<body class="app">   	
  <?php
    include("includes/header.php");
		include("includes/conexion.php");
		//este resumen solo estara habilitado para administradores
		// echo $rolUsuario;
		// echo $idEmpresaSesion;
		if($rolUsuario == "Vendedor"){
			header("Location: caja.php");
			?>
			<script>
				window.location = "caja.php";
			</script>
			<?php
		}
		
		$fechaHoy = date('Y-m-d');
		//realizamos las consultas para ver las ventas totales en el mes
		$sqlVentas = "SELECT SUM(totalVenta) AS ventasEnMes FROM VENTAS 
		WHERE empresaID = '$idEmpresaSesion' AND MONTH(fechaVenta) = MONTH(CURDATE())";
		try {
			$queryVentas = mysqli_query($conexion, $sqlVentas);
			$fetchVentas = mysqli_fetch_assoc($queryVentas);
			$totVentas = $fetchVentas['ventasEnMes'];
		} catch (\Throwable $th) {
			//error de consulta
			$totVentas = "1";
		}
		
		$sqlVentasAnt = "SELECT SUM(totalVenta) AS ventasMesAnt FROM VENTAS
		WHERE empresaID = '$idEmpresaSesion' AND MONTH(fechaVenta) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
		AND YEAR(fechaVenta) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
		try {
			$queryVentasAnt = mysqli_query($conexion, $sqlVentasAnt);
			$fetchVentasAnt = mysqli_fetch_assoc($queryVentasAnt);
			$totVentasAnt = $fetchVentasAnt['ventasMesAnt'];
			if($totVentasAnt >0){
				$totVentasAnt = $totVentasAnt;
			}else{
				$totVentasAnt = "1";
			}
		} catch (\Throwable $th) {
			$totVentasAnt = "1";	
		}
		//aqui esta el error del index
		$diferenciaVentas = $totVentas - $totVentasAnt; 
		$porcentageVentas = ($diferenciaVentas / $totVentasAnt) * 100;
		$porcentageVentas = number_format($porcentageVentas,2);
		$iconoVentas = "";
		$colorVentas = "";
		
		if($diferenciaVentas > 0){
			//incrementaron las ventas
			$iconoVentas = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
			<path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5"/>
			</svg>';
			$colorVentas = "text-success";
		}else{
			//las ventas disminuyeros
			$iconoVentas = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
			<path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1"/>
			</svg>';
			$colorVentas = "text-danger";
		}
		
		//para consultar los gatros mensuales, consultaremos la tabla de movimientos caja
		//aquellos que tengan el concepto de salida y adquisicion de mercancia (9 y 10)
		$sqlGasto = "SELECT SUM(montoMov) AS gastoMensual FROM MOVCAJAS WHERE empresaMovID = '$idEmpresaSesion' 
		AND conceptoMov IN (9,10) AND MONTH(fechaMovimiento) = MONTH(CURDATE())";
		try {
			$queryGasto = mysqli_query($conexion, $sqlGasto);
			$fetchGasto = mysqli_fetch_assoc($queryGasto);
			$montoGasto = $fetchGasto['gastoMensual'];

		} catch (\Throwable $th) {
			//error en la consulta
			$montoGasto = '1.00';
		}
		
		$sqlGasAnt = "SELECT SUM(montoMov) AS gastoMesAnt FROM MOVCAJAS
		WHERE empresaMovID = '$idEmpresaSesion' AND conceptoMov IN (9,10) AND MONTH(fechaMovimiento) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
		AND YEAR(fechaMovimiento) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
		try {
			$queryGasAnt = mysqli_query($conexion, $sqlGasAnt);
			$fetchGasAnt = mysqli_fetch_assoc($queryGasAnt);
			$gastoMesAnt = $fetchGasAnt['gastoMesAnt'];
			
			$difGasto = $montoGasto - $gastoMesAnt;
			$porcentGasto = ($difGasto / $gastoMesAnt) * 100;
			$porcentGasto = number_format($porcentGasto,2);
			$iconoGasto = "";
			$colorGasto = "";
			if($difGasto > 0){
				//el gasto se incremento respecto del mes anterior
				$iconoGasto = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
				<path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5"/>
				</svg>';
				$colorGasto = "text-danger";
			}else{
				//el gasto se ha mantenido abajo respecto del mes anterior
				$iconoGasto = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
				<path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1"/>
				</svg>';
				$colorGasto = "text-success";
			}
		} catch (\Throwable $th) {
			//
		}
		
		$sqlVentasDia = "SELECT SUM(totalVenta) AS ventasDia FROM VENTAS WHERE 
		empresaID = '$idEmpresaSesion' AND fechaVenta = '$fechaHoy'";
		try {
			$queryVentasHoy = mysqli_query($conexion, $sqlVentasDia);
			$fetchVentasHoy = mysqli_fetch_assoc($queryVentasHoy);
			$ventasHoy = $fetchVentasHoy['ventasDia'];

		} catch (\Throwable $th) {
			$ventasHoy = "0";
		}

		$sqlArti = "SELECT SUM(b.existenciaSucursal) AS totArti FROM SUCURSALES a  INNER JOIN 
		ARTICULOSUCURSAL b ON a.idSucursal = b.sucursalID WHERE a.empresaSucID = '$idEmpresaSesion' AND b.existenciaSucursal > 0";
		try {
			$queryArti = mysqli_query($conexion, $sqlArti);
			$fetchArti = mysqli_fetch_assoc($queryArti);
			$artiActual = $fetchArti['totArti'];
		} catch (\Throwable $th) {
			//error de consulta
			$artiActual = 0;
		}

  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Resumen</h1>
			    
			    <div class="app-card alert alert-dismissible shadow-sm mb-4 border-left-decoration" role="alert" style="display:none;">
				    <div class="inner">
							
					    <div class="app-card-body p-3 p-lg-4" >
						    <h3 class="mb-3">Welcome, developer!</h3>
						    <div class="row gx-5 gy-3">
						      <div class="col-12 col-lg-9">
							      <div>Portal is a free Bootstrap 5 admin dashboard template. The design is simple, clean and modular so it's a great base for building any modern web app.</div>
							    </div><!--//col-->
							    <div class="col-12 col-lg-3">
								    <a class="btn app-btn-primary" href="https://themes.3rdwavemedia.com/bootstrap-templates/admin-dashboard/portal-free-bootstrap-admin-dashboard-template-for-developers/">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-file-earmark-arrow-down me-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path d="M4 0h5.5v1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h1V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/>
												<path d="M9.5 3V0L14 4.5h-3A1.5 1.5 0 0 1 9.5 3z"/>
												<path fill-rule="evenodd" d="M8 6a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 10.293V6.5A.5.5 0 0 1 8 6z"/>
											</svg>
											Free Download
										</a>
							    </div><!--//col-->
						    </div><!--//row-->
						    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					    </div><!--//app-card-body-->
					    
				    </div><!--//inner-->
			    </div><!--//app-card-->
				    
			    <div class="row g-4 mb-4">
				    <div class="col-6 col-lg-3">
					    <div class="app-card app-card-stat shadow-sm h-100">
						    <div class="app-card-body p-3 p-lg-4">
							    <h4 class="stats-type mb-1">Ventas en el mes</h4>
							    <div class="stats-figure">$<?php echo number_format($totVentas,2); ?></div>
							    <div class="stats-meta <?php echo $colorVentas; ?>">
								    <?php echo $iconoVentas; ?>
										<?php echo $porcentageVentas; ?>%</div>
						    	</div><!--//app-card-body-->
						    	<a class="app-card-link-mask" href="#"></a>
					    </div><!--//app-card-->
				    </div><!--//col-->
				    
				    <div class="col-6 col-lg-3">
					    <div class="app-card app-card-stat shadow-sm h-100">
						    <div class="app-card-body p-3 p-lg-4">
							    <h4 class="stats-type mb-1">Gasto Mensual</h4>
							    <div class="stats-figure">$<?php echo number_format($montoGasto,2); ?></div>
							    <div class="stats-meta <?php echo $colorGasto; ?>">
								    <?php echo $iconoGasto; ?>
										<?php echo $porcentGasto; ?>%
									</div>
						    </div><!--//app-card-body-->
						    <a class="app-card-link-mask" href="#"></a>
					    </div><!--//app-card-->
				    </div><!--//col-->

				    <div class="col-6 col-lg-3">
					    <div class="app-card app-card-stat shadow-sm h-100">
						    <div class="app-card-body p-3 p-lg-4">
							    <h4 class="stats-type mb-1">Ventas del Dia</h4>
							    <div class="stats-figure">$<?php echo number_format($ventasHoy,2); ?></div>
							    <div class="stats-meta">
								  </div>
						    </div><!--//app-card-body-->
						    <a class="app-card-link-mask" href="#"></a>
					    </div><!--//app-card-->
				    </div><!--//col-->

				    <div class="col-6 col-lg-3">
					    <div class="app-card app-card-stat shadow-sm h-100">
						    <div class="app-card-body p-3 p-lg-4">
							    <h4 class="stats-type mb-1">Inventario Actual</h4>
							    <div class="stats-figure"><?php echo $artiActual; ?></div>
							    <div class="stats-meta">Articulos Disponibles</div>
						    </div><!--//app-card-body-->
						    <a class="app-card-link-mask" href="#"></a>
					    </div><!--//app-card-->
				    </div><!--//col-->
			    </div><!--//row-->

			    <div class="row g-4 mb-4">
						<div class="col-12 col-lg-6">
							<div class="app-card app-card-chart h-100 shadow-sm">
								<div class="app-card-header p-3">
									<div class="row justify-content-between align-items-center">
										<div class="col-auto">
											<h4 class="app-card-title">Ventas de la semana</h4>
										</div><!--//col-->
										<div class="col-auto">
											<div class="card-header-action">
												<!-- <a href="charts.html">More charts</a> -->

											</div><!--//card-header-actions-->
										</div><!--//col-->
									</div><!--//row-->
								</div><!--//app-card-header-->


								<div class="col-sm-12">
									<?php 

										// $hoy = date('N'); // Obtener el número del día de la semana actual

										// $diaSemana = ['1'=>'lunes', '2'=>'martes', '3'=>'miércoles', '4'=>'jueves', '5'=>'viernes', '6'=>'sábado', '7'=>'domingo'];
										// $semanaActual = [];
										// // echo $diaSemana[$hoy]."<br>";
										
										// $auxFec = date('Y-m-d');
										// for ($i = $hoy; $i <= 7; $i++) {
										// 	// echo $i;
										// 	// echo $diaSemana[$i] . "<br>";
										// 	$semanaActual[$diaSemana[$i]]=$auxFec;

										// 	$auxFec = date('Y-m-d', strtotime($auxFec. ' + 1 days'));
										// 	// echo $auxFec;
											
										// }

										// // print_r($semanaActual);

										// // for ($i = 1; $i < $hoy; $i++) {
										// // 	echo $diaSemana[$i-1] . "<br>";
										// // }
										// $auxFec = date('Y-m-d');
										// for ($i = $hoy; $i >= 1; $i--) {
										// 	echo $diaSemana[$i] . "<br>";
										// 	$semanaActual[$diaSemana[$i]]=$auxFec;
										// 	$auxFec = date('Y-m-d', strtotime($auxFec. ' - 1 days'));
										// }
										// asort($semanaActual);
										// print_r($semanaActual);

										// //Teniendo en cuenta lo anterior calcularemos la semana pasada
										// $semanaPasada = [];
										// $ultimoDia = $semanaActual['lunes'];
										// echo "<br> Ultio Dia, ".$ultimoDia;

										// $auxFec2 = $ultimoDia;
										// for($x = 7; $x >= 1; $x--){
										// 	$auxFec2 = date('Y-m-d', strtotime($auxFec2. ' - 1 days'));
										// 	$semanaPasada[$diaSemana[$x]] = $auxFec2;
										// 	// echo $diaSemana[$x]."<br>";
										// }//fin del for
										// echo "<br>=====<br>";
										// asort($semanaPasada);
										// print_r($semanaPasada);


										// $dias = ["1"=>"Lunes","2"=>"Martes","3"=>"Miercoles","4"=>"Jueves","5"=>"Viernes","6"=>"Sabado","7"=>"Domingo"];
										// $dia = date('N');
										// echo $dias[$dia];
									?>
								</div>

								<div class="app-card-body p-3 p-lg-4">
									<div class="chart-container">
										<canvas id="canvas-linechart" ></canvas>
									</div>
								</div><!--//app-card-body-->
							</div><!--//app-card-->
						</div><!--//col-->

			        <div class="col-12 col-lg-6">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">
							        <div class="col-auto">
						                <h4 class="app-card-title">Existencia de Productos</h4>
							        </div><!--//col-->
											<div class="col-auto">
								        <div class="card-header-action">
									        <a href="verProductos.php">Ver Inventario</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->
						        </div><!--//row-->
					        </div><!--//app-card-header-->
					        <div class="app-card-body p-3 p-lg-4">
										<div class="table-responsive">
											<table class="table table-borderless mb-0">
												<thead>
													<tr>
														<th class="meta">Producto</th>
														<th class="meta stat-cell">Sucursal</th>
														<th class="meta stat-cell">Existencia</th>
													</tr>
												</thead>
												<tbody>
													<?php
														// tomaremos los productos que tengan menos de 5 productos en existencia
														$sqlInve = "SELECT a.*,b.nombreArticulo,c.nombreSuc FROM ARTICULOSUCURSAL a INNER JOIN ARTICULOS b 
														ON a.articuloID = b.idArticulo INNER JOIN SUCURSALES c ON a.sucursalID = c.idSucursal 
														WHERE a.existenciaSucursal <= 5 AND b.empresaID = '$idEmpresaSesion' LIMIT 8";
														try {
															$queryInve = mysqli_query($conexion, $sqlInve);
															while($fetchInve = mysqli_fetch_assoc($queryInve)){
																$prod = $fetchInve['nombreArticulo'];
																$existencia = $fetchInve['existenciaSucursal'];
																$nombreSuc = $fetchInve['nombreSuc'];
																$idProdInve = $fetchInve['articuloID'];
																$claseInve = "";
																$claseInve2 = "";
																if($existencia <= 3){
																	$claseInve = "text-danger";
																	$claseInve2 = "color:#dc3545 !important;";
																}else{
																	$claseInve = "";
																	$claseInve2 = "color: black !important;";
																}

																echo "<tr>
																	<td class='$claseInve'>$prod</td>
																	<td class='stat-cell $claseInve'>$nombreSuc</td>
																	<td class='stat-cell $claseInve'>
																		<a href='verInfoProducto.php?infoProd=$idProdInve' style='$claseInve2'>
																			$existencia
																		</a>
																	</td>
																</tr>";
															}//fin del while
														} catch (\Throwable $th) {
															//error de consulta
															echo "error";
														}
													?>
												</tbody>
											</table>
										</div>
						        <?php 
											
										?>
					        </div><!--//app-card-body-->
				        </div><!--//app-card-->
			        </div><!--//col-->
			        
			    </div><!--//row-->


			    <div class="row g-4 mb-4">
				    <div class="col-12 col-lg-6">
				        <div class="app-card app-card-progress-list h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">
							        <div class="col-auto">
						                <h4 class="app-card-title">Productos Mas Vendidos</h4>
							        </div><!--//col-->
							        
						        </div><!--//row-->
					        </div><!--//app-card-header-->
					        <div class="app-card-body">
										<?php 
											$sql4 = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion'";
											$query4 = mysqli_query($conexion,$sql4);
											$sucursales = '';
											while($fetch4 = mysqli_fetch_assoc($query4)){
												$idSucursal = $fetch4['idSucursal'];
												if($sucursales == ""){
													$sucursales = $idSucursal;
												}else{
													$sucursales = $sucursales.",".$idSucursal;
												}
												
											}
											
											$sqlProd = "SELECT SUM(cantidadVenta) AS totales,
											(SELECT c.nombreArticulo FROM ARTICULOS c WHERE c.idArticulo = a.articuloID) AS nameArti FROM DETALLEVENTA a INNER JOIN SUCURSALES b 
											ON a.sucursalID = b.idSucursal WHERE a.sucursalID IN ($sucursales) group by articuloID";
											$queryProd = mysqli_query($conexion, $sqlProd);
											$totalesVentas = 0;

											while($fetch5 = mysqli_fetch_assoc($queryProd)){
												$totalesVentas = $totalesVentas + $fetch5['totales'];
											}
											// echo $totalesVentas;

											$sqlProd2 = "SELECT SUM(cantidadVenta) AS totales,
											(SELECT c.nombreArticulo FROM ARTICULOS c WHERE c.idArticulo = a.articuloID) AS nameArti FROM DETALLEVENTA a INNER JOIN SUCURSALES b 
											ON a.sucursalID = b.idSucursal WHERE a.sucursalID IN ($sucursales) group by articuloID ORDER BY totales DESC LIMIT 5";
											$queryProd2 = mysqli_query($conexion, $sqlProd2);

											while($fetch7 = mysqli_fetch_assoc($queryProd2)){
												$nombreProd = $fetch7['nameArti'];
												$ventasProd = $fetch7['totales'];
												$porcentaje = ($ventasProd / $totalesVentas) * 100;
												$porcentaje2 = number_format($porcentaje,2);
												echo '
												<div class="item p-3">
													<div class="row align-items-center">
														<div class="col">
															<div class="title mb-1 ">'.$nombreProd.' - '.$porcentaje2.'%</div>
															<div class="progress">
																<div class="progress-bar bg-success" role="progressbar" style="width: '.$porcentaje.'%;" aria-valuenow="'.$porcentaje.'" aria-valuemin="0" aria-valuemax="100"></div>
															</div>
														</div><!--//col-->

														<!--<div class="col-auto">
															<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
																<path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
															</svg>
														</div>-->
													</div><!--//row-->

													<a class="item-link-mask" href="#!"></a>
												</div><!--//item-->';
											}


										?>
							    	
							    
							    
							     
		
					        </div><!--//app-card-body-->
				        </div><!--//app-card-->
			        </div><!--//col-->


			        <div class="col-12 col-lg-6">
				        <div class="app-card app-card-stats-table h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">
							        <div class="col-auto">
						            <h4 class="app-card-title">Ultimas ventas realizadas</h4>
							        </div><!--//col-->
							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="reportesCaja.php">Ver Reportes</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->
						        </div><!--//row-->
					        </div><!--//app-card-header-->
					        <div class="app-card-body p-3 p-lg-4">
						        <div class="table-responsive">
							        <table class="table table-borderless mb-0">
												<thead>
													<tr>
														<th class="meta">Cajero</th>
														<th class="meta stat-cell">Sucursal</th>
														<th class="meta stat-cell">Monto</th>
													</tr>
												</thead>
												<tbody>
													<!-- <tr>
														<td><a href="#">google.com</a></td>
														<td class="stat-cell">110</td>
														<td class="stat-cell">
															<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up text-success" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									  						<path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
															</svg> 
									            30%
									          </td>
													</tr> -->
													<?php
														//realizamos la consulta para ver los vendedores
														$sqlVentas2 = "SELECT *,
														(SELECT d.nombreSuc FROM DETALLEVENTA c INNER JOIN SUCURSALES d ON c.sucursalID = d.idSucursal 
														WHERE c.ventaID = a.idVenta LIMIT 1) AS sucVenta FROM VENTAS a INNER JOIN USUARIOS b 
														ON a.usuarioID = b.idUsuario WHERE a.empresaID = '$idEmpresaSesion' ORDER BY a.idVenta DESC LIMIT 6";
														try {
															$queryVentas2 = mysqli_query($conexion, $sqlVentas2);
															if(mysqli_num_rows($queryVentas2) > 0){
																while($fetchVentas2 = mysqli_fetch_assoc($queryVentas2)){
																	$montoVenta = $fetchVentas2['totalVenta'];
																	$nombreVenta = $fetchVentas2['nombreUsuario']." ".$fetchVentas2['apPaternoUsuario']." ".$fetchVentas2['apMaternoUsuario'];
																	$sucVenta = $fetchVentas2['sucVenta'];

																	echo "<tr>
																	<td><a href='#!'>$nombreVenta</a></td>
																	<td class='stat-cell'>$sucVenta</td>
																	<td class='stat-cell'>$$montoVenta</td>
																	</tr>";
																	
																}//fin del while 2
															}else{
																//sin ventas registradas
															}
														} catch (\Throwable $th) {
															//ocurrio un error en la consulta de datos
														}
													?>
													
												</tbody>
											</table>
						        </div><!--//table-responsive-->
					        </div><!--//app-card-body-->
				        </div><!--//app-card-->
			        </div><!--//col-->

			    </div><!--//row-->


			    
			    
		    </div><!--//container-fluid-->
	    </div><!--//app-content-->
	    
	    <footer class="">
        <div class="container text-center py-3">
              <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
          <small class="copyright">Disenado por </i> by <a class="app-link" href="https://www.tecuanisoft.com" target="_blank">TecuaniSoft</a></small>
            
        </div>
      </footer><!--//app-auth-footer-->	
	    
    </div><!--//app-wrapper-->    					

 
    <!-- Javascript -->          
    <script src="assets/plugins/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>  

    <!-- Charts JS -->
    <script src="assets/plugins/chart.js/chart.min.js"></script> 
    <script src="assets/js/index-charts.js"></script> 
    
    <!-- Page Specific JS -->
    <script src="assets/js/app.js"></script> 

</body>
</html> 

