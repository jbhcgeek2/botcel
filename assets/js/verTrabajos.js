let estatus = document.getElementById('buscarEstatus');
estatus.addEventListener('change', function(){
  let estatusSel = estatus.value;
  let nombreCli = document.getElementById('clienteNombre').value;

  let datos = new FormData();
  datos.append('estatusBusqueda',estatusSel);
  datos.append('nombreCli',nombreCli);

  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/trabajosOperaciones.php',false);
  envio.send(datos);
  

  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    
    if(res.status == "ok"){
      let contenido = '';
      if(res.mensaje == "dataOk"){
        //si tiene resultados
        let colorStatus = {"Activo":'badge rounded-pill text-bg-success',
        "En Proceso":'badge rounded-pill text-bg-warning',
        "En Espera":'badge rounded-pill text-bg-secondary',
        "Finalizado":'badge rounded-pill text-bg-danger',
        "Cancelado":'badge rounded-pill text-bg-dark'};

        for (let z = 0; z < res.data.length; z++) {
          // console.log(res.data[z].numTrabajo);
          let nombreCli = res.data[z].nombreCliente;
          let tipoTra = res.data[z].nombreServicio;
          let fechaReg = res.data[z].fechaTrabajo;
          let fechaEnt = res.data[z].fechaEntrega;
          let estatusTra = res.data[z].estatusTrabajo;
          let trabId = res.data[z].idTrabajo;

          let colorEstatus = colorStatus[estatusTra];

          contenido = contenido+`<tr>
            <td>${nombreCli}</td>
            <td>${tipoTra}</td>
            <td>${fechaReg}</td>
            <td>${fechaEnt}</td>
            <td><span class='${colorEstatus}'>${estatusTra}</span></td>
            <td>
              <a href='verInfoTrabajo.php?data=${trabId}' class='btn btn-primary'>Ver</a>
            </td>
          </tr>`;
        }//fin del for
        document.getElementById('resBusqueda').innerHTML = contenido;
      }else{
        //no se encontraron resultados
        contenido = '<tr><td colspan="6" style="text-align:center;">Sin Resultados</td></tr>';
        document.getElementById('resBusqueda').innerHTML = contenido;
      }
    }else{
      //error
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
      'Verifica tu conexion a internet',
      'error'
    )
  }
});


let nombreCli = document.getElementById('clienteNombre');
nombreCli.addEventListener('change', function(){
  //metodo para buscar trabajos por nombre de clientes
  let nombreCliente = nombreCli.value;
  let estatusTra = document.getElementById('buscarEstatus').value;

  let datos = new FormData();
  datos.append('buscarByCliente',nombreCliente);
  datos.append('estatusTraCli',estatusTra);

  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/trabajosOperaciones.php',false);
  envio.send(datos);

  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    
    if(res.status == "ok"){
      let contenido = '';
      if(res.mensaje == "dataOk"){
        //si tiene resultados
        let colorStatus = {"Activo":'badge rounded-pill text-bg-success',
        "En Proceso":'badge rounded-pill text-bg-warning',
        "En Espera":'badge rounded-pill text-bg-secondary',
        "Finalizado":'badge rounded-pill text-bg-danger',
        "Cancelado":'badge rounded-pill text-bg-dark'};

        for (let z = 0; z < res.data.length; z++) {
          // console.log(res.data[z].numTrabajo);
          let nombreCli = res.data[z].nombreCliente;
          let tipoTra = res.data[z].nombreServicio;
          let fechaReg = res.data[z].fechaTrabajo;
          let fechaEnt = res.data[z].fechaEntrega;
          let estatusTra = res.data[z].estatusTrabajo;
          let trabId = res.data[z].idTrabajo;

          let colorEstatus = colorStatus[estatusTra];

          contenido = contenido+`<tr>
            <td>${nombreCli}</td>
            <td>${tipoTra}</td>
            <td>${fechaReg}</td>
            <td>${fechaEnt}</td>
            <td><span class='${colorEstatus}'>${estatusTra}</span></td>
            <td>
              <a href='verInfoTrabajo.php?data=${trabId}' class='btn btn-primary'>Ver</a>
            </td>
          </tr>`;
        }//fin del for
        document.getElementById('resBusqueda').innerHTML = contenido;
      }else{
        //no se encontraron resultados
        contenido = '<tr><td colspan="6" style="text-align:center;">Sin Resultados</td></tr>';
        document.getElementById('resBusqueda').innerHTML = contenido;
      }
    }else{
      //error
      let err = res.mensaje;
      Swal.fire(
        'Ha ocurrido un error',
        'Verificar: '+err,
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