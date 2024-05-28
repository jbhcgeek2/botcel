let btnAgregar = document.getElementById('saveMov');
btnAgregar.addEventListener('click', function(){
  //recolectamos los datos del formulario
  let datos = new FormData(document.getElementById('dataMovArti'));
  
  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/movsProds.php',false);
  envio.send(datos);

  if(envio.status == 200){
    console.log(envio.responseText);
    let res = JSON.parse(envio.responseText);
    if(res.status == "ok"){
      //se procdeso todo bien chido
      Swal.fire(
        'Movimiento aplicado',
        'Se inserto correctamente la operacion',
        'success'
      ).then(function(){
        location.reload();
      })
    }else{
      //ocurrio un error en la operacion
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
      'Ha ocurrido un error'
    )
  }

});


function getDataProd(dato,campo){
  //funcion para consultar la informacion de un articulo
  //primero consultamos que datos esta haciendo referencia
  let datos = new FormData();
  datos.append('campoBus',campo);
  datos.append('valorBus',dato);
  let suc = document.getElementById("sucursal").value;
  //verificamos si esta seleccionada una sucursal
  if(suc > 0){
    datos.append('sucursalBus',suc);
  }

  if(dato != "" && campo != ""){
    let envio = new XMLHttpRequest();
    envio.open("POST","../includes/movsProds.php",false);
    envio.send(datos);
    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      console.log(res);
      if(res.status == "ok"){
        let cantidadActual = res.data.existenciaSucursal;
        let precioUni = res.data.precioUnitario;
        let precioComp = res.data.precioCompra;
        let prodId = res.data.idArticulo;
        if(campo == "codProducto"){
          document.getElementById("producto").value = prodId;
        }

        if(campo == "prodModal"){
          //cuando se trate del modal de traspasos insertaremos los datos del
          //producto en una seccion distinta
          
        }else{
          document.getElementById('actActual').value = cantidadActual;
          document.getElementById('preActual').value = precioUni;
          document.getElementById('preCompra').value = precioComp;
          //habilitamo0s los campos de captura
          document.getElementById('cantidadMov').removeAttribute('disabled');
          document.getElementById('precioCompra').removeAttribute('disabled');
          document.getElementById('totalCompra').removeAttribute('disabled');
        }
  
        
        
  
      }else{
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrido un error',
          'Verificar: '+err,
          'error'
        )
      }
    }else{
      //error en la consulta
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      )
    }
  }
  

  


  
}


let changePrecio = document.getElementById("cambiarPrecio");
changePrecio.addEventListener('click', function(){
  if(changePrecio.checked){
    document.getElementById("preActual").removeAttribute("disabled");
  }else{
    //si no, lo deshabilitamos
    document.getElementById("preActual").setAttribute("disabled",true);
  }
});



function calculaTotal(){
  let cantidad = document.getElementById('cantidadMov').value;
  let precio = parseFloat(document.getElementById('precioCompra').value);
  let precioAnterior = parseFloat(document.getElementById('preActual').value);

  if(cantidad != "" && precio > 0){
    let total = parseFloat(precio * cantidad);
    document.getElementById('totalCompra').value  = total;
  }

  if(precio > precioAnterior){
    //si el precio de compra actual es mayor al anterior, le sugerimos un cambio
    Swal.fire({
      title: 'Accio Sugerida',
      text: 'Se sugiere actualizar el precio unitario de venta, deseas cambiarlo?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, cambiar',
      cancelButtonText: 'No cambiar'
    }).then((result) => {
      if(result.isConfirmed){
        document.getElementById("cambiarPrecio").checked = true;
        document.getElementById("preActual").removeAttribute("disabled");
      }else{
        //no se cambia, cntinuamos con el proceso
      }
    })
  }
}


let btnAdd = document.getElementById('addMov');
btnAdd.addEventListener('click', function(){
  //tomamos la informacion del movimiento
  let tipoMov = document.getElementById("tipoMov").value;
  let prodId = document.getElementById("producto").value;
  let prodName = document.getElementById("producto");
  prodName = prodName.options[prodName.selectedIndex].text;
  let prov = document.getElementById('proveedorMov').value;
  let sucId = document.getElementById("sucursal").value;
  let sucIdName = document.getElementById("sucursal");
  sucIdName = sucIdName.options[sucIdName.selectedIndex].text;
  let cantidad = parseFloat(document.getElementById("cantidadMov").value);
  let precioCom = document.getElementById("precioCompra").value;
  let totalCompra = parseFloat(document.getElementById("totalCompra").value);
  let numRows = parseInt(document.getElementById("numRowsTab").value);
  let preCompraOrigi = document.getElementById("preCompra").value;

  let cambiaPre = document.getElementById("cambiarPrecio");
  let valorNuevoPre = document.getElementById("preActual").value;
  let nuevoPre = "no";
  if(cambiaPre.checked){
    nuevoPre = "si";
  }

  if(precioCompra <= valorNuevoPre){
    Swal.fire({
      title: 'Incongruencia en los datos',
      text: 'El precio de venta es menor al precio de compra, deseas continuar?.',
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Continuar',
    }).then((result) => {
      if(result.isConfirmed){
        //el usuario continuar de todos modos
        numRows = numRows+1;
        let sumCantidad = cantidad + parseFloat(document.getElementById("numTotArti").value);
        let sumMonto = 0;
      
        if(tipoMov == "Entrada"){
          sumMonto = totalCompra + parseFloat(document.getElementById("totCompra").value);
        }else if(tipoMov == "Salida"){
          sumMonto = parseFloat(document.getElementById("totCompra").value) - totalCompra;
        }
      
        
      
        //agregamos la tabla
      
        let tabla = `
          
          <tr id="rowMov${numRows}">
            <input type="hidden" id="tipoMovTab${numRows}" name="tipoMovTab${numRows}" value="${tipoMov}">
            <input type="hidden" id="prodIdTab${numRows}" name="prodIdTab${numRows}" value="${prodId}">
            <input type="hidden" id="provIdTab${numRows}" name="provIdTab${numRows}" value="${prov}">
            <input type="hidden" id="sucIdMovTab${numRows}" name="sucIdMovTab${numRows}" value="${sucId}">
            <input type="hidden" id="cantMovTab${numRows}" name="cantMovTab${numRows}" value="${cantidad}">
            <input type="hidden" id="precioComTab${numRows}" name="precioComTab${numRows}" value="${precioCom}">
            <input type="hidden" id="totComTab${numRows}" name="totComTab${numRows}" value="${totalCompra}">
            <input type="hidden" id="cambiaPrecio${numRows}" name="cambiaPrecio${numRows}" value="${nuevoPre}">
            <input type="hidden" id="valorNuevoPre${numRows}" name="valorNuevoPre${numRows}" value="${valorNuevoPre}">
            
            <td>${tipoMov}</td>
            <td>${prodName}</td>
            <td>${cantidad}</td>
            <td>${sucIdName}</td>
            <td>${totalCompra}</td>
            <td>
              <a href="#!" class="btn danger">Borrar</a>
            </td>
          </tr>
        `;
      
        //insertamos el nuevo campo a la tabla
      
        document.getElementById("resMovs").insertAdjacentHTML("beforeend",tabla);
        //una vez insertado los valores, reseteamos los campos
        document.getElementById("tipoMov").value = "";
        document.getElementById("producto").value = "";
        document.getElementById("producto").value = "";
        // document.getElementById('proveedorMov').value = "";
        document.getElementById("sucursal").value = "";
        document.getElementById("cantidadMov").value = "";
        document.getElementById("precioCompra").value = "";
        document.getElementById("totalCompra").value = "";
        document.getElementById("actActual").value = "";
        document.getElementById("preActual").value = "";
        document.getElementById("preCompra").value = "";
        document.getElementById("cantidadMov").disabled = true;
        document.getElementById("precioCompra").disabled = true;
        document.getElementById("totalCompra").disabled = true;
        document.getElementById("preActual").disabled = true;
        document.getElementById("numRowsTab").value = numRows;
      
        document.getElementById("numTotArti").value = sumCantidad;
        document.getElementById("totCompra").value = sumMonto;
      
        //fin swal confirmed
      }
    })
  }else{
    //si no causa conflico, continuamos normalmente
    //el usuario continuar de todos modos
    numRows = numRows+1;
    let sumCantidad = cantidad + parseFloat(document.getElementById("numTotArti").value);
    let sumMonto = 0;
  
    if(tipoMov == "Entrada"){
      sumMonto = totalCompra + parseFloat(document.getElementById("totCompra").value);
    }else if(tipoMov == "Salida"){
      sumMonto = parseFloat(document.getElementById("totCompra").value) - totalCompra;
    }
  
    
  
    //agregamos la tabla
  
    let tabla = `
      
      <tr id="rowMov${numRows}">
        <input type="hidden" id="tipoMovTab${numRows}" name="tipoMovTab${numRows}" value="${tipoMov}">
        <input type="hidden" id="prodIdTab${numRows}" name="prodIdTab${numRows}" value="${prodId}">
        <input type="hidden" id="provIdTab${numRows}" name="provIdTab${numRows}" value="${prov}">
        <input type="hidden" id="sucIdMovTab${numRows}" name="sucIdMovTab${numRows}" value="${sucId}">
        <input type="hidden" id="cantMovTab${numRows}" name="cantMovTab${numRows}" value="${cantidad}">
        <input type="hidden" id="precioComTab${numRows}" name="precioComTab${numRows}" value="${precioCom}">
        <input type="hidden" id="totComTab${numRows}" name="totComTab${numRows}" value="${totalCompra}">
        <input type="hidden" id="cambiaPrecio${numRows}" name="cambiaPrecio${numRows}" value="${nuevoPre}">
        <input type="hidden" id="valorNuevoPre${numRows}" name="valorNuevoPre${numRows}" value="${valorNuevoPre}">
        
        <td>${tipoMov}</td>
        <td>${prodName}</td>
        <td>${cantidad}</td>
        <td>${sucIdName}</td>
        <td>${totalCompra}</td>
        <td>
          <a href="#!" class="btn danger">Borrar</a>
        </td>
      </tr>
    `;
  
    //insertamos el nuevo campo a la tabla
  
    document.getElementById("resMovs").insertAdjacentHTML("beforeend",tabla);
    //una vez insertado los valores, reseteamos los campos
    document.getElementById("tipoMov").value = "";
    document.getElementById("producto").value = "";
    document.getElementById("producto").value = "";
    // document.getElementById('proveedorMov').value = "";
    document.getElementById("sucursal").value = "";
    document.getElementById("cantidadMov").value = "";
    document.getElementById("precioCompra").value = "";
    document.getElementById("totalCompra").value = "";
    document.getElementById("actActual").value = "";
    document.getElementById("preActual").value = "";
    document.getElementById("preCompra").value = "";
    document.getElementById("cantidadMov").disabled = true;
    document.getElementById("precioCompra").disabled = true;
    document.getElementById("totalCompra").disabled = true;
    document.getElementById("preActual").disabled = true;
    document.getElementById("numRowsTab").value = numRows;
  
    document.getElementById("numTotArti").value = sumCantidad;
    document.getElementById("totCompra").value = sumMonto;
  
    //fin swal confirmed
  }


});

function traspasoModal(valor){
  if(valor == "Traspaso"){
    //uinicamente estar activa en la seccion de traspasos y 
    //mostraremos un modal en donde se realizara la operacion
    
    //verificamos si tiene mas de una sucursal
    let numSuc = document.getElementById("sucursal").options.length;
    if(numSuc > 2){
      const myModal = new bootstrap.Modal("#modalTraspaso", {
        keyboard:false,
        focus: true
      })
      const modalTraspaso = document.getElementById('modalTraspaso');
      myModal.show(modalTraspaso);
    }else{
      //no puede usar este metodo por que solo tiene una sucursal
      Swal.fire({
        title: 'Operacion no permitida',
        text: 'Para utilizar esta funcion debes contar con 2 o mas sucursales.',
        icon: 'warning',
      }).then(function(){
        location.reload();
      })
    }


    
  }
}

function procesaTraspaso(){
  //antes de continuar, preguntamos si esta deacuerdo

  Swal.fire({
    title: 'Procesar Traspaso',
    text: 'Estas seguro de procesar el traspaso?',
    iconHtml: '?',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Procesar'
  }).then((result)=>{
    if(result.isConfirmed){
      //procesamos el traspaso
      let producto = document.getElementById('prodModal').value;
      let sucOrigi = document.getElementById('sucursalOrigen').value;
      let sucDesti = document.getElementById('sucursalDestino').value;
      let cantidadTras = document.getElementById('cantidadMovSuc').value;
      let tipoComTras = document.getElementById('tipoCompTras').value;
      let fecTras = document.getElementById('fechaTras').value;
      let numComp = document.getElementById('numComproTras').value;

      if(producto != "" && sucOrigi != "" && sucDesti != "" && cantidadTras != ""){
        if(sucOrigi != sucDesti){
          let datos = new FormData();
          datos.append("prodModalTras",producto);
          datos.append("sucOriTras",sucOrigi);
          datos.append("sucDesTras",sucDesti);
          datos.append("cantidadTras",cantidadTras);
          datos.append("tipoComproTras",tipoComTras);
          datos.append("numComproTras",numComp);
          datos.append("fechaTras",fecTras);
  
          let envio = new XMLHttpRequest();
          envio.open("POST","../includes/movsProds.php",false);
          envio.send(datos);
  
          if(envio.status == 200){
            console.log(envio.responseText);
            let res = JSON.parse(envio.responseText);
            if(res.status == "ok"){
              Swal.fire(
                'Traspaso Realizado',
                'Se realizo correctamente el traspaso',
                'success'
              ).then(function(){
                location.reload();
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
            //error de comunicacion
            Swal.fire(
              'Servidor Inalcansable',
              'Verifica tu conexion a internet',
              'error'
            )
          }
        }else{
          //las sucursales son iguales
          Swal.fire(
            'Incongruencia en los datos',
            'Las sucursales no deben ser las mismas, verificalo',
            'warning'
          )
        }
        
      }else{
        //datos vacios
        Swal.fire(
          'Datos Incorrectos',
          'Verifica que los campos contengan datos capturados',
          'error'
        )
      }
    }else{
      //cancelamos
      location.reload();
    }
  })
}