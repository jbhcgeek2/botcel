
function calculaTotal(dato){

  if(dato > 0){
    let inicial = document.getElementById('montoEfectivoIni').value;
    let montoEfe = document.getElementById('ventaEfectivo').value;
    if(montoEfe == ""){
      montoEfe = 0;
    }
    let elemResult = document.getElementById('montoDife');
    let elemColor = document.getElementById('totalDiferencia');

    inicial = parseFloat(inicial);
    montoEfe = parseFloat(montoEfe);
    dato = parseFloat(dato);

    let montoTotal = inicial+montoEfe;
    let diferencia = parseFloat(montoTotal - dato);

    // let suma = dato + inicial;
    if(diferencia != 0){
      elemColor.classList.remove('text-primary');
      elemColor.classList.add('text-danger');
    }else{
      elemColor.classList.add('text-primary');
      elemColor.classList.remove('text-danger');
    }

    elemResult.innerHTML = diferencia;

  }
  
}

function calculaMontoResta(monto){

  if(monto > 0){
    let montoRetira = parseFloat(monto);
    let totalCaja = parseFloat(document.getElementById('montoEfectivo').value);
  
    let restoCaja = parseFloat(totalCaja - montoRetira);

    document.getElementById('saldoDeja').innerHTML = restoCaja;
  }

}

let btnCerrar = document.getElementById('btnCerrarDia');
btnCerrar.addEventListener('click', function(){
  Swal.fire({
    title: 'Estas seguro de cerrar el dia?',
    text: 'No podras deshacer esta accion',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Cerrar Dia',
    cancelButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //mandamos el cierre
      let efectivoCaja = parseFloat(document.getElementById('montoEfectivo').value);
      let montoRetiro = document.getElementById('montoRetiroEfe').value;
      if(montoRetiro == ""){
        montoRetiro = 0;
      }else{
        montoRetiro = parseFloat(montoRetiro);
      }
      let observMov = document.getElementById('obervacionCierre').value;
      let montoDig = document.getElementById('ventaDigital').value;
      if(montoDig == ""){
        montoDig = 0;
      }else{
        montoDig = parseFloat(montoDig);
      }
      let saldoTotalCaja = (document.getElementById('totalCajaSaldo').value);
      //validamos la informacion capturada
      let pasa = 0;
      let pregunta = 0;
      let titulo = "";
      let texto = "";

      if(efectivoCaja == saldoTotalCaja){
        //los saldos cuadran
        document.getElementById('montoEfectivo').classList.remove('is-invalid');
        document.getElementById('montoEfectivo').classList.add('is-valid');
      }else{
        //los saldos no coinciden que lo verifique
        pasa = pasa +1;
        titulo =  'Los montos de caja no coinciden';
        texto = 'Verifica la informacion e intentalo de nuevo';
        document.getElementById('montoEfectivo').classList.add('is-invalid');
      }

      //si los saldos cuadran, verifixamos si desea retirar todo el dinero
      if(montoRetiro == efectivoCaja){
        titulo = "Deseas retirar todo el efectivo?";
        texto = "No se contara con efectivo en la apertura del siguiente dia";
        pasa = pasa +1;
      }else if(montoRetiro > efectivoCaja){
        titulo = "Retiro invalido.";
        texto = "No se contara con efectivo en la apertura del siguiente dia";
        document.getElementById('montoRetiroEfe').classList.remove('is-valid');
        document.getElementById('montoRetiroEfe').classList.add('is-invalid');
        pasa = pasa +1;
      }else{
        
      }

      if(observMov.length > 6){
        document.getElementById('obervacionCierre').classList.remove('is-invalid');
        document.getElementById('obervacionCierre').classList.add('is-valid');
      }else{
        titulo = "Indique una observacion";
        texto = "Asegurate de capturar informacion";
        document.getElementById('obervacionCierre').classList.remove('is-valid');
        document.getElementById('obervacionCierre').classList.add('is-invalid');
        pasa = pasa +1;
      }


      if(pasa == 0){
        //aqui podemos enviar el formulario
        let datos = new FormData();
        datos.append("efectivoTotCaja",efectivoCaja);
        datos.append("montoRetiraEfe",montoRetiro);
        datos.append("observCierre",observMov);
        datos.append("montoDigital",montoDig);
  
        let envio = new XMLHttpRequest();
        envio.open("POST","../includes/cajas.php",false);
        envio.send(datos);
  
        // console.log(envio.responseText);
        if(envio.status == 200){
          let res = JSON.parse(envio.responseText);
          if(res.status == "ok"){
            Swal.fire(
              'Cierre aplicado',
              'No olvides imprimir tus reportes',
              'success'
            ).then(function(){
              //redireccionamos a los reportes
              window.location = "reportesCaja.php";
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
          //error
          Swal.fire(
            'Servidor Inalcansable',
            'Verifica tu conexion a internet',
            'error'
          )
        }
        //fin de envio de formulario
      }else{
        Swal.fire({
          title: titulo,
          text: texto,
          icon: 'error'
        })
      }


    }else{
      //se cancela el swal
    }
  })
})