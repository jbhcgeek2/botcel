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

    //verificamos que exista el get
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Servicio Tecnicos</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verServicios.php">Registrar Servicio Tecnico</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      
                      <form id="dataModServ" class="row">
                        <input type="hidden" name="servData" id="servData" value="<?php echo  $idServicio; ?>">
                        <div class="col-sm-12 col-md-6 mb-3">
                          <label for="nombreServ" class="form-label">Servicio</label>
                          <input type="text" id="nombreServ" name="nombreServ" 
                          value='<?php echo $nombreServicio; ?>' class="form-control">
                        </div>
                        <div class="col-sm-12 col-md-3 mb-3">
                          <label for="precioFijo" class="form-label">Tipo de Precio</label>
                          <select name="precioFijo" id="precioFijo" class="form-select">
                            <option value="" disabled>Seleccione...</option>
                            <?php 
                              if($tipoPrecio == '1'){
                                echo '<option value="1" selected>Fijo</option>
                                <option value="0">Variable</option>';
                              }else{
                                echo '<option value="1">Fijo</option>
                                <option value="0" selected>Variable</option>';
                              }
                            ?>
                          </select>
                        </div>
                        <div class="col-sm-12 col-md-3 mb-3">
                          <label for="precioServ" class="form-label">Precio</label>
                          <input type="number" id="precioServ" <?php echo $clasePrecio; ?> 
                          value="<?php echo $precioServ; ?>" name="precioServ" class="form-control">
                        </div>

                        <div class="col-sm-12 col-md-4 mb-3">
                          <label for="catServicio" class="form-label">Categoria de Servicio</label>
                          <select name="catServicio" id="catServicio" class="form-select">
                            <option value=""selected disabled>Seleccione...</option>
                            <option value="newCatServ">Nueva Categoria</option>
                            <?php 
                              //consultamos las categorias de la empresa
                              $sqlCat = "SELECT * FROM CATEGORIASERVICIO WHERE empresaID = '$idEmpresaSesion'";
                              try {
                                $queryCat = mysqli_query($conexion, $sqlCat);
                                if(mysqli_num_rows($queryCat) > 0){
                                  while($fetchCat = mysqli_fetch_assoc($queryCat)){
                                    $nombreCat = $fetchCat['nombreCatServ'];
                                    $idCatServ = $fetchCat['idCategoriaServ'];

                                    if($catServ == $nombreCat){
                                      echo "<option value='$nombreCat' selected>$nombreCat</option>";
                                    }else{
                                      echo "<option value='$nombreCat'>$nombreCat</option>";
                                    }

                                    
                                  }//fin del while cat
                                }else{
                                  //sin categorias Registradas
                                  echo "<option value='noData'>Sin Categorias</option>";
                                }
                              } catch (\Throwable $th) {
                                //throw $th;
                                echo "<option value=''>Error de cosnulta</option>";
                              }
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-4">
                          <label for="estatusServ" class="form-label">Estatus</label>
                          <select name="estatusServ" id="estatusServ" class="form-select">
                            <option value="" disabled>Seleccione...</option>
                            <?php 
                              if($estatusServ == "1"){
                                echo "<option value='1' selected>Activo</option>
                                <option value='0'>Deshabilitado</option>";
                              }else{
                                //deshabilitado
                                echo "<option value='1'>Activo</option>
                                <option value='0' selected>Deshabilitado</option>";
                              }
                            ?>
                          </select>
                        </div>
                        

                        
                        
                      </form>
                      <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                        <a href="#!" class="btn btn-primary" role="buttom" id="updateServ">Actualizar</a>
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
</body>
</html> 

