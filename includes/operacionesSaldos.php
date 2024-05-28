<?php 
session_start();

if(!empty($_SESSION['usuarioPOS'])){

  require_once("usuarios.php");
  require_once("conexion.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;

  $dataUSer = getDataUser($usuario,$idEmpresaSesion);
  $dataUSer = json_decode($dataUSer);
  $idSucursalN = $dataUSer->sucursalID;
  $idUsuario = $dataUSer->idUsuario;

  if(!empty($_POST['tipoMov'])){
    //seccion para registrar un movimiento de cuenta
    $tipo = $_POST['tipoMov'];
    $concepto = $_POST['concepMov'];
    $observ = $_POST['observMov'];
    $montoMov = $_POST['montoMov'];
    $metodo = $_POST['metodoMov'];
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    if($metodo == "Efectivo"){
      $campo = 'saldoEfectivo';
      $campoAnt = 'saldoEfeAnterior';
    }else{
      $campo = 'saldoTransferencia';
      $campoAnt = 'saldoTransAnterior';
    }

    // antes de procesar el movimiento, verificamos que si es retiro
    //cuente con el saldo suficiente
    if($tipo == "Salida"){
      $sqlEmp = "SELECT $campo FROM EMPRESAS WHERE idEmpresa = '$idEmpresaSesion'";
      try {
        $queryEmp = mysqli_query($conexion, $sqlEmp);
        $fetchEmp = mysqli_fetch_assoc($queryEmp);
        $saldoCuenta = $fetchEmp[$campo];
        if($saldoCuenta >= $montoMov){
          //si se puede procvesar el movimiento
          $nuevoSaldo = $saldoCuenta - $montoMov;
          //primero insertamos el movimiento
          $sql1 = "INSERT INTO MOVCAJAS (fechaMovimiento,horaMovimiento,usuarioMov,montoMov,conceptoMov,
          observacionMov,sucursalMovID,tipoMov,empresaMovID) VALUES ('$fecha','$hora','$idUsuario',
          '$montoMov','$concepto','$observ','$idSucursalN','S','$idEmpresaSesion')";
          try {
            $query1 = mysqli_query($conexion, $sql1);
            //se inserto el movimiento, ahora descontamos ese saldo
            $sql2 = "UPDATE EMPRESAS SET $campo = '$nuevoSaldo', $campoAnt = '$saldoCuenta' 
            WHERE idEmpresa = '$idEmpresaSesion'";
            try {
              $query2 = mysqli_query($conexion, $sql2);
              //hasta aqui podemos dar por terminado el proceso de rehistro
              $res = ['status'=>'ok','mensaje'=>'operationComplete'];
              echo json_encode($res);
            } catch (\Throwable $th) {
              //si fallo en esta partte diremos que contacte a soporte tecnico
              $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error, contacte a soporte tecnico'];
              echo json_encode($res);
            }
          } catch (\Throwable $th) {
            $res = ['status'=>'error','mensaje'=>'Ocurrio un error al procesar el movimiento: '.$th];
            echo json_encode($res);
          }
        }else{
          //el monto a retirar el mayor al disponible
          $res = ['status'=>'error','mensaje'=>'El monto a retirar es superior al disponible.'];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        //error de sonulata a la base 
        $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar los saldos'];
        echo json_encode($res);
      }
    }else{
      //se trata de una entrada, aqui no importa la validacion del saldo
      //simplemente lo sumamos
      $sqlEmp = "SELECT $campo FROM EMPRESAS WHERE idEmpresa = '$idEmpresaSesion'";
      try {
        $queryEmp = mysqli_query($conexion, $sqlEmp);
        $fetchEmp = mysqli_fetch_assoc($queryEmp);
        $saldoCuenta = $fetchEmp[$campo];

        $nuevoSaldo = $saldoCuenta+$montoMov;
        //realizamos la insercion del movimiento
        $sql1 = "INSERT INTO MOVCAJAS (fechaMovimiento,horaMovimiento,usuarioMov,montoMov,conceptoMov,
        observacionMov,sucursalMovID,tipoMov,empresaMovID) VALUES ('$fecha','$hora','$idUsuario',
        '$montoMov','$concepto','$observ','$idSucursalN','E','$idEmpresaSesion')";
        try {
          $query1 = mysqli_query($conexion, $sql1);
          //ahora actualizamos el datos
          $sql2 = "UPDATE EMPRESAS SET $campo = '$nuevoSaldo', $campoAnt = '$saldoCuenta' WHERE 
          idEmpresa = '$idEmpresaSesion'";
          try {
            $query2 = mysqli_query($conexion, $sql2);
            //podemos dar por concluiodo
            $res = ['status'=>'ok','mensaje'=>'operationComplete'];
            echo json_encode($res);
          } catch (\Throwable $th) {
            $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error, contacte a soporte tecnico.'];
            echo json_encode($res);
          }
        } catch (\Throwable $th) {
          $res = ['status'=>'error','mensaje'=>'Ocurrio un error al procesar el movimiento.'];
          echo json_encode($res);
        }
      }catch (\Throwable $th) {
        $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar los saldos'];
        echo json_encode($res);
      }
    }
  }

}
?>