let btnSaveCat = document.getElementById('saveCat');

btnSaveCat.addEventListener('click', function(){
  //preguntamos si quiere enviar la informacion
  Swal.fire({
    title: 'Confirmar Registro',
    text: 'Estas seguro de guardar el registro?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //se continuar con el proceso
      let datos = new FormData(document.getElementById('dataCategoria'));
      
      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/altaCategoria.php',false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          Swal.fire({
            title: 'Categoria Guardada',
            text: 'Deseas registrar otra categoria?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Registrar otra',
            cancelButtonText: 'Terminar',
          }).then((result)=>{
            if(result.isConfirmed){
              //refrescamos la pagina
              location.reload();
            }else{
              //la mandamos al catalogo de categorias\
              window.location = "../verProductos.php";
            }
          })
        }else{
          //error en la guardada
          let err = res.mensaje;
          Swal.fire(
            'Ha ocurrido un error',
            err,
            'error'
          )
        }

      }else{
        //problemas de comunicacion
        Swal.fire(
          'Servidor Inalcansable',
          'Verifica tu conexion a internet',
          'error'
        )
      }
    }
  })
})