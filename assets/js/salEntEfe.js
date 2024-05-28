let btnMovs = document.getElementById('btnBuscarMovs');

btnMovs.addEventListener('click', function(){
  //verificamos que las fechas esten correctas
  let fechaIniMov = document.getElementById('fechaIniMov').value;
  let fechaFinMov = document.getElementById('fechaFinMov').value;

  if(fechaIniMov != "" && fechaFinMov != ""){
    //si estan capturadas mandamos el xml
    let datos = new FormData();
    datos.append("fechaIniMov",fechaIniMov);
    datos.append("fechaFinMov",fechaFinMov);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);
    
    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      console.log(res);
      if(res.status == "ok"){
        //se consulto bien, ahora verificamos si tiene datos
        let tabla = '';
        if(res.data != "NoData"){
          //creamos el for de datos
          let sumaTotal = 0;
          for (let i = 0; i < res.data.length; i++) {
            // console.log(res.data);
            let auxRes = res.data;
            let fechaMov = auxRes[i].fechaMovimiento;
            let concepName = auxRes[i].concepName;
            let montoMov = auxRes[i].montoMov;
            let tipoMov = auxRes[i].tipoMov;
            let userMov = auxRes[i].usmov;
            let sucMov = auxRes[i].sucNameMov;
            let auxTipoMov = "";
            let classAux = "";
            if(tipoMov == "E"){
              auxTipoMov = "Entrada";
              // classAux = "table-success";
              sumaTotal = parseFloat(sumaTotal) + parseFloat(montoMov);
            }else{
              auxTipoMov = "Salida";
              // classAux = "table-danger";
              sumaTotal = parseFloat(sumaTotal) + parseFloat(montoMov);
            }

            

            tabla = tabla+`
            <tr class='${classAux}'>
              <td>${fechaMov}</td>
              <td>${concepName}</td>
              <td>${montoMov}</td>
              <td>${auxTipoMov}</td>
              <td>${userMov}</td>
              <td>${sucMov}</td>
            </tr>
            `;
          }//fin del for

          //  const formattedNumber = sumaTotal.toLocaleString('en-US', { maximumFractionDigits: 2 });
          // tabla = tabla+`
          // <tr>
          //   <td colspan='2' class='fw-bold' style='text-align:right'>Total Venta</td>
          //   <td class='fw-bold'>$${formattedNumber}</td>
          //   <td></td>
          //   <td></td>
          //   <td></td>
          // </tr>
          // `;
        }else{
          //no se tienen datos
          tabla = `<tr>
            <td colspan='6' style='text-align:center;' class='fw-bold'>Sin resultados</td>
          </tr>
          `;
        }
        document.getElementById('bodyTableReport').innerHTML = tabla;
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
      //error de conexion
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion',
        'warning'
      )      
    }
  }else{
    //debe tener las 2 fechas capturadas
    Swal.fire(
      'Fechas Invalidas',
      'Asegurate de capturar la fecha de inicio y la fecha fin',
      'warning'
    )
  }
})