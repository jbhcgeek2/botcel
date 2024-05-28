var campoInput = document.getElementById("buscarProducto");
campoInput.focus();

let campoBusqueda = document.getElementById('buscarProducto');
campoBusqueda.addEventListener('change', function(){
  let valor = campoBusqueda.value;

 if(valor.trim().split("").length > 0){
  //se tiene info
  
    let datos = new FormData();
    datos.append("busqueda",valor);

    
    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/cajas.php',false);
    envio.send(datos);

    if(envio.status == 200){
      
      let res = JSON.parse(envio.responseText);
      // if(JSON.parse(envio.responseText)){

      // }else{
      //   console.log("mal");
      // }

      if(res.status == "ok"){
        //verificamos el numero de resultados
        // numRes.length
        if(res.data == "operationSuccess"){
          location.reload();
        }else{
          let numRes = res.data.length;
          // console.log(res.data);
          let contenido = '';
          for (let x = 0; x < numRes; x++) {
            let nombre = res.data[x].nombreArticulo;
            nombre = nombre.substring(0, 22)+"...";
            // console.log(nombre);
            let precio = "";
            let arti = res.data[x].idArticulo;
            let precioMayoreo = "";
            let imagen = "";
            if(res.data[x].precioUnitario == ""){
              precio = "NA";
            }else{
              precio = res.data[x].precioUnitario;
            }
            if(precioMayoreo = res.data[x].precioMayoreo == ""){
              precioMayoreo = "NA";
            }else{
              precioMayoreo = res.data[x].precioMayoreo;
            }
            if(res.data[x].imgArticulo == "" || res.data[x].imgArticulo == null){
              imagen = '../assets/images/no-image-available.jpeg';
            }else{
              imagen = res.data[x].imgArticulo;
            }
            
            
            //creamos los nuevos elementos del resultado
            let auxContenido = `
            <div class='col-sm-12 col-md-6 col-lg-4'>
              <div class='card mb-3' style='min-height:75px; !important' onclick='addCarrito(${arti})'>
  
                <div class='row g-0'>
  
                  
                  <div class='col-md-12'>
                    <div class='card-body pl-1 pt-0 pb-0'>
                      <span class='card-title text-truncate mb-0' style='font-size:11px;'><strong>${nombre}</strong></span><br>
                      <span class='card-text mb-0'>$${precio}</span><br>
                      <span class='card-text mt-0'><small style='font-size:9px;'>Mayoreo: $${precioMayoreo}</small></span>
                    </div>
                  </div>
  
                </div>
  
              </div>
            </div>`;
            contenido = contenido+auxContenido;
          }
          // console.log(numRes);
          document.getElementById('resultBusqueda').innerHTML = contenido;
        }
        
      }else{
        //error de status
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrido un error',
          'Verificar: '+err,
          'error'
        ).then(function(){
          campoBusqueda.value = '';
          campoBusqueda.focus;
        })
      }


      // console.log(envio.responseText);
    }else{
      //error de comunicacion
    }
  
 }

  
})


function addCarrito(articulo){
  //cuando el usuario de click en el elemento, este se agregara a su orden
  let datos = new FormData();
  datos.append("addArti",articulo);

  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/cajas.php',false);
  envio.send(datos);

  if(envio.status == 200){
    // let res = JSON.parse(envio.responseTex);
    
    let res = envio.responseText.split("+-_-+");
    if(res[0]== "operationSuccess"){
      let contenido = res[1];
      let totalVenta = res[2];
      let totalArti = res[3];

      document.getElementById('cantenidoProds').innerHTML = contenido;
      document.getElementById('totalVentaProds').innerHTML = "Pagar $"+totalVenta;
      document.getElementById('numArtiVenta').innerHTML = "("+totalArti+" Articulos)";
      document.getElementById("totalCobroVenta").value = totalVenta;
      document.getElementById("totalVentanaCobro").innerHTML = "<strong>$"+totalVenta+"</strong>";
    }else{
      let err = res[1];
      Swal.fire(
        'Ha ocurrido un error',
        'verificar: '+err,
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
  
}

function addMoreProd(prodVenta){
  let venta = prodVenta;

  //enviamos que agregue uno mas a la venta
  let datos = new FormData();
  datos.append("addOneProd",venta);

  let envio = new XMLHttpRequest();
  envio.open("POST","../includes/cajas.php",false);
  envio.send(datos);

  if(envio.status == 200){
    let res = envio.responseText;
    res = JSON.parse(res);
    if(res.status == "ok"){
      let totalVenta = res.data.totalVenta;
      let cantidadProd = res.data.cantidadVenta;
      let subtotal = res.data.subtotal;
      let totalArti = res.data.totalArti;
  
      document.getElementById("cantVent"+venta).value = cantidadProd;
      document.getElementById("subTotVenta"+venta).innerHTML = subtotal;
      document.getElementById("totalVentaProds").innerHTML = "Pagar $"+totalVenta;
      document.getElementById("numArtiVenta").innerHTML = "("+totalArti+" Articulos)";
      document.getElementById("totalCobroVenta").value = totalVenta;
      document.getElementById("totalVentanaCobro").innerHTML = "<strong>$"+totalVenta+"</strong>";
    }else{
      //ocurrio un error realizar la operqacion
      let err = res.mensaje;
      Swal.fire(
        'Ha ocurrio un error',
        'Verificar: '+err,
        'error'
      )
    }

    
    // console.log(res);
  }else{
    //error de comunicacion
    Swal.fire(
      'Ha ocurrido un error',
      'Verificar: ',
      'error'
    )
  }
}

function updateCantProd(campo){

  let valor = document.getElementById(campo).value;

  if(valor >= 1){
    let aux = campo.split("cantVent");
    let idProdVenta = aux[1];
    // alert(valor);
  
    let datos = new FormData();
    datos.append("addOneProd",idProdVenta);
    datos.append("cambioCantidad",valor);
  
    let envio = new XMLHttpRequest();
    envio.open("POST","../includes/cajas.php",false);
    envio.send(datos);
  
    if(envio.status == 200){
      let res = envio.responseText;
      res = JSON.parse(res);
      if(res.status == "ok"){
        let totalVenta = res.data.totalVenta;
        let cantidadProd = res.data.cantidadVenta;
        let subtotal = res.data.subtotal;
        let totalArticulo = res.data.totalArti;
    
        // document.getElementById("cantVent"+venta).value = cantidadProd;
        document.getElementById("subTotVenta"+idProdVenta).innerHTML = subtotal;
        document.getElementById("totalVentaProds").innerHTML = "Pagar $"+totalVenta;
        document.getElementById("numArtiVenta").innerHTML = "("+totalArticulo+" Articulos)";
        document.getElementById("totalCobroVenta").value = totalVenta;
        document.getElementById("totalVentanaCobro").innerHTML = "<strong>$"+totalVenta+"</strong>";
      }else{
        //ocurrio un error en la operacion
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrio un error',
          'Vericiar: '+err,
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
  }else{
    //no se puede indicar una cantidad menor o a 1
    Swal.fire(
      'Indique una cantidad valida',
      'No puedes indicar una cantidad menor a 1.',
      'error'
    )
  }
  

}

function delOneProd(prod){
  //verificaremos si la cantidad del producto es mayor a 1
  let cantidadActual = document.getElementById("cantVent"+prod).value;
  // console.log(cantidadActual);
  if(cantidadActual >= 2){
    let datos = new FormData();
    datos.append("addOneProd",prod);
    datos.append("delOneProd","delete");

    let envio = new XMLHttpRequest();
    envio.open("POST","../includes/cajas.php",false);
    envio.send(datos);

    if(envio.status == 200){
      let res = envio.responseText;
      res = JSON.parse(res);
      if(res.status == "ok"){
        let totalVenta = res.data.totalVenta;
        let cantidadProd = res.data.cantidadVenta;
        let subtotal = res.data.subtotal;
        let totalArti = res.data.totalArti;
    
        document.getElementById("cantVent"+prod).value = cantidadProd;
        document.getElementById("subTotVenta"+prod).innerHTML = subtotal;
        document.getElementById("totalVentaProds").innerHTML = "Pagar $"+totalVenta;
        document.getElementById("numArtiVenta").innerHTML = "("+totalArti+" Articulos)";
        document.getElementById("totalCobroVenta").value = totalVenta;
        document.getElementById("totalVentanaCobro").innerHTML = "<strong>$"+totalVenta+"</strong>";
      }else{
        //error de operacion
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrdio un error',
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
      'Indique una cantidad valida',
      'No puedes indicar una cantidad menor a 1.',
      'error'
    )
  }
}

function delProd(producto){
  //seccion para eliminar el producto del carrito
  let datos = new FormData();
  datos.append("delAllProd",producto);
  

  let envio = new XMLHttpRequest();
  envio.open("POST","../includes/cajas.php",false);
  envio.send(datos);

  if(envio.status == 200){
    // let res = JSON.parse(envio.responseTex);
    let res = envio.responseText.split("+-_-+");
    if(res[0]== "operationSuccess"){
      let contenido = res[1];
      let totalVenta = res[2];
      let totalArti = res[3];
      document.getElementById('cantenidoProds').innerHTML = contenido;
      document.getElementById('totalVentaProds').innerHTML = "Pagar $"+totalVenta;
      document.getElementById('numArtiVenta').innerHTML = "("+totalArti+" Articulos)";
      document.getElementById("totalCobroVenta").value = totalVenta;
      document.getElementById("totalVentanaCobro").innerHTML = "<strong>$"+totalVenta+"</strong>";


    }else{
      Swal.fire(
        'Ha ocurrido un error',
        'verificar: ',
        'error'
      )
    }
    
    // console.log(res);
  }else{
    Swal.fire(
      'Servidor Inalcansable',
      'Verifica tu conexion a internet',
      'error'
    )
  }

}

function sendToTras(){
  //funcion para vaciar todo el carrito de compras

  let datos = new FormData();
  datos.append("sendToTras","confirmado");

  let envia = new XMLHttpRequest();
  envia.open("POST","../includes/cajas.php",false);
  envia.send(datos);

  if(envia.status == 200){
    let res = JSON.parse(envia.responseText);
    if(res.status == "ok"){
      //se elimino correctamente el carrito, no decimos nada, pero mandamos los valores a 0
      document.getElementById('cantenidoProds').innerHTML = "";
      document.getElementById('numArtiVenta').innerHTML = "(0 Articulos)";
      document.getElementById('totalVentaProds').innerHTML = "Pagar $0.00";
      document.getElementById("totalCobroVenta").value = '0';
      document.getElementById("totalVentanaCobro").innerHTML = "<strong>$0.00</strong>";
    }else{
      //ocurrio un error al eliminar los prodcutos
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
}

function calculaDescuento(){
  let descuento = document.getElementById("descuentoVenta").value;

  if(descuento > 0 && descuento < 101){
    //para hacer este calculo lo tendremos que realizar directo en php
    let datos = new FormData();
    datos.append("porceDesc",descuento);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/cajas.php',false);
    envio.send(datos);

    if(envio.status == 200){
      // console.log(envio.responseText);
      let res = JSON.parse(envio.responseText);
      if(res.status == "ok"){
        let total = res.data;
        document.getElementById("totalVentaProds").innerHTML = "Pagar $"+total;
        document.getElementById("totalCobroVenta").value = total;
        document.getElementById("totalVentanaCobro").innerHTML = "<strong>$"+total+"</strong>";
      }else{
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
  }
}

function metodoPago(campo){
  //tenemos 4 metodos de pago veremos cual esta seleccionado
  let campos = ["pagoEfectivo","pagoTarjeta","pagoTransferencia","pagoCredito"];
  for(let x = 0; x < campos.length; x++){
    let campoSel = "img"+campos[x];
    let element = document.getElementById(campoSel);
    let campoInput = document.getElementById(campos[0]);

    if(campos[x] == campo){
      //este campo esta seleccionado
      //primero, verificamos si contiene la clase grayscale  
      if(element.classList.contains("grayScale")){
        //contiene la clase, se la eliminamos
        element.classList.remove("grayScale");
      }
    }else{
      //ponemos la clase grayscale
      element.classList.add("grayScale");
      //si tiene valores lo ponemos en 0
      campoInput.value = "";
      
    }
  }//fin del for
}

function calculaCambio(){
  //este metodo aplica unicamente al metodo de pago en efectivo

  let montopago = parseFloat(document.getElementById("pagoEfectivo").value);
  let totalCobro = parseFloat(document.getElementById("totalCobroVenta").value);
  // console.log(totalCobro);
  if(montopago > 0){
    if(montopago >= totalCobro){
      let cambio = montopago - totalCobro;
      document.getElementById("cambioLabel").innerHTML = "Cambio $"+cambio;
    }
    
  }else{

  }
}

let btnCobro = document.getElementById("btnCobroVenta");

btnCobro.addEventListener('click', function(){
  //antes de continuar preguntamos si estta seguro de procesar
  Swal.fire({
    title: 'Estas seguro de procesar el cobro?',
    text: 'No podras cancelar este ticket',
    icon: 'warning',
    animation: true,
    showDenyButton: true,
    allowOutsideClick: false,
    allowEscapeKey:false,
    confirmButtonText: "Cobrar",
    denyButtonText: "Regresar"
  }).then((result) => {
    if(result.isConfirmed){
      //procesamos la venta
      //hacemos el envio de la venta
      let montoPagoEfe = document.getElementById("pagoEfectivo").value;
      let montoPagoTarjeta = document.getElementById("pagoTarjeta").value;
      let montoPagoTransferencia = document.getElementById("pagoTransferencia").value;
      let montoPagoCredito = document.getElementById("pagoCredito").value;
      let clienteVenta = document.getElementById("clienteVenta").value;

      let total = document.getElementById("totalCobroVenta").value;
      let descuento = document.getElementById("descuentoVenta").value;
      let iva = document.getElementById("ivaVenta").value;

      let datos = new FormData();
      datos.append("montoPagoEfe",montoPagoEfe);
      datos.append("montoPagoTarjeta",montoPagoTarjeta);
      datos.append("montoPagoTransferencia",montoPagoTransferencia);
      datos.append("montoPagoCredito",montoPagoCredito);
      datos.append("totalPago",total);
      datos.append("descuentoPago",descuento);
      datos.append("ivaPago",iva);
      datos.append("clienteVenta",clienteVenta);

      let envio = new XMLHttpRequest();
      envio.open("POST","../includes/cajas.php",false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);

        if(res.status == "ok"){
          //mandamos a imprimir el ticket
          let ticket = res.data;
          Swal.fire({
            title: 'Venta Registrada',
            text: 'Puede mandar a imprimir su ticket',
            icon: 'success'
          }).then(function(){
            window.open("../print.php?t="+ticket,"Impresion de Ticket");
            location.reload();
          })
        }else{
          //ocurrio un error
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

    }else{
      //cerramos el swal
      // console.log("se cancela");
    }
  })
})


if(document.getElementById('modalAperturaDia')){
  //no se ha registrado el inicio del dia
  const myModal = new bootstrap.Modal('#modalAperturaDia',{
    keyboard: false,
    focus: true
  });
  const modalApertura = document.getElementById('modalAperturaDia');
  myModal.show(modalApertura);


  let btnEnviar = document.getElementById('enviaApertura');  
  btnEnviar.addEventListener('click', function(){

    let observacion = document.getElementById('observMov').value;
    let montoInicio = document.getElementById('montoMov').value;
    var pasa = 0;
    // var envio;

    if(observacion.length >= 5){
      //verificamos si el monto es 0
      // pasa = 1;
      if(montoInicio == "0" || montoInicio == "" || montoInicio == " "){
        //aqui debemos preguntar
        Swal.fire({
          title: 'Iniciar en ceros?',
          text: 'Estas seguro de aperturar el dia en ceros?',
          icon: 'warning',
          showDenyButton: true,
          allowOutsideClick: false,
          allowEscapeKey:false,
          confirmButtonText: "Iniciar en ceros",
          denyButtonText: "Cancenlar"
        }).then( (result) => {
          if(result.isConfirmed){
            enviarDatosAper();
          }else{
            pasa = 0;
          }
        })
      }else{
        enviarDatosAper();
      }

      
    }else{
      //debe tener una observacion valida
      Swal.fire(
        'Observacion faltante',
        'asegurate de indicar una observacion valida',
        'error'
      )
      pasa = 0;
    }

    function enviarDatosAper(){
      let datos = new FormData(document.getElementById('datosApertura'));
      let envio = new XMLHttpRequest();
      envio.open("POST","../includes/cajas.php",false);
      envio.send(datos);
      if(envio.status == 200){
        // console.log(envio.responseText);
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          if(res.mensaje == "operationSuccess"){
            //se inserto el movimiento
            Swal.fire(
              'Apertura realizada',
              'Ya es posible comenzar a cajear, Suerte!',
              'success'
            ).then(function(){
              location.reload();
            })
          }else{
            //ya existe el movimiento
            Swal.fire(
              'Cuidado!',
              'Ya existe una apertura del dia, reporte a soporte',
              'error'
            )
          }
        }else{
          //error en la consulta de usuario
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
    }

    
  })
}

function addTrabajo(trabajo){
  //funcion para agregar al carrito un metodo de cobro
  let datos = new FormData();
  datos.append('addTrabajoCobro',trabajo);

  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/cajas.php',false);
  envio.send(datos);

  if(envio.status == 200){
    let res = envio.responseText.split("+-_-+");
    if(res[0]== "operationSuccess"){
      let contenido = res[1];
      let totalVenta = res[2];
      let totalArti = res[3];

      document.getElementById('cantenidoProds').innerHTML = contenido;
      document.getElementById('totalVentaProds').innerHTML = "Pagar $"+totalVenta;
      document.getElementById('numArtiVenta').innerHTML = "("+totalArti+" Articulos)";
      document.getElementById("totalCobroVenta").value = totalVenta;
      document.getElementById("totalVentanaCobro").innerHTML = "<strong>$"+totalVenta+"</strong>";
    }else{
      let err = res[1];
      Swal.fire(
        'Ha ocurrido un error',
        'verificar: '+err,
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
}