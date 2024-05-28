if (document.getElementById('modalAperturaDia')) {
  //no se ha registrado el inicio del dia
  const myModal = new bootstrap.Modal('#modalAperturaDia', {
    keyboard: false,
    focus: true
  });
  const modalApertura = document.getElementById('modalAperturaDia');
  myModal.show(modalApertura);


  let btnEnviar = document.getElementById('enviaApertura');
  btnEnviar.addEventListener('click', function () {
    let datos = new FormData(document.getElementById('datosApertura'));

    let envio = new XMLHttpRequest();
    envio.open("POST", "../includes/cajas.php", false);
    envio.send(datos);

    if (envio.status == 200) {
      console.log(envio.responseText);
    } else {
      //error de comunicacion
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      );
    }
  });
}
