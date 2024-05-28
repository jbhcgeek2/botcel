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
    

    //verificamos la existencia de la categoria de servicios
    if(!empty($_GET['data'])){
      $idCatServ = $_GET['data'];

      //consultamos la categoria
      $sqlCatServ = "SELECT * FROM CATEGORIASERVICIO WHERE idCategoriaServ = '$idCatServ' AND empresaID = '$idEmpresaSesion'";
      try {
        $queryCatServ = mysqli_query($conexion,$sqlCatServ);
        if(mysqli_num_rows($queryCatServ) == 1){
          //si existe la categoria
          $fetchCatServ = mysqli_fetch_assoc($queryCatServ);

          $nombreCat = $fetchCatServ['nombreCatServ'];
          $statusCat = $fetchCatServ['estatusCategoriaServ'];
          $descripcion = $fetchCatServ['descripcionCategoriaServ'];

        }else{
          //categoria no localizable

        }
      } catch (\Throwable $th) {
        //throw $th;
      }
    }else{
      //lo redireccionamos

    }
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Registro de Categorias Servicio</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verCatServicio.php">Ver Categoria de Servicio</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      <form id="dataAltaServCat" class="row">
                        <input type="hidden" name="dataCatServUpate" id="dataCatServUpate" value="<?php echo $idCatServ; ?>">
                        <div class="col-sm-12 col-md-6 col-lg-8 mb-3">
                          <label for="nombreCatServUpdate" class="form-label">Categoria de Servicio</label>
                          <input type="text" id="nombreCatServUpdate" name="nombreCatServUpdate" class="form-control" 
                          value="<?php echo $nombreCat; ?>">
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="estatusCatServUpdate" class="form-label">Estatus Categoria</label>
                          <select name="estatusCatServUpdate" id="estatusCatServUpdate" class="form-select">
                            <option value=""selected disabled>Seleccione...</option>
                            <?php 
                              if($statusCat == 1){
                                echo '<option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>';
                              }else{
                                echo '<option value="1">Activo</option>
                                <option value="0" selected>Inactivo</option>';
                              }
                            ?>
                          </select>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-12 mb-3">
                          <label for="descripcionCatServUpdate" class="form-label">Descripcion</label>
                          <input type="text" id="descripcionCatServUpdate" name="descripcionCatServUpdate" class="form-control" 
                          value="<?php echo $descripcion;  ?>">
                        </div>
                        
                        
                      </form>
                      <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                        <a href="#!" class="btn btn-primary" role="buttom" id="btnUpdateCat">Actualizar</a>
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
    <script src="assets/js/verInfoCatServ.js"></script>
    
</body>
</html> 

