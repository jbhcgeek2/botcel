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
    include("includes/articulos.php");
    include("includes/conexion.php");

    //validamos la existencia del productoId
    if(!empty($_GET['infoProd'])){
      $idProd = $_GET['infoProd'];
      //tenemos que corroborar que el producto sea de la empresa
      $datosProd = json_decode(getInfoproducto($idEmpresaSesion,$idProd));
      // if($datosProd->status == "ok")
      // print_r($datosProd);
      if($datosProd->status == "ok"){
        $error = "no";
        // $datos = json_decode($datosProd->data);
        // echo count($datosProd->data);
        // print_r($datosProd);
      }else{
        //ocurrio un error al consultar la informacion
        $error = "si";
        
        
      }

    }else{
      //si no se detecta nada de valores, lo mandamos a volar
      $error = "si";
    }
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <!-- <h1 class="app-page-title">Productos</h1> -->
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title">Informacion del Producto</h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verProductos.php">Ver Productos</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoForm">
      
                    <form id="dataProducto">
                      <div class="row">

                      <div class="col-sm-12 col-md-3 mb-3 text-center">
                        <?php 
                          ///mostramos la imagen del producto, tartaremos que esta imagen 
                          //este en formato cuadrado 1:1 
                          if(empty($datosProd->data->imgArticulo)){
                            echo "<img src='../assets/images/no-image-available.jpeg' class='imagenProducto'>";
                          }else{
                            echo "<img src='".$datosProd->data->imgArticulo."' class='imagenProducto'>";
                          }
                        ?>
                      </div>

                      <div class="col-sm-12 col-md-9  mb-3">

                        <input type="hidden" name="dataProd" id="dataProd" value="<?php echo $datosProd->data->idArticulo; ?>">
                        <input type="hidden" name="imgProd" id="imgProd" value ="<?php echo $datosProd->data->imgArticulo; ?>">

                        <div class="row g-4 mb-3">
                          <div class="col-sm-12 mb-3">
                            <label for="nombreArticulo" class="form-label">Nombre Producto</label>
                            <input type="text" class="form-control required" required placeholder="Nombre Articulo" 
                            name="nombreArticulo" id="nombreArticulo" value="<?php echo $datosProd->data->nombreArticulo; ?>">
                            <div class="invalid-feedback">Ingrese un nombre</div>
                          </div>
                          

                          <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                            <label for="precioMenudeo" class="form-label">Precio de Menudeo</label>
                            <input type="number" class="form-control required" name="precioMenudeo" id="precioMenudeo"
                            value="<?php echo $datosProd->data->precioUnitario; ?>">
                          </div>
                          <div class=" col-sm-12 col-md-4 col-lg-3 mb-3">
                            <label for="precioMayoreo" class="form-label">Precio de Mayoreo</label>
                            <input type="number" class="form-control required" name="precioMayoreo" id="precioMayoreo" 
                            value="<?php echo $datosProd->data->precioMayoreo; ?>">
                          </div>
                          <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                            <label for="mayoreoDesde" class="form-label">Mayoreo Desde</label>
                            <input type="number" class="form-control required" name="mayoreoDesde" id="mayoreoDesde" 
                            value="<?php echo $datosProd->data->mayoreoDesde; ?>">
                          </div>
                          <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                            <label for="estatus" class="form-label">Estatus</label>
                            <select name="estatus" id="estatus" class="form-select required">
                              <option value="" selected disabled>Seleccione...</option>
                              <?php 
                                if($datosProd->data->estatusArticulo == 1){
                                  echo '<option value="1" selected>Activo</option><option value="NA">Inactivo</option>';
                                }else{
                                  echo '<option value="1">Activo</option><option value="NA" selected>Inactivo</option>';
                                  
                                }
                              ?>
                              
                              <!-- <option value="NA">Inactivo</option> -->
                            </select>
                          </div>

                          <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select name="categoria" id="categoria" class="form-select form-control required">
                              <option value="" selected disabled>Seleccione...</option>
                              <?php 
                                $sqlCat = "SELECT * FROM CATEGORIA WHERE empresaID = '$idEmpresaSesion' AND estatusCategoria = '1'";
                                try {
                                  $queryCat = mysqli_query($conexion, $sqlCat);
                                  $numCats = mysqli_num_rows($queryCat);
                                  while($fetchCat = mysqli_fetch_assoc($queryCat)){
                                    $nombreCat = $fetchCat['nombreCategoria'];
                                    $idCat = $fetchCat['idCategoria'];
                                    $idCatAtual = $datosProd->data->categoriaID;
                                    if($idCat == $idCatAtual){
                                      echo "<option value='$idCatAtual' selected>$nombreCat</option>";
                                    }else{
                                      echo "<option value='$idCat'>$nombreCat</option>";
                                    }
                                  }//fin del while
                                } catch (\Throwable $th) {
                                  echo "<option value='' selected disabled>Error de BD</option>";
                                }
                              ?>
                            </select>
                          </div>
                          <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                            <label for="codigo" class="form-label">Codigo</label>
                            <input type="text" class="form-control" id="codigoProducto" 
                            name="codigoProducto" value="<?php echo $datosProd->data->codigoProducto; ?>">
                          </div>

                          <div class="col-sm-12 col-md-6 mb-3">
                            <label for="proveedor" class="form-label">Proveedor</label>
                            <select name="proveedor" id="proveedor" class="form-select form-control required">
                              <?php 
                                //consultamos los proveedores de la empresa
                                $sqlProv = "SELECT * FROM PROVEEDORES WHERE provEmpresaID = '$idEmpresaSesion' 
                                AND estatusProveedor = '1'";
                                try {
                                  $queryProv = mysqli_query($conexion, $sqlProv);
                                  while($fetchProv = mysqli_fetch_assoc($queryProv)){
                                    $nombreProv = $fetchProv['nombreProveedor'];
                                    $idProv = $fetchProv['idProveedor'];

                                    if($idProv == $datosProd->data->proveedorID){
                                      echo "<option value='$idProv' selected>$nombreProv</option>";
                                    }else{
                                      echo "<option value='$idProv'>$nombreProv</option>";
                                    }

                                    
                                  }//fin del while
                                } catch (\Throwable $th) {
                                  
                                }
                              ?>
                            </select>
                          </div>

                        </div>

                        

                        <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                          <label for="descripcion" class="form-label">Descripcion</label>
                          <input type="text" class="form-control required" name="descripcion" id="descripcion" 
                          value="<?php echo $datosProd->data->descripcionArticulo; ?>">
                        </div>
                      </div>

                      <div class="input-group mb-3">
                        <input type="file" class="form-control" id="imagenProducto" name="imagenProducto" placeholder="Imagen del articulo">
                        <label class="input-group-text" for="imagenProducto">Actualizar Imagen</label>
                      </div>

                      <?php 
                        if($rolUsuario == "Administrador"){
                          ?>
                          <div class="row">
                            <div class="col-sm-12" style="text-align:center;">
                              <a href="#!" type="button" class="btn btn-primary w-25" id="btnUpdateProd">Actualizar</a>
                            </div>
                          </div>
                          <?php
                        }
                      ?>
                      

                        
                        <hr class="my-4">

                        <p class="text-center">Para modificar las cantidades de productos tendra que indicar una salida o entrada desde el menu lateral</p>

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
                              $cantidadSuc = getArtiSucursal($idSuc,$idProd);
                              $cantidadSuc = json_decode($cantidadSuc);
                              $cantidad = $cantidadSuc->data;
                              echo "<div class='row'>
                                <div class='col-sm-12 col-md-8 col-lg-4 mb-3'>
                                  <label for='nombreSuc$idSuc' class='form-label'>Nombre Sucursal</label>
                                  <input type='text' class='form-control' name='nombreSuc$idSuc' id='nombreSuc$idSuc' value='$nombreSucursal' readonly>
                                </div>
                                <div class='col-sm-12 col-md-4 col-lg-3 mb-3'>
                                  <label for='cantidadSuc$idSuc' class='form-label'>Existencia en Sucursal</label>
                                  <input type='number' class='form-control' name='cantidadSuc$idSuc' id='cantidadSuc$idSuc' value='$cantidad' readonly>
                                </div>
                              </div>";
                            }//fin del for
                          }else{
                            //error al consultar las sucursales
                          }
                        ?>

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
    <script src="assets/js/updateProducto.js"></script>

    <?php
    if($error == "si"){
      ?>
      <script>
        Swal.fire(
          'Ha ocurrido un error',
          'No fue posible consultar la informacion del producto.',
          'error'
        ).then(function(){
          window.location = "verProductos.php";
        })
      </script>
      <?php
    }
    ?>

</body>
</html> 

