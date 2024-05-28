
let btnSabeCliente = document.getElementById('btnSaveCliente');
btnSabeCliente.addEventListener("click", function(){

  let datos = new FormData(document.getElementById('datosCliente'));

  let envio = new XMLHttpRequest();
  envio.open("POST","../includes/operacionesCliente.php",false);
  envio.send(datos);

  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    if(res.status == 'ok'){
      //se inserto correctamente el cliente
      Swal.fire({
        title: 'Cliente Registrado',
        text: 'Deseas registrar, un nuevo cliente?',
        icon: 'success',
        showDenyButton: true,
        confirmButtonText: 'Registrar Nuevo',
        denyButtonText: `No, terminar proceso`,
      }).then((result)=>{
        if(result.isConfirmed){
          location.reload();
        }else{
          //salimos a ver los clientes
          window.location = "../clientes.php";
        }
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
})