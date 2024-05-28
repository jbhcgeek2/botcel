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
    
    //seccion exclusiva para administradores
    if($rolUsuario != "Administrador"){
      //cargamos el js para redireccionar
      ?>
        <script>
          window.location = 'index.php';
        </script>
      <?php
    }else{
      //consultamos los datos de la empresa
      $sqlEmp = "SELECT * FROM EMPRESAS WHERE idEmpresa = '$idEmpresaSesion'";
      try {
        $queryEmp = mysqli_query($conexion, $sqlEmp);
        if(mysqli_num_rows($queryEmp) == 1){
          $fetchEmp = mysqli_fetch_assoc($queryEmp);

          $saldoEfe = $fetchEmp['saldoEfectivo'];
          $saldoTrans = $fetchEmp['saldoTransferencia'];

          $saldoTotal = $saldoEfe+$saldoTrans;
        }else{
          //empresa no locolizada
        }
      } catch (\Throwable $th) {
        //throw $th;
      }

      $fecha = date('Y-m-d');
      // Ahora consultamois los saldos de las sucursales
      $sqlSuc = "SELECT * FROM MOVCAJAS WHERE conceptoMov = '1' AND fechaMovimiento = '$fecha'";
      $querySuc = mysqli_query($conexion, $sqlSuc);
      $saldoSuc = 0;
      if(mysqli_num_rows($querySuc) > 0){
        while($fetchSuc = mysqli_fetch_assoc($querySuc)){
          $dinero = $fetchSuc['montoMov'];
          $saldoSuc = $saldoSuc+$dinero;
        }//fin del while
      }else{
        //definimos los saldo en 0, no hacemos nada
      }
    }
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Saldos de la cuenta</h1>
			    
			    <div class="row">
            <div class="col-6 col-lg-3 mb-3">
              <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                  <h4 class="stats-type mb-1">Saldo Total</h4>
                  <div class="stats-figure fw-bold">$<?php echo number_format($saldoTotal,2); ?></div>
                  <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
              </div><!--//col-->
            </div>

            <div class="col-6 col-lg-3 mb-3">
              <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                  <h4 class="stats-type mb-1">Saldo Efectivo</h4>
                  <div class="stats-figure">$<?php echo number_format($saldoEfe,2); ?></div>
                  <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
              </div><!--//col-->
            </div>

            <div class="col-6 col-lg-3 mb-3">
              <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                  <h4 class="stats-type mb-1">Saldo Bancos</h4>
                  <div class="stats-figure">$<?php echo number_format($saldoTrans,2); ?></div>
                  <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
              </div><!--//col-->
            </div>

            <div class="col-6 col-lg-3 mb-3">
              <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                  <h4 class="stats-type mb-1">Saldo Sucursales</h4>
                  <div class="stats-figure">$<?php echo number_format($saldoSuc,2); ?></div>
                  <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
              </div><!--//col-->
            </div>

            

          </div>
          

        <div class="row">
          <div class="col-sm-12">
            <div class="app-card app-card-stat shadow-sm h-100">
              <div class="app-card-body p-3">
                <h4>Movimientos de Cuenta</h4>

                <div class="row">
                  <div class="col-sm-12 col-md-4 offset-md-4 mb-4" style="text-align:center;">
                    <a href="#!" class="btn btn-primary" id="btnRegMov" 
                    data-bs-toggle="modal" data-bs-target="#modalMov">Registrar Movimiento</a>
                  </div>
                </div>

                <div class="row" style="height:400px; overflow-y:scroll;">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Monto</th>
                        <th>Descripcion</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        //consultamos los movimientos de caja
                        $sqlMovs = "SELECT * FROM MOVCAJAS a INNER JOIN CONCEPTOSMOV b 
                        ON a.conceptoMov = b.idConcepto INNER JOIN USUARIOS c 
                        ON a.usuarioMov = c.idUsuario WHERE a.empresaMovID = '$idEmpresaSesion'";
                        try {
                          $queryMovs = mysqli_query($conexion,$sqlMovs);
                          if(mysqli_num_rows($queryMovs) > 0){
                            while($fetchMov = mysqli_fetch_assoc($queryMovs)){
                              $montoMov = $fetchMov['montoMov'];
                              $fechaMov = $fetchMov['fechaMovimiento'];
                              $tipo = $fetchMov['tipoMov'];
                              $usuario = $fetchMov['usuarioMov'];
                              $desc = $fetchMov['observacionMov'];
                              $nombreUser = $fetchMov['nombreUsuario']." ".$fetchMov['apPaternoUsuario'];

                              echo "<tr>
                                <td>$fechaMov</td>
                                <td>$nombreUser</td>
                                <td>$".number_format($montoMov,2)."</td>
                                <td>$desc</td>
                              </tr>";

                            }//fin del while
                          }else{
                            //no se tienen movim8ientos
                          }
                        } catch (\Throwable $th) {
                          //throw $th;
                        }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        
        <div class="modal fade" id="modalMov">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5">Registrar Movimiento de Cuenta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <form id="regMov" class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                      <label for="tipoMovReg" class="form-label">Tipo de Movimiento</label>
                      <select name="tipoMovReg" id="tipoMovReg" class="form-select">
                        <option value="">Seleccione...</option>
                        <option value="Entrada">Entrada</option>
                        <option value="Salida">Salida</option>
                      </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                      <label for="metodoMovReg" class="form-label">Metodo de Movimiento</label>
                      <select name="metodoMovReg" id="metodoMovReg" class="form-select">
                        <option value="">Seleccione...</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Digitales">Medios Digitales</option>
                      </select>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-5 mb-3">
                      <label for="concepMovReg" class="form-label">Concepto de Movimiento</label>
                      <select name="concepMovReg" id="concepMovReg" class="form-select">
                        <option value="">Seleccione</option>
                        <?php 
                          // consultamos los tipos de movimiento
                          $sqlTiMov = "SELECT * FROM CONCEPTOSMOV WHERE estatusConcepto = '1'";
                          $queryTiMo = mysqli_query($conexion, $sqlTiMov);
                          while($fetchTiMov = mysqli_fetch_assoc($queryTiMo)){
                            $nombreTiMov = $fetchTiMov['nombreConcepto'];
                            $idTiMov = $fetchTiMov['idConcepto'];

                            echo "<option value='$idTiMov'>$nombreTiMov</option>";
                          }//fin del while
                        ?>
                      </select>
                    </div>

                    <div class="col-sm-12 col-md-8 col-lg-8 mb-3">
                      <label for="observMov" class="form-label">Observacion de Movimiento</label>
                      <input type="text" id="observMov" name="observMov" class="form-control">
                    </div>
                    
                    <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                      <label for="montoMovReg" class="form-label">Monto de Movimiento</label>
                      <input type="number" id="montoMovReg" name="montoMovReg" class="form-control">
                    </div> 

                    <!-- <div class="row">
                      <div class="col-sm-12 col-md-4 offset-md-4" style="text-align:center">
                        <a href="#!" class="btn btn-primary" id="btnRegMov">Registrar</a>
                      </div>
                    </div> -->

                  </form>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSendMov">Registrar</button>
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
    <script src="assets/js/saldos.js"></script>
</body>
</html> 

