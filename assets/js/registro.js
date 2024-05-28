let btnRegistro = document.getElementById('registroNuevo');

btnRegistro.addEventListener('click', function(){
  //seccion para validar y registrar el nuevo usuario
  let data = new FormData(document.getElementById('formRegistro'));

  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/registroNuevo.php',false);
  envio.send(data);

  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    if(res.estatus == "ok"){
      //registro completo
      Swal.fire(
        'Registro exitoso',
        'Se ha completado el registro de su empresa.',
        'success'
      ).then(function(){
        window.location = 'login.php';
      })
    }else{
      //error en elproceso
      let err = res.mensaje;
      Swal.fire(
        'Ha ocurrido un error',
        'Verificar: '+err,
        'error'
      )
    }

  }else{
    Swal.fire(
      'Servidor Inalcansable',
      'Ha ocurrido un error al comunicarse con el servidor',
      'error'
    )
  }
})