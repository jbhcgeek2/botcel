let btnUpdate = document.getElementById('btnUpdateProv');

btnUpdate.addEventListener('click', function(){
  Swal.fire({
    title: 'Actualizar Proveedor',
    text: 'Estas seguro de actualizar los datos del provedor?',
    iconHtml: '?',
    showCancelButton: true,
    confirmButtonText: 'Si, Actualizar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if(result.isConfirmed){
      let datos = new FormData(document.getElementById('dataProv'));

      let envio = new XMLHttpRequest();
      envio.open("POST","../includes/modProveedores.php",false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          Swal.fire(
            'Proveedor Actualizado',
            'Se actualizo la informacion del proveedor',
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
      //se cancelo
    }
  })
});