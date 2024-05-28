let btnUpdate = document.getElementById('btnUpdateCat');
btnUpdate.addEventListener('click', function(){
  //preguntamos si desea cambiar la categoria
  Swal.fire({
    title: 'Actualizar Categoria?',
    text: 'Estas seguro de actualizar la categoria?',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Si, Actualizar',
    denyButtonText: 'No, Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //enviamos los datos
      let datos = new FormData(document.getElementById('dataAltaServCat'));

      let envio = new XMLHttpRequest();
      envio.open("POST","../includes/altaCategoria.php",false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          Swal.fire(
            'Categoria actualizada',
            'Se actualizo correctamente la categoria',
            'success'
          ).then(function(){
            location.reload();
          })
        }else{
          //ocurrio un error
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
      
    }
  })
})