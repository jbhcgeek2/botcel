let precioSel = document.getElementById('precioFijo');

precioSel.addEventListener('change', function(){
  let datoFijo = precioSel.value;
  //verificaremos si escojio un precio fijo o no, para
  //deshabilitar el campo de precio

  if(datoFijo == "1"){
    //dejamos habilitado el campo removeAttribute
    document.getElementById('precioServ').removeAttribute('disabled');

  }else{
    //lo deshabilitamos y seteamos a vacio
    document.getElementById('precioServ').value = "";
    document.getElementById('precioServ').setAttribute('disabled',true);
  }
})

btnAltaServ = document.getElementById('altaServ');
btnAltaServ.addEventListener('click', function(){
  //seccion para enviar el formulario de alta servicio
  let datos = new FormData(document.getElementById('dataAltaServ'));
  
  let envio = new XMLHttpRequest();
  envio.open("POST","../includes/altaServicios.php",false);
  envio.send(datos);

  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    if(res.status == "ok"){
      //todo procedio correctamente
      Swal.fire({
        title: 'Registro completo',
        text: 'Deseas Registrar otra categoria?',
        icon: 'success',
        showDenyButton: true,
        confirmButtonText: 'Registrar otra',
        denyButtonText: 'Terminar'
      }).then((result)=>{
        if(result.isConfirmed){
          //registraremos otra
          location.reload();
        }else{
          //nos vamos a ver las categorias
          window.location = 'verServicios.php';
        }
      })
    }else{
      //ocurrio un error al insertar
      let err = res.mensaje;
      Swal.fire(
        'Ha ocurrido un error',
        'Verificar: '+err,
        'error'
      )
    }
  }else{
    //error de comunicaicon
    Swal.fire(
      'Servidor Inalcansable',
      'Verifica tu conexion a internet',
      'error'
    )
  }
})
