let btnAlta = document.getElementById('btnAltaProv');
btnAlta.addEventListener('click', function(){
  Swal.fire({
    title: 'Registrar Proveedor?',
    text: 'Estas seguro de registrar al proveedor?',
    iconHtml: '?',
    confirmButtonText: 'Registrar',
    showCancelButton: true,
    cancelButtonText: 'No, Cancelar'
  }).then((result) =>{
    if(result.isConfirmed){
      //creamos el formulario de registro
      let datos = new FormData(document.getElementById('datosNewProv'));
      let envio = new XMLHttpRequest();
      envio.open("POST","../includes/modProveedores.php",false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          //se registro, le preguntamos si quiere regisrtrar otro
          Swal.fire({
            title: 'Proveedor Registrado',
            text: 'Deseas registrar un nuevo proveedor?',
            icon: 'success',
            confirmButtonText: 'Registrar Otro',
            showCancelButton: true,
            cancelButtonText: 'No'
          }).then((result) =>{
            if(result.isConfirmed){
              //recargamos la pagina para terminar el proceso
              location.reload();
            }else{
              //lo redirigimos a ver los proveedores
              window.location = 'verProveedores.php';
            }
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
      //cancelamos el envio
    }
  })
});