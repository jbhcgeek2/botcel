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

    //verificamos la existencia del provedor
    $dataProv = $_GET['data'];
    //consultamos la existencia del proveedor
    $sql1 = "SELECT * FROM PROVEEDORES WHERE provEmpresaID = '$idEmpresaSesion' AND idProveedor = '$dataProv'";
    try {
      $query1 = mysqli_query($conexion, $sql1);
      if(mysqli_num_rows($query1) == 1){
        //si existe el proveedor
        $fetch1 = mysqli_fetch_assoc($query1);

        $nombreprov = $fetch1['nombreProveedor'];
        $telProv = $fetch1['telProveedor'];
        $mailProv = $fetch1['mailProveedor'];
        $dirProv = $fetch1['direccionProv'];
        $estatus = $fetch1['estatusProveedor'];
        
      }else{
        //no existe el proveedor
        ?>
        <script>
          window.location = 'verProveedores.php';
        </script>
        <?php
      }
    } catch (\Throwable $th) {
      //error en la consulta del proveedor
      ?>
      <script>
        window.location = 'verProveedores.php';
      </script>
      <?php
    }


    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Informacion de Proveedor</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verProveedores.php">Ver Proveedores</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoProv">
      
                    <div class="row">
                      <form id="dataProv" class="row">
                        <input type="hidden" name="dataProvEdit" id="dataProvEdit" value="<?php echo $dataProv; ?>">
                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                          <label for="nombreProvEdit" class="form-label">Nombre</label>
                          <input type="text" id="nombreProvEdit" name="nombreProvEdit" value="<?php echo $nombreprov; ?>" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-2 mb-3">
                          <label for="telProvEdit" class="form-label">Telefono</label>
                          <input type="text" id="telProvEdit" name="telProvEdit" value="<?php echo $telProv; ?>" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="mailProvEdit" class="form-label">Correo</label>
                          <input type="text" id="mailProvEdit" name="mailProvEdit" value="<?php echo $mailProv; ?>" class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                          <label for="estatusProvEdit" class="form-label">Estatus</label>
                          <select name="estatusProvEdit" id="estatusProvEdit" class="form-select">
                            <option value="-" disabled>Seleccione</option>
                            <?php 
                              if($estatus == 1){
                                echo "<option value='1' selected>Activo</option>
                                <option value='0'>Inactivo</option>";
                              }else{
                                echo "<option value='1'>Activo</option>
                                <option value='0' selected>Inactivo</option>";
                              }
                            ?>
                          </select>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                          <label for="dirProvEdit" class="form-label">Direccion</label>
                          <input type="text" id="dirProvEdit" name="dirProvEdit" value="<?php echo $dirProv; ?>" class="form-control">
                        </div>
                        

                      </form>
                      <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                        <a href="#!" id="btnUpdateProv" class="btn btn-primary">Actualizar</a>
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
    <script src="assets/js/verInfoProv.js"></script>
</body>
</html> 

