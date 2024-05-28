let btnAltaCat = document.getElementById('altaCatServ');

btnAltaCat.addEventListener('click', function(){
  Swal.fire({
    title: 'Registrar Categoria?',
    text: 'Estas seguro de registrar la categoria',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Si, Registrar',
    denyButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //mandamos el registro
      let datos = new FormData(document.getElementById('dataAltaServCat'));
      
      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/altaCategoria.php',false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == 'ok'){
          Swal.fire(
            'Categoria Insertada',
            'Se inserto correctamente la categoria',
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
        //error de servidor
        Swal.fire(
          'Servidor Inalcansable',
          'Verificat tu conexion a internet',
          'error'
        )
      }
      
    }
  })
});