let btnModUser = document.getElementById('btnModUser');

btnModUser.addEventListener('click', function(){
  //primero preguntamos si desea modificar los datos

  Swal.fire({
    title: 'Actualizar datos?',
    text: 'Estas seguro de actulizar los datos?',
    iconHtml: '?',
    showCancelButton: true,
    confirmButtonText: 'Actualizar',
    cancelButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //actualizamos la informacion, pero antes, validamos
      //que los datos esten capturados
      let nombre = document.getElementById('nombreUser').value;
      let pw = document.getElementById('passwordUser').value;
      if(nombre != "" && pw != ""){
        let datos = new FormData(document.getElementById('modUsuarioEmp'));

        let envio = new XMLHttpRequest();
        envio.open('POST','../includes/modUsuarioEmpr.php',false);
        envio.send(datos);

        if(envio.status == 200){
          let res = JSON.parse(envio.responseText);

          if(res.status == "ok"){
            Swal.fire(
              'Usuario actualizado',
              'La informacion del usuario se actualizo correctamente',
              'success'
            ).then(function(){
              location.reload();
            })
          }else{
            //ocurrio un error al actulizar el usuario
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
        //datos incompletos
        Swal.fire(
          'Datos incompletos',
          'Asegurate que toda la informacion del usuario este cargada',
          'error'
        )
      }
      
    }else{
      //cancelamos, no hacemos nada
    }
  })
})