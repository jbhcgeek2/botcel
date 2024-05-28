let inputBuscar = document.getElementById("buscarCliente");

inputBuscar.addEventListener('keyup', function(){
  let valor = inputBuscar.value;

  if(valor.length > 1 && valor.length % 3 === 0){
    let dato = new FormData();
    dato.append("buscarCliente",valor);
  
    let envio = new XMLHttpRequest();
    envio.open("POST","../includes/operacionesCliente.php",false);
    envio.send(dato);
  
    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      console.log(res.data.length);
      let campos = "";
      if(res.data.length > 0){
        for (let x = 0; x < res.data.length; x++) {
          let nombreCli = res.data[x].nombreCliente;
          let telCli = res.data[x].telefonoCliente;
          let mailCli = res.data[x].emailCliente;
          let cliente = res.data[x].idClientes;
  
          console.log(nombreCli);
  
          campos = campos+`<tr>
            <td>${nombreCli}</td>
            <td>${telCli}</td>
            <td>${mailCli}</td>
            <td>
              <a href='verCliente.php?cliente=${cliente}'>Ver</a>
            </td>
          </tr>`;
          
        }//fin del for
        //insertamos los resultados
        document.getElementById("resultBusqueda").innerHTML = campos;
      }else{
        //sin resultados
        campos = "<tr><td colspan='5'><h5 class='text-center'>Sin Resultados</h5></td></tr>";
        document.getElementById("resultBusqueda").innerHTML = campos;
      }
    }else{
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      )
    }
  }else{
    //sin campos requeridos
  }

  

});