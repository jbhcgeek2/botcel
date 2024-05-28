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
    include("includes/cliente.php");

    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    // $idUsuario = json_decode($datosUsuario)->idUsuario;


    //solo el usuario administrador podra dar de alta nuevos usuarios
    if($rolUsuario != "Administrador"){
      //acceso denegado
      ?>
        <script>
          window.location = '../verUsuarioEmpr.php';
        </script>
      <?php
      
    }else{
      //cargamos la informacion del usuario

      $idUsuarioGet = $_GET['data'];

      if(!empty($idUsuarioGet)){
        $sqlUser = "SELECT * FROM USUARIOS WHERE idUsuario = '$idUsuarioGet' AND empresaID = '$idEmpresaSesion'";
        try {
          $queryUser = mysqli_query($conexion, $sqlUser);
          if(mysqli_num_rows($queryUser) == 1){
            //si se tienen datos de usuario
            $fetchUser = mysqli_fetch_assoc($queryUser);

            $nombreUser = $fetchUser['nombreUsuario'];
            $apPaternoUser = $fetchUser['apPaternoUsuario'];
            $apMaternoUser = $fetchUser['apMaternoUsuario'];
            $telUser = $fetchUser['telUsuario'];
            $mailUser = $fetchUser['correoUsuario'];
            $usuarioUser = $fetchUser['userName'];
            $contraUser = $fetchUser['passwordUser'];
            $sucursalUser = $fetchUser['sucursalID'];
            $tipoUser = $fetchUser['rolID'];
            $statusUser = $fetchUser['statusUsuario'];
          }else{
            //no se tienen datos
              ?>
              <script>
                window.location = '../verUsuarioEmpr.php';
              </script>
            <?php
          }
        } catch (\Throwable $th) {
          //error de consulta
          ?>
            <script>
              window.location = '../verUsuarioEmpr.php';
            </script>
          <?php
        }
      }else{
        //no se tienen datos
        ?>
        <script>
          window.location = '../verUsuarioEmpr.php';
        </script>
      <?php
      }
    }

    

    
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
          <div class="col-12 col-lg-12">
            <div class="app-card h-100 shadow-sm">
              <div class="app-card-header p-3">
                <div class="row justify-content0between align-items0center">
                  <h4 class="app-card-title">Registro de usuarios</h4>
                </div>
              </div><!--app-header-->
              
              

              <div class="app-card-body p-3 p-lg-4">
                <div class="row">
                  <form id="modUsuarioEmp">
                    <div class="row">
                      <input type="hidden" name="usuarioMod" id="usuarioMod" value="<?php echo $idUsuarioGet; ?>">

                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="nombreUser" class="form-label">Nombres</label>
                        <input type="text" id="nombreUser" name="nombreUser" class="form-control" value="<?php echo $nombreUser; ?>">
                      </div>
                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="apPaterno" class="form-label">Apellido Paterno</label>
                        <input type="text" id="apPaterno" name="apPaterno" class="form-control" value="<?php echo $apPaternoUser; ?>">
                      </div>
                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="apMaterno" class="form-label">Apellido Materno</label>
                        <input type="text" id="apMaterno" name="apMaterno" class="form-control" value="<?php echo $apMaternoUser; ?>">
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                        <label for="telUser" class="form-label">Telefono</label>
                        <input type="text" id="telUser" name="telUser" class="form-control" value="<?php echo $telUser ?>">
                      </div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                        <label for="mailUser" class="form-label">Correo</label>
                        <input type="text" id="mailUser" name="mailUser" class="form-control" value="<?php echo $mailUser; ?>">
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                        <label for="userName" class="form-label">Usuario</label>
                        <input type="text" id="userName" name="userName" class="form-control" readonly value="<?php echo $usuarioUser; ?>">
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                        <label for="passwordUser" class="form-label">Contrasena</label>
                        <input type="password" id="passwordUser" name="passwordUser" class="form-control" value="<?php echo $contraUser; ?>">
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                        <label for="scUsuario" class="form-label">Sucursal</label>
                        <select name="scUsuario" id="scUsuario" class="form-select">
                          <option value="" disabled>Seleccione</option>
                          <?php 
                            //consuiltamos las sucursales de la empresa
                            $sqlSucs = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion' 
                            AND estatusSuc = '1'";
                            $querySucs = mysqli_query($conexion, $sqlSucs);
                            while($fetchSucs = mysqli_fetch_assoc($querySucs)){
                              $idSucs = $fetchSucs['idSucursal'];
                              $nombreSuc = $fetchSucs['nombreSuc'];
                              if($idSucs == $sucursalUser){
                                echo "<option value='$idSucs' selected>$nombreSuc</option>";
                              }else{
                                echo "<option value='$idSucs'>$nombreSuc</option>";
                              }
                            }//fin del while
                          ?>
                        </select>
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                        <label for="tipoUser" class="form-label">Tipo de Usuario</label>
                        <select name="tipoUser" id="tipoUser" class="form-select">
                          <option value="" selected disabled>Seleccione</option>
                          <?php 
                            //consuiltamos las sucursales de la empresa
                            $sqlTU = "SELECT * FROM ROLES";
                            $queryTU = mysqli_query($conexion, $sqlTU);
                            while($fetchTU = mysqli_fetch_assoc($queryTU)){
                              $idTipo = $fetchTU['idRol'];
                              $nombreTipo = $fetchTU['nombreRol'];

                              if($idTipo == $tipoUser){
                                echo "<option value='$idTipo' selected>$nombreTipo</option>";
                              }else{
                                echo "<option value='$idTipo'>$nombreTipo</option>";
                              }

                              
                            }//fin del while
                          ?>
                        </select>
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                        <label for="estatusUser" class="form-label">Estatus Usuario</label>
                        <select name="estatusUser" id="estatusUser" class="form-select">
                          <option value="" selected disabled>Seleccione</option>
                          <?php 
                            //consuiltamos las sucursales de la empresa
                            if($statusUser == 1){
                              echo "<option value='1' selected>Activo</option>
                              <option value='0'>Baja</option>";
                            }else{
                              echo "<option value='1'>Activo</option>
                              <option value='0' selected>Baja</option>";
                            }
                          ?>
                        </select>
                      </div>

                      

                    </div><!--Fin row form-->

                    <div class="row" style="text-align:center;">
                      <div class="col-sm-12 col-md-4 offset-md-4">
                        <a href="#!" class="btn btn-success" id="btnModUser">Actualizar</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            
          </div>
        </div><!--container-xl-->
      </div><!--app-content-->

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
    <script src="assets/js/modUserEmp.js"></script>

</body>
</html> 

