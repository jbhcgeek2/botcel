let btnBuscar = document.getElementById('btnBuscarMovs');

btnBuscar.addEventListener('click', function(){
  //verificamos que las fechas esten capturadas
  let fechaIni = document.getElementById('fechaIniMov').value;
  let fechaFin = document.getElementById('fechaFinMov').value;

  if(fechaIni != "" && fechaFin != ""){
    let datos = new FormData();
    datos.append('fechaIniMerca',fechaIni);
    datos.append('fechaFinMerca',fechaFin);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);

    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      console.log(res);
      if(res.status == "ok"){
        if(res.data != "NoData"){
          let tabla = '';
          for (let x = 0; x < res.data.length; x++) {
            let fecha = res.data[x].fechaMov;
            let prodName = res.data[x].nombreProdMov;
            let montoUni = res.data[x].precioCompra;
            let cantiMov = res.data[x].cantidad;
            let tipoMov = res.data[x].tipoMov;
            let userMov = res.data[x].usuarioMov;
            let sucMov = res.data[x].nombreSuc;

            tabla = tabla+ `
            <tr>
              <td>${fecha}</td>
              <td>${prodName}</td>
              <td>${montoUni}</td>
              <td>${cantiMov}</td>
              <td>${tipoMov}</td>
              <td>${userMov}</td>
              <td>${sucMov}</td>
            </tr>
            `;
            
          }//fin del for
          //insertamos la tabla
          document.getElementById('bodyTableReport').innerHTML = tabla;
        }else{
          //sin datos registrados
          tabla = `<tr>
            <td colspan='6' style='text-align:center;' class='fw-bold'>Sin resultados</td>
          </tr>`;
          document.getElementById('bodyTableReport').innerHTML = tabla;
        }
      }else{
        //ocurrio un error en la consulta de datos
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrido un error',
          'Verificar: '+err,
          'error'
        )
      }
    }else{
      //error de comunicacion
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion',
        'warning'
      )  
    }
  }else{
    //fechas no capturadas
    Swal.fire(
      'Fechas Invalidas',
      'Asegurate de capturar la fecha de inicio y la fecha fin',
      'warning'
    )
  }
});