
let btnUpdate = document.getElementById('updateSuc');
btnUpdate.addEventListener('click', function(){
  //primero preguntamos si esta seguro de actualizar
  Swal.fire({
    title: 'Estas Seguro de Actualizar?',
    iconHtml: '?',
    showCancelButton: true,
    confimButtonText: 'Actualizar',
    cancelButtonText: 'Cancelar'
  }).then((result) =>{
    if(result.isConfirmed){
      //desarrollamos el formdata
      let datos = new FormData(document.getElementById('dataSucursal'));
      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/modSucursales.php',false);
      envio.send(datos);

      if(envio.status == 200){
        console.log(envio.responseText);
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          //se actalizo la informacion
          Swal.fire(
            'Sucursal Actualizada',
            'La informacion se actualizo correctamente.',
            'success'
          ).then(function(){
            location.reload();
          })
        }else{
          //error al actualizar
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
          'Verifica tu conexion a internet',
          'error'
        )
      }
    }else{
      //no hacemos nada
    }
  })
})