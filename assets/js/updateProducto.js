let btnUpdate = document.getElementById('btnUpdateProd');

btnUpdate.addEventListener('click', function(){
  let datos = new FormData(document.getElementById('dataProducto'));

  let envio = new XMLHttpRequest();
  envio.open("POST","../includes/modProducto.php",false);

  envio.send(datos);
  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    if(res.status == "ok"){
      Swal.fire(
        'Producto Actualizado',
        'Se actualizo correctamente la informacion del producto',
        'success'
      ).then(function(){
        location.reload();
      })
    }else{
      //error al actualizar
      let err = "Verificar: "+res.mensaje;
      Swal.fire(
        'Ha ocurrido un error',
        err,
        'error'
      )
    }
    // console.log(envio.responseText);
  }else{
    //error de comunicacion
    Swal.fire(
      'Servidor inalcansable',
      'Verifica tu conexion a internet',
      'error'
    )
  }
})