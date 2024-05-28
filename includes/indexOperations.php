
<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){

  if(!empty($_POST['getVentasWeek'])){
    include("conexion.php");
    include("usuarios.php");

    $usuario = $_SESSION['usuarioPOS'];

    $empresa = datoEmpresaSesion($usuario,"id");
		$empresa = json_decode($empresa);
		$idEmpresaSesion = $empresa->dato;

    $hoy = date('N'); // Obtener el número del día de la semana actual

    $diaSemana = ['1'=>'lunes', '2'=>'martes', '3'=>'miercoles', '4'=>'jueves', '5'=>'viernes', '6'=>'sabado', '7'=>'domingo'];
    $semanaActual = [];
    $datoSemActual = [];
    $datoSemPasada = [];
    
    // echo $diaSemana[$hoy]."<br>";
    
    $auxFec = date('Y-m-d');
    for ($i = $hoy; $i <= 7; $i++) {
      // echo $i;
      // echo $diaSemana[$i] . "<br>";
      $semanaActual[$diaSemana[$i]]=$auxFec;

      $sql = "SELECT SUM(totalVenta) AS ventasDia FROM VENTAS WHERE 
      fechaVenta = '$auxFec' AND empresaID = '$idEmpresaSesion'";
      $query = mysqli_query($conexion, $sql);
      $fetch = mysqli_fetch_assoc($query);
      $ventas = $fetch['ventasDia'];
      if($ventas == NULL){
        $ventas = 0;
      }
      $datoSemActual[$auxFec] = $ventas;

      $auxFec = date('Y-m-d', strtotime($auxFec. ' + 1 days'));
      // echo $auxFec;
      
    }

    // print_r($semanaActual);

    // for ($i = 1; $i < $hoy; $i++) {
    // 	echo $diaSemana[$i-1] . "<br>";
    // }
    $auxFec = date('Y-m-d');
    for ($i = $hoy; $i >= 1; $i--) {
      // echo $diaSemana[$i] . "<br>";
      //consultamos las ventas del dia

      $sql2 = "SELECT SUM(totalVenta) AS ventasDia FROM VENTAS WHERE 
      fechaVenta = '$auxFec' AND empresaID = '$idEmpresaSesion'";
      $query2 = mysqli_query($conexion, $sql2);
      $fetch2 = mysqli_fetch_assoc($query2);
      $ventas2 = $fetch2['ventasDia'];
      if($ventas2 == NULL){
        $ventas2 = 0;
      }
      $datoSemActual[$auxFec] = $ventas2;
      
      $semanaActual[$diaSemana[$i]]=$auxFec;
      $auxFec = date('Y-m-d', strtotime($auxFec. ' - 1 days'));
    }

    $semanaActual = $semanaActual;
    // print_r($semanaActual);

    //Teniendo en cuenta lo anterior calcularemos la semana pasada
    $semanaPasada = [];
    $ultimoDia = $semanaActual['lunes'];
    // echo "<br> Ultio Dia, ".$ultimoDia;

    $auxFec2 = $ultimoDia;
    for($x = 7; $x >= 1; $x--){
      $auxFec2 = date('Y-m-d', strtotime($auxFec2. ' - 1 days'));
      $semanaPasada[$diaSemana[$x]] = $auxFec2;

      $sql3 = "SELECT SUM(totalVenta) AS ventasDia FROM VENTAS WHERE 
      fechaVenta = '$auxFec2' AND empresaID = '$idEmpresaSesion'";
      $query3 = mysqli_query($conexion, $sql3);
      $fetch3 = mysqli_fetch_assoc($query3);
      $ventas3 = $fetch3['ventasDia'];
      if($ventas3 == NULL){
        $ventas3 = 0;
      }
      $datoSemPasada[$auxFec2] = $ventas3;

      // echo $diaSemana[$x]."<br>";
    }//fin del for
    // echo "<br>=====<br>";
    $semanaPasada = $semanaPasada;


    //consultamos los datos de los productos mas vendidos
    

    
    // print_r($semanaPasada);
    $res = ["actual"=>$semanaActual,"pasada"=>$semanaPasada,
    "datoSemActual"=>$datoSemActual,"datoSemPasada"=>$datoSemPasada];
    echo json_encode($res);

  }
}


?>