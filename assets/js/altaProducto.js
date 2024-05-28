
//function para mandar el formulario de alta

let btnEnvia = document.getElementById('btnSaveProducto');
btnEnvia.addEventListener("click", function(){

  let datos = new FormData(document.getElementById('dataProducto'));
  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/altaProducto.php',false);

  envio.send(datos);

  if(envio.status == 200){
    // console.log(envio.responseText);
    let res = envio.responseText;
    res = JSON.parse(res);
    console.log(res);
    if(res.mensaje == "operationSuccess"){
      //se inserto correctamente, preguntamos si registra otro
      //o es todo
      Swal.fire({
        title: 'Registro Completo',
        text: 'Deseas registrar otro producto?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No, terminar'
      }).then((result)=>{
        if(result.isConfirmed){
          //registramos otro
          location.reload();
        }else{
          window.location = "verProductos.php";
        }
      })
    }else{
      //ocurrio un error al insertar los datos
      let err = res.mensaje;
      Swal.fire(
        'Ha ocurrido un error',
        err,
        'error'
      )
    }
  }else{
    Swal.fire(
      'Servidor Inalcansable',
      'Verifica tuconexion a internet, si el problema persiste contacta a soiporte',
      'error'
    )
  }
})


let cats = document.getElementById('contentCats').value;
if(cats == "noContiene"){
  Swal.fire({
    title: 'Sin Categorias Registradas',
    text: 'Antes de continuar, asegurate de registrar una categoria.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Registrar Ahora',
    cancelButtonText: 'Cancelar',
  }).then((result)=>{
    if(result.isConfirmed){
      window.location = "altaCategoria.php";
    }
  })
}