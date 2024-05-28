let btnUpdateDatos = document.getElementById('btnUpdateDatos');
btnUpdateDatos.addEventListener('click', function(){
  Swal.fire({
    title: 'Actualizar Datos',
    text: 'Estas seguro de actualizar tus datos?',
    iconHtml: '?',
    showCancelButton: true,
    confirmButtonText: 'Si, actualizar',
    cancelButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //actualizamos los datos
      let datos = new FormData(document.getElementById('datosEmpresa'));

      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/updateEmpresas.php',false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          //se actualizo correctamente
          Swal.fire(
            'Informacion actualizada',
            'Se completo correctamente la actualizacion',
            'success'
          ).then(function(){
            location.reload();
          })
        }else{
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
      //no hacemos nada
    }
  })
})