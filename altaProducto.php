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
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Registrar Producto</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title">Captura la informacion del Nuevo Producto</h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="productos.php">Ver Productos</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoForm">
      
                    <form id="dataProducto">
                      <div class="row">

                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                          <label for="nombreArticulo" class="form-label"><strong class="text-warning">*</strong> Nombre Producto</label>
                          <input type="text" class="form-control required" required placeholder="Nombre Articulo" name="nombreArticulo" id="nombreArticulo">
                          <div class="invalid-feedback">Ingrese un nombre</div>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="precioMenudeo" class="form-label"><strong class="text-warning">*</strong> Precio de Menudeo</label>
                          <input type="number" class="form-control required" name="precioMenudeo" id="precioMenudeo">
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="precioMayoreo" class="form-label"><strong class="text-warning">*</strong> Precio de Mayoreo</label>
                          <input type="number" class="form-control required" name="precioMayoreo" id="precioMayoreo">
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                          <label for="mayoreoDesde" class="form-label"><strong class="text-warning">*</strong> Mayoreo Desde</label>
                          <input type="number" class="form-control required" name="mayoreoDesde" id="mayoreoDesde">
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                          <label for="estatus" class="form-label"><strong class="text-warning">*</strong> Estatus</label>
                          <select name="estatus" id="estatus" class="form-select required">
                            <option value="" selected disabled>Seleccione...</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="codigoProducto" class="form-label">Codigo</label>
                          <input type="text" class="form-control required" name="codigoProducto" id="codigoProducto">
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-3">
                          <label for="provProducto" class="form-label">Proveedor</label>
                          <select name="provProducto" id="provProducto" class="form-select">
                            <option value="" selected disabled>Seleccione</option>
                            <?php 
                              $sqlProv = "SELECT * FROM PROVEEDORES WHERE provEmpresaID  = '$idEmpresaSesion' 
                              AND estatusProveedor  = '1'";
                              try {
                                $queryProv = mysqli_query($conexion, $sqlProv);
                                if(mysqli_num_rows($queryProv) > 0){
                                  while($fetchProv = mysqli_fetch_assoc($queryProv)){
                                    $nombreProc = $fetchProv['nombreProveedor'];
                                    $idProv = $fetchProv['idProveedor'];

                                    echo "<option value='$idProv'>$nombreProc</option>";
                                  }//fin del while
                                  
                                }else{
                                  //sin proveedores registrados
                                  echo "<option value='1'>Generico</option>";
                                }
                              } catch (\Throwable $th) {
                                //throw $th;
                              }
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                          <label for="categoria" class="form-label"><strong class="text-warning">*</strong> Categoria</label>
                          <select name="categoria" id="categoria" class="form-select required">
                            <option value="" selected disabled>Seleccione...</option>
                            <?php 
                              $sqlCat = "SELECT * FROM CATEGORIA WHERE empresaID = '$idEmpresaSesion' AND estatusCategoria = '1'";
                              try {
                                $queryCat = mysqli_query($conexion, $sqlCat);
                                $numCats = mysqli_num_rows($queryCat);
                                while($fetchCat = mysqli_fetch_assoc($queryCat)){
                                  $nombreCat = $fetchCat['nombreCategoria'];
                                  $idCat = $fetchCat['idCategoria'];
                                  echo "<option value='$idCat'>$nombreCat</option>";

                                }//fin del while
                              } catch (\Throwable $th) {
                                echo "<option value='' selected disabled>Error de BD</option>";
                              }
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-5 mb-3">
                          <label for="descripcion" class="form-label"><strong class="text-warning">*</strong> Descripcion</label>
                          <input type="text" class="form-control required" name="descripcion" id="descripcion">
                        </div>
                        <hr class="my-4">

                        <p class="text-center">Indique las cantidades con las que cuenta en cada sucursal</p>

                        <?php 
                          //auxiliar para validar las categorias e indicarle que tiene que
                          //registrar categorias antes de continuar
                          if($numCats == 0){
                            echo "<input type='hidden' id='contentCats' value='noContiene'>";
                          }else{
                            echo "<input type='hidden' id='contentCats' value='siContiene'>";
                          }


                          //consultamos las sucursales
                          
                          $sucursales = verSucursales($usuario,'');
                          $sucursales = json_decode($sucursales);
                          if($sucursales->estatus ==  "ok"){
                            //print_r($sucursales->dato);
                            for($x = 0; $x  < count($sucursales->dato); $x++){
                              $nombreSucursal = $sucursales->dato[$x]->nombreSuc;
                              $idSuc = $sucursales->dato[$x]->idSucursal;
                              echo "<div class='row'>
                                <div class='col-sm-12 col-md-8 col-lg-4 mb-3'>
                                  <label for='nombreSuc$idSuc' class='form-label'>Nombre Sucursal</label>
                                  <input type='text' class='form-control' name='nombreSuc$idSuc' id='nombreSuc$idSuc' value='$nombreSucursal' readonly>
                                </div>
                                <div class='col-sm-12 col-md-4 col-lg-3 mb-3'>
                                  <label for='cantidadSuc$idSuc' class='form-label'>Existencia en Sucursal</label>
                                  <input type='number' class='form-control' name='cantidadSuc$idSuc' id='cantidadSuc$idSuc'>
                                </div>
                              </div>";
                            }//fin del for
                          }else{
                            //error al consultar las sucursales
                          }
                        ?>


                        <div class="input-group mb-3">
                          <input type="file" class="form-control" id="imagenProducto" name="imagenProducto" placeholder="Imagen del articulo">
                          <label class="input-group-text" for="imagenProducto">Imagen de Articulo</label>
                        </div>

                      </div>
                      

                      <div class="row">
                        <a href="#!" type="button" class="btn btn-primary w-25" id="btnSaveProducto">Guardar</a>
                        
                      </div>
                    </form>

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
    <script src="assets/js/altaProducto.js"></script>

</body>
</html> 

