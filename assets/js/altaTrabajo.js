
document.addEventListener("DOMContentLoaded", function() {
  Swal.fire({
    title: 'Antes de continuar...',
    text: 'Ya se encuentra registrado el cliente?',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Ya esta registrado',
    denyButtonText: 'No, Registrar nuevo cliente'
  }).then((result)=>{
    if(result.isConfirmed){
      //ya esta registrado
    }else{
      //no esta registrado, lo damos de alta
      window.location = 'altaCliente.php';
    }
  })

  let tipoServ = document.getElementById('tipoServicio');
  tipoServ.addEventListener('change', function(){
    //buscaremos el precio sugerido del servicio
    let dato = tipoServ.value;
    let datoServ = new FormData();
    datoServ.append('servCheck',dato);

    let envioServ = new XMLHttpRequest();
    envioServ.open('POST','../includes/trabajosOperaciones.php', false);
    envioServ.send(datoServ);

    if(envioServ.status == 200){
      let res = JSON.parse(envioServ.responseText);

      if(res.status == "ok"){
        //se consulto correctamente
        document.getElementById('costoServicio').value = res.data;
      }else{
        //error de operacion
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrido un error',
          'Verifica: '+err,
          'error'
        )
      }
    }else{
      //error de servidor
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      )
    }

  })

  let btnAlta = document.getElementById('altaTrabajo');
  btnAlta.addEventListener('click', function(){
    //le preguntamos que si esta seguro registrar

    Swal.fire({
      title: 'Registrar Trabajo?',
      text: 'Estas seguro de registrarlo?',
      icon: 'warning',
      showDenyButton: true,
      confirmButtonText: 'Si, registrar',
      denyButtonText: 'Cancelar'
    }).then((result)=>{
      if(result.isConfirmed){
        //cargamos los datos

        let datos = new FormData(document.getElementById('dataAltaTrab'));
        //verificamos que los datos requeridos esten cargados
        let pasa = 0;
        let camposReq = ['clienteTrabajo','fechaServicio','sucursalServicio','tipoDispositivo','tipoServicio',
        'marcaServicio','modeloServicio','numberDevice','descripcionProblema','fechaEntrega'];
        datos.forEach(function(valor, clave){
          console.log('Valor: '+valor+' del campo '+clave);
          //si el campo esta vacio lo marcaremos como valido
          let campo = document.getElementById(clave);

          if(camposReq.includes(clave)){
            if(valor.trim() == "" ||  valor == " "){
              //campo vacio
              campo.classList.add('is-invalid');
              pasa++;
            }else{
              campo.classList.remove('is-invalid');
              campo.classList.add('is-valid');
            }
          }
          
        })

        if(pasa == 0){
          //carmagos el envio
          let envio = new XMLHttpRequest();
          envio.open('POST','../includes/trabajosOperaciones.php', false);
          envio.send(datos);

          if(envio.status == 200){
            //se completo
            let res = JSON.parse(envio.responseText);
            if(res.status == 'ok'){
              //se completo el proceso
              Swal.fire(
                'Trabajo Registrado',
                'Se ha registrado el trabajo correctamente',
                'success'
              ).then(function(){
                //una vez registrado el trabajo, abrimos una nueva ventana
                //para la impresion del ticket
                let trabajoT = res.data;
                window.open('../printTrabajo.php?t='+trabajoT,'_blank');
                window.location = 'verInfoTrabajo.php?data='+trabajoT;
              })
            }else{
              // ocurrio un error al insertar el trabajo
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

        }else{
          Swal.fire(
            'Campos Incompletos',
            'Completa la informacion requerida',
            'warning'
          )
        }

      }
    })
  });

})


