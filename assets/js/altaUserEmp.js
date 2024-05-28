let btnAlta = document.getElementById('btnAltaUser');

btnAlta.addEventListener('click', function(){
  //verificamos que este el nombre, usuario y contra
  let name = document.getElementById('nombreUser').value;
  let usName = document.getElementById('userName').value;
  let passUser = document.getElementById('passwordUser').value;

  if(name != "" && usName != "" && passUser != ""){
    let datos = new FormData(document.getElementById('nuevoUsuarioEmp'));

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/altaUsuario.php',false);
    envio.send(datos);
  
    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      if(res.data == "operationSuccess"){
        //se registro el usuario
        Swal.fire({
          title: 'Usuario Registrado',
          text: 'Se inserto correctamente el usuario',
          icon: 'success'
        }).then(function(){
          window.location = '../verUsuarioEmpr.php';
        })
      }else{
        let err = res.mensaje;
        if(err == "planInsuficiente"){
          Swal.fire(
            'Accion Denegada',
            'Has llegado al limite de usuarios registrados para tu plan',
            'warning'
          )
        }else if(err == "userExist"){
          Swal.fire(
            'El usuario ya existe',
            'Por favor indica un usuario diferente',
            'error'
          )
        }else{
          Swal.fire(
            'Ha ocurrido un error',
            'Verificar: '+err,
            'error'
          )
        }
        
      }
      
    }else{
      //error dfe comunicacion
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      )
    }
  }else{
    //no se detecto informacion
    Swal.fire(
      'Campos invalidos',
      'Asegurate que todos los campos esten capturados',
      'error'
    )
  }

  
});