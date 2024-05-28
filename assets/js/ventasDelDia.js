let btnBuscar = document.getElementById('btnBuscar');

btnBuscar.addEventListener('click', function(){
  //verificamos que tengamos fecha inicial y final
  let fechaIni = document.getElementById('fechaIni').value;
  let fechaFin = document.getElementById('fechaFin').value;

  if(fechaIni != "" && fechaFin != ""){
    //enviamos la solicitud de datos
    let datos = new FormData();
    datos.append("fechaIniBus",fechaIni);
    datos.append("fechaFinBus",fechaFin);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);

    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      console.log(res);
      //verificamos la respuesta
      if(res.status == "ok"){
        if(res.data.length > 0){
          //si se tienen datos. hacemos un ciclo for
          //para reconstruir la tabla
          let sumaTotal = 0;
          let tabla = '';
          for (let x = 0; x < res.data.length; x++) {
            let fechaVenta = res.data[x].fechaVenta;
            let prodName = res.data[x].producto;
            let cantVenta = res.data[x].cantidad;
            let usVenta = res.data[x].usuario;
            let sucName = res.data[x].sucursalVenta;
            let totVenta = res.data[x].totalVenta;
            let idVen = res.data[x].venta;

            sumaTotal = parseFloat(sumaTotal) + parseFloat(totVenta);

            tabla = tabla+`
            <tr>
            <td>${fechaVenta}</td>
            <td>${prodName}</td>
            <td>${cantVenta}</td>
            <td>$${totVenta}</td>
            <td>${usVenta}</td>
            <td>${sucName}</td>
            <td>
              <a href='../print.php?t=${idVen}' target='_blank' class='btn btn-success'>Ver Ticket</a>
            </td>
            </tr>
            `;
          }//fin del for
          //insertamos el row de totales
          const formattedNumber = sumaTotal.toLocaleString('en-US', { maximumFractionDigits: 2 });
          tabla = tabla+`
          <tr>
            <td colspan='3' class='fw-bold' style='text-align:right'>Total Venta</td>
            <td class='fw-bold'>$${formattedNumber}</td>
            <td></td>
            <td></td>
          </tr>
          `;

          //insertamos el contenido nuevo en la tabla
          document.getElementById('bodyTableReport').innerHTML = tabla;
          
        }else{
          //no se tienen datos
          tabla = `<tr>
            <td colspan='6' style='text-align:center;'><strong>Sin Resultados</strong></td>
          </tr>`;
          document.getElementById('bodyTableReport').innerHTML = tabla;
        }
      }else{
        //ocurrio un error en la consulta
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
      'Fechas Invalidas',
      'Asegurate de capturar fecha inicial y final',
      'warning'
    )
  }
});