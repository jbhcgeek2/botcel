let btnLog = document.getElementById('brtSession');

btnLog.addEventListener('click', function(){
  //verificamos que contenga dastos correctos

  let mailLog = document.getElementById('userName').value;
  let passLog = document.getElementById('passLog').value;

  if(mailLog != "" && passLog != ""){
    //verificamos los datos
    let datos = new FormData;
    datos.append("mail",mailLog);
    datos.append("pass",passLog);

    let envio = new XMLHttpRequest();
    envio.open("POST","../includes/loginOperation.php", false);
    envio.send(datos);

    if(envio.status == 200){
      //respuesta ok
      let res = envio.responseText;
      console.log(res);
      res = JSON.parse(res);
      if(res.status == "ok"){
        //el estatus es correecto, lo redirigimos a la pagina de inicio
        window.location= 'index.php';
      }else{
        Swal.fire(
          'Datos incorrectos',
          'Usuario y/o contrasena incorrectos',
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
    Swal.fire(
      'Verifica tus datos',
      'Los campos deben estar capturados.',
      'error'
    )
  }
});