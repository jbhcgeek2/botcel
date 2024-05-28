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

    if($rolUsuario == "Administrador" || $rolUsuario == "Encargado"){
      
    }else{
      ?>
      <script>
        window.location = "../";
      </script>
      <?php
    }
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Listado de Categorias</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="altaCategoria.php">Registrar Categoria</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Categoria</th>
                          <th>Estatus</th>
                          <th>Productos Asignados</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          //consultamos las categorias de la empresa
                          $sqlCat = "SELECT a.*,(SELECT COUNT(*) FROM ARTICULOS b WHERE b.empresaID = a.empresaID 
                          AND b.categoriaID = a.idCategoria) AS artiByCat FROM CATEGORIA a WHERE a.empresaID = '$idEmpresaSesion'";
                          try {
                            $queryCat = mysqli_query($conexion, $sqlCat);
                            if(mysqli_num_rows($queryCat) > 0){
                              while($fetchCat = mysqli_fetch_assoc($queryCat)){
                                $nombreCat = $fetchCat['nombreCategoria'];
                                $estatusCat = $fetchCat['estatusCategoria'];
                                $articulos = $fetchCat['artiByCat'];
                                if($estatusCat == 1){
                                  $estatusCat = "Activo";
                                }else{
                                  $estatusCat = "Baja";
                                }

                                echo "<tr>
                                  <td>$nombreCat</td>
                                  <td>$estatusCat</td>
                                  <td>$articulos</td>
                                  <td>
                                    <a href='#!' class='btn btn-success'>Ver</a>
                                  </td>
                                </tr>";
                              }//fin del while
                            }else{
                              //sin resultados
                            }
                          } catch (\Throwable $th) {
                            //throw $th;
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
    <script src="assets/js/altaProducto.js"></script>
</body>
</html> 


