let btnUpdate = document.getElementById("btnUpdateCliente");

btnUpdate.addEventListener("click", function(){
  let datos = new FormData(document.getElementById("datosClienteUpdate"));

  let envio = new XMLHttpRequest();
  envio.open("POST","../includes/operacionesCliente.php",false);
  envio.send(datos);

  if(envio.status == 200){
    console.log(envio.responseText);
    let res = JSON.parse(envio.responseText);
    if(res.status == "ok"){
      //se actualizo la informacion del cliente
      Swal.fire(
        'Informacion Actualizada',
        'Se actualizo correctamente la informacion del cliente',
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
    //error en el envio
    Swal.fire(
      'Servidor Inalcansable',
      'Verifica tu conexion a internet',
      'error'
    )
  }
});