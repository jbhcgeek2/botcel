if(document.getElementById('dataAltaSuc')){
  let btnAlta = document.getElementById('altaSuc');
  btnAlta.addEventListener('click', function(){
    //preguntamos si esta seguro de registrar
    Swal.fire({
      title: 'Estas seguro de registrar sucursal?',
      iconHTML: '?',
      showCancelButtom: true,
      confirmButtomText: 'Registrar',
      cancelButtomText: 'Cancenlar'
    }).then((result) => {
      if(result.isConfirmed){
        //procedemos
        let datos = new FormData(document.getElementById('dataAltaSuc'));
        let envio = new XMLHttpRequest();
        envio.open("POST","../includes/modSucursales.php",false);
        envio.send(datos);

        if(envio.status == 200){
          let res = JSON.parse(envio.responseText);
          if(res.status == "ok"){
            Swal.fire(
              'Sucursal Registrada',
              'Se Registro correctamente la sucursal',
              'success'
            ).then(function(){
              window.location = "verSucursales.php";
            })
          }else{
            //error al insertar
            let err = res.mensaje;
            Swal.fire(
              'Ha ocurrido un error',
              'Verificar: '+err,
              'error'
            )
          }
        }else{
          //error de comunicacion
          Swal.fire(
            'Servidor Inalcansable',
            'Verifica tu conexion a internet',
            'error'
          )
        }
      }else{
        //se cancelo
      }
    })
  });
}