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

