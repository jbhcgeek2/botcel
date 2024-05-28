<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){

  require_once("usuarios.php");
  require_once("cliente.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;

  if(!empty($_POST['nombreCliente'])){
    //capturamos los datos del cliente
    $nombreCliente = $_POST['nombreCliente'];
    $telefono = $_POST['telefonoCliente'];
    $email = $_POST['emailCliente'];
    $direccion = $_POST['direccionCliente'];
    $rfcCliente = $_POST['rfcCliente'];
    $van = 0;

    if(empty($_POST['nombreCliente']) || empty($_POST['telefonoCliente'])){
      $van = 1;
    }

    if($van == 0){
      $altaCliente = altaCliente($nombreCliente,$telefono,$email,$direccion,$rfcCliente,$idEmpresaSesion);
      echo $altaCliente;
    }else{
      $res = ['status'=>'error','mensaje'=>'Verifica que los campos esten correctamente capturados'];
      echo json_encode($res);
    }
    
  }elseif(!empty($_POST['nombreClienteUpdate'])){
    //seccion para la actualizacion de clientes
    $idCliente = $_POST['clienteUpdate'];
    $nombre = $_POST['nombreClienteUpdate'];
    $telefono = $_POST['telefonoCliente'];
    $email = $_POST['emailCliente'];
    $direccion = $_POST['direccionCliente'];
    $rfc = $_POST['rfcCliente'];

    $actualiza = updateCliente($nombre,$direccion,$telefono,$email,$rfc,$idCliente);
    // $actualiza = json_decode($actualiza);

    echo $actualiza;

  }elseif(!empty($_POST['buscarCliente'])){
    $data = $_POST['buscarCliente'];
    

    $busqueda = buscarCliente($data,$idEmpresaSesion);
    echo $busqueda;
  }
}
?>