<?php 
  //operaciohnes de clientes


  //VER LA EMPRESA DEL USAURIO
  function datoEmpresaSesion($sesion,$dato){
    require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }

    //verificamos la empresa del usaurio
    $sql = "SELECT * FROM USUARIOS a INNER JOIN EMPRESAS b
    ON a.empresaID = b.idEmpresa WHERE a.userName = '$sesion'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) == 1){
        $fetch = mysqli_fetch_assoc($query);

        switch ($dato) {
          case 'id':
            $valor = $fetch['idEmpresa'];
            break;
          case 'nombre':
            $valor = $fetch['nombreEmpresa'];
            break;
          case 'saldoEfectivo':
            $valor = $fetch['saldoEfectivo'];
            break;
          case 'saldoTransferencia':
            $valor = $fetch['saldoEfectivo'];
            break;
          case 'logo':
            $valor = $fetch['imgLogoEmpresa'];
            break;
          
          default:
            $valor = "error";
            break;
        }
        
        $res = ["status"=>"ok","dato"=>$valor];
        return json_encode($res);
      }else{
        $res = ["status"=>"error","mensaje"=>"Empresa no localizada."];
        return json_encode($res);
      }
    }catch (Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"Error al consultar la informacion."];
      return json_encode($res);
    }
  }

  function getSucursalUsuario($sesion){
    require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }

    $sql = "SELECT * FROM USUARIOS a INNER JOIN SUCURSALES b ON a.sucursalID = b.idSucursal 
    WHERE a.userName = '$sesion'";
    try {
      $query = mysqli_query($conexion,$sql);
      $fetch = mysqli_fetch_assoc($query);

      $nombreSucur = $fetch['nombreSuc'];
      $res = ["status"=>"ok","dato"=>$nombreSucur];
      return json_encode($res);
    } catch (\Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"Error al consultar la informacion. ".$th];
      return json_encode($res);
    }

  }

  function usuarioExiste($usuario){
    require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }

    $sql = "SELECT userName FROM USUARIOS WHERE userName = '$usuario'";
    try {
      $query = mysqli_query($conexion, $sql);
      $data = "";
      if(mysqli_num_rows($query) > 0){
        //usuario existe
        $data = "usuarioExiste";
      }else{
        //el usuario no existe
        $data = "usuarioNoExiste";
      }
      $res = ["estatus"=>"ok","dato"=>$data];
    } catch (\Throwable $th) {
      //error al consultar el usuario
      $error = "Ha ocurrido un error: ".$th;
      $res = ["estatus"=>"error","dato"=>$error];
    }
    return json_encode($res);
  }

  function nuevoUsuario($empresa,$nombre,$paterno,$materno,$telUser,$correo,
  $userName,$pass,$sucursal,$rol){
    require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }
    $sql = "INSERT INTO USUARIOS (empresaID,nombreUsuario,apPaternoUsuario,apMaternoUsuario,
    telUsuario,correoUsuario,userName,passwordUser,sucursalID,rolID) VALUES ('$empresa',
    '$nombre','$paterno','$materno','$telUser','$correo','$userName','$pass','$sucursal','$rol')";
    try {
      $query = mysqli_query($conexion, $sql);
      $res = ["status"=>"ok","dato"=>"operationSuccess"];
      return json_encode($res);
    } catch (\Throwable $th) {
      //error en la base de datos
      return $th;
    }
    

  }

  function getDataUser($usuario,$empresa){
    require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }

    $sql = "SELECT * FROM USUARIOS WHERE userName = '$usuario' AND empresaID = '$empresa'";
    try {
      $query = mysqli_query($conexion, $sql);
      $fetch = mysqli_fetch_assoc($query);
      return json_encode($fetch);
    } catch (\Throwable $th) {
      //error en la consulta del usuario
      $error = "Ha ocurrido un error: ".$th;
      $res = ["estatus"=>"error","dato"=>$error];
      return json_encode($res);
    }
  }

  function verTipoUsuario($usuario){
    require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }

    $sql = "SELECT * FROM USUARIOS a INNER JOIN ROLES b ON a.rolID = b.idRol WHERE 
    a.userName = '$usuario'";
    try {
      $query = mysqli_query($conexion,$sql);
      $fetch = mysqli_fetch_assoc($query);
      $tipoRol = $fetch['nombreRol'];
      $res = ['status'=>"ok","data"=>$tipoRol];
      return json_encode($res);
    } catch (\Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar el tipo de rol: ".$th];
      return json_encode($res);
    }
  }

  function getSucById($idSucursal){
    require('conexion.php');
    $res = [];
    if(!$conexion){
      require('../conexion.php');
      if(!$conexion){
        require('../includes/conexion.php');
      }
    }

    $sql = "SELECT * FROM SUCURSALES WHERE idSucursal = '$idSucursal'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) > 0){
        $fetch = mysqli_fetch_assoc($query);
        $nombreSuc = $fetch['nombreSuc'];

        $res = ["status"=>"ok","dato"=>$nombreSuc];
        return json_encode($res);
      }else{
        //sin resultados
        $res = ["status"=>"ok","dato"=>"noData"];
        return json_encode($res);
      }
    } catch (\Throwable $th) {
      //error en la consulta
      $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar la sucursal: ".$th];
      return json_encode($res);
    }
  }

?>