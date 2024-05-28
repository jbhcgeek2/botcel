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

    //verificamos la existencia de la sucursal
    $dataSuc = $_GET['data'];
    if(!empty($dataSuc) && is_numeric($dataSuc)){
      $sql1 = "SELECT * FROM SUCURSALES WHERE idSucursal = '$dataSuc' && empresaSucId = '$idEmpresaSesion'";
      try {
        $query1 = mysqli_query($conexion, $sql1);
        if(mysqli_num_rows($query1) == 1){
          //si se localizo a la sucursal
          $fetchSuc = mysqli_fetch_assoc($query1);
          $nombreSuc = $fetchSuc['nombreSuc'];
          $calleSuc = $fetchSuc['calleSuc'];
          $telSuc = $fetchSuc['telefonoSuc'];
          $estatusSuc = $fetchSuc['estatusSuc'];

        }else{
          //no se localizo la sucursal, por lo que lo redirigimos
          header('Location: verSucursales.php');
          ?>
          <script>
            window.location = 'verSucursales.php';
          </script>
          <?php
        }
      } catch (\Throwable $th) {
        //error en la consulta
        header('Location: verSucursales.php');
        ?>
        <script>
          window.location = 'verSucursales.php';
        </script>
        <?php
      }
    }
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Informacion de Sucursal</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verSucursales.php">Ver Sucursales</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      <form id="dataSucursal" class="row">
                        <input type="hidden" name="dataSuc" id="dataSuc" value="<?php echo $dataSuc; ?>">
                        <div class="col-sm-12 col-md-4 mb-3">
                          <label for="nameSucursal" class="form-label">Nombre</label>
                          <input type="text" id="nameSucursal" name="nameSucursal" value="<?php echo $nombreSuc; ?>" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-4 mb-3">
                          <label for="domSuc" class="form-label">Domicilio Sucursal</label>
                          <input type="text" id="domSuc" name="domSuc" value="<?php echo $calleSuc; ?>" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-4 mb-3">
                          <label for="telSuc" class="form-label">Telefono</label>
                          <input type="text" id="telSuc" name="telSuc" value="<?php echo $telSuc; ?>" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-4 mb-3">
                          <label for="estatusSuc" class="form-label">Estatus</label>
                          <select name="estatusSuc" id="estatusSuc" class="form-select">
                            <option value="">Seleccione</option>
                            <?php 
                              if($estatusSuc == 1){
                                echo "<option value='1' selected disabled>Activa</option>
                                <option value='0'>Inactiva</option>";
                              }else{
                                echo "<option value='1'>Activa</option>
                                <option value='0' selected disabled>Inactiva</option>";
                              }
                            ?>
                          </select>
                        </div>

                      </form>
                      <div class="col-sm-12 col-md-4 col-lg-3 offset-md-4 text-center">
                        <a href="#!" class="btn btn-primary" id="updateSuc" role="button">Actualizar</a>
                      </div>
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
    <script src="assets/js/verInfoSuc.js"></script>
</body>
</html> 

