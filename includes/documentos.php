<?php 

function uploadDoc($tmpFile,$tipo,$nombre,$ruta,$empresa){
  $res = [];

  //verificamos la existencia del documento
  $archivo = $tmpFile['type'];
  //verificamos el tipo de documento que se esta subiendo
  $tipoArchivo = $tmpFile['type'];
  $permitidos = [];
  switch ($tipo) {
    case 'imagen':
      $permitidos = ['image/gif','image/jpeg','image/png','	image/jpg'];
      break;
    
    default:
      # code...
      break;
  }

  if(in_array($tipoArchivo, $permitidos)){
    //si es del tipo de documento correcto
    $auxName = explode(".",$tmpFile['name']);
    $ext = $auxName[count($auxName)-1];
    //generamos un nombre aleatorio
    $caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-";
    $aleatorio = substr(str_shuffle($caracteres),0,10);
    $nombreArchivo = $nombre."_".$empresa."_".$aleatorio.".".$ext;
    //verificamos que exista la carpeta del archivo
    $carpetaDestino = $ruta."/".$empresa;
    if(file_exists($carpetaDestino) || mkdir($carpetaDestino)){
      // continua normal
      $rutaCompleta = $carpetaDestino."/".$nombreArchivo;
      try {
        move_uploaded_file($tmpFile['tmp_name'],$rutaCompleta);
        //se movio correctamente, pasamos la rura
        $res = ["estatus"=>"ok","mensaje"=>"operationSuccess","dato"=>$rutaCompleta];
        return json_encode($res);
      } catch (\Throwable $th) {
        ///ocurrio un error al mover el archivo
        $res = ["estatus"=>"error","mensaje"=>"Ocurrio un error al mover el documento: ".$th];
        return json_encode($res);
      }
    }else{
      //no se pudo generar la carp[eta destino
      $res = ["estatus"=>"error","mensaje"=>"Ocurrio un error al generar la carpeta de almacenamiento."];
      return json_encode($res);
    }


  }else{
    //no es del tipo compatible
    $res = ["estatus"=>"error","mensaje"=>"Tipo de archivo incorrecto."];
    return json_encode($res);
  }

}
?>