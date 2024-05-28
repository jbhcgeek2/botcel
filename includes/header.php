<?php 

	session_start();
	require_once("includes/usuarios.php");
	require("includes/conexion.php");
	//consultamos la empresa de la sesion

	//verificaremos la existencia de la sesion del usuario
	$usuario = $_SESSION['usuarioPOS'];
	if(empty($usuario)){
		//usuario vacio
		header('location:login.php');
	}else{
		$empresa = datoEmpresaSesion($usuario,"id");
		$empresa = json_decode($empresa);
		$idEmpresaSesion = $empresa->dato;

		$empresa2= datoEmpresaSesion($usuario,"nombre");
		$empresa2 = json_decode($empresa2);
		$nombrEmpresa = $empresa2->dato;

		$empresa3 = datoEmpresaSesion($usuario,"logo");
		$empresa3 = json_decode($empresa3);
		$logoEmp = $empresa3->dato;

		$sucursal = getSucursalUsuario($usuario);
		$sucursal = json_decode($sucursal);
		$nombreSucursal = $sucursal->dato;

		$dataUSer = getDataUser($usuario,$idEmpresaSesion);
		$dataUSer = json_decode($dataUSer);
		$idSucursalN = $dataUSer->sucursalID;

		$tipoRol = verTipoUsuario($usuario);
		$tipoUsuario = json_decode($tipoRol);
		$rolUsuario = "";
		// print_r($tipoUsuario);
		if($tipoUsuario->status == "ok"){
			$rolUsuario = $tipoUsuario->data;
		}else{
			$rolUsuario = "error";
		}

		// $datosUsuario = json_decode(getDataUser($usuario));
		// if($datosUsuario->status == "ok"){
			
		// }else{
		// 	//ERROR EN LA CONSULTA
		// }
	}

	

?>

<header class="app-header fixed-top">	   	            
  <div class="app-header-inner">  
	  <div class="container-fluid py-2">
		  <div class="app-header-content"> 
		    <div class="row justify-content-between align-items-center">
			        
          <div class="col-auto">
            <a id="sidepanel-toggler" class="sidepanel-toggler d-inline-block d-xl-none" href="#">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img"><title>Menu</title><path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" d="M4 7h22M4 15h22M4 23h22"></path></svg>
            </a>
          </div><!--//col-->

          <div class="search-mobile-trigger d-sm-none col">
            <i class="search-mobile-trigger-icon fa-solid fa-magnifying-glass"></i>
          </div><!--//col-->

                
		      <div class="app-utilities col-auto">
			      
							<?php 
								if($rolUsuario == "Administrador"){
									?>
									<div class="app-utility-item">
										<a href="settings.php" title="Settings">
											<!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z"/>
												<path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z"/>
											</svg>
										</a>
									</div><!--//app-utility-item-->
									<?php
								}
								
							?>
              
			            
              <div class="app-utility-item app-user-dropdown dropdown">
                <a class="dropdown-toggle" id="user-dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
									<img src="<?php echo $logoEmp; ?>" alt="Perfil" class="imagenPerfil">
								</a>
                <ul class="dropdown-menu" aria-labelledby="user-dropdown-toggle">
                  <li><a class="dropdown-item" href="account.php">Mi Cuenta</a></li>
									<?php 
										//verificamos el tipo de usuario debe ser administrador
										//para ver la seccion de configuracion
										if($rolUsuario == "Administrador"){
											echo '<li><a class="dropdown-item" href="settings.php">Configuracion</a></li>';
										}
									?>
                  
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="logOut.php">Cerrar Sesion</a></li>
                </ul>
              </div><!--//app-user-dropdown--> 

		      </div><!--//app-utilities-->

		    </div><!--//row-->
	    </div><!--//app-header-content-->
	  </div><!--//container-fluid-->
  </div><!--//app-header-inner-->


  
        <div id="app-sidepanel" class="app-sidepanel"> 
	        <div id="sidepanel-drop" class="sidepanel-drop"></div>
	        <div class="sidepanel-inner d-flex flex-column">
		        <a href="#" id="sidepanel-close" class="sidepanel-close d-xl-none">&times;</a>
		        
		        
			    	<nav id="app-nav-main" class="app-nav app-nav-main flex-grow-1">
				    	<ul class="app-menu list-unstyled accordion" id="menu-accordion">
					    	<li class="nav-item">
					        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
					        <a class="nav-link active" href="index.php">
						        <span class="nav-icon">
						        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-house-door" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" d="M7.646 1.146a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 .146.354v7a.5.5 0 0 1-.5.5H9.5a.5.5 0 0 1-.5-.5v-4H7v4a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .146-.354l6-6zM2.5 7.707V14H6v-4a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v4h3.5V7.707L8 2.207l-5.5 5.5z"/>
											<path fill-rule="evenodd" d="M13 2.5V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
										</svg>
						        </span>
		                <span class="nav-link-text">Inicio</span>
					        </a><!--//nav-link-->
					    	</li><!--//nav-item-->

					    	


					    	<li class="nav-item">
					        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
					        <a class="nav-link" href="verTrabajos.php">
						        <span class="nav-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-data" viewBox="0 0 16 16">
											<path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0z"/>
											<path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
											<path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
										</svg>
						        </span>
		                <span class="nav-link-text">Ver Trabajos</span>
					        </a><!--//nav-link-->
					    	</li><!--//nav-item-->
								<li class="nav-item">
									<a href="altaTrabajo.php" class="nav-link">
										<span class="nav-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-plus" viewBox="0 0 16 16">
												<path fill-rule="evenodd" d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7"/>
												<path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
												<path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
											</svg>
										</span>
										<span class="nav-link-text">Nuevo Trabajo</span>
									</a>
								</li>

								

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-cliente" aria-expanded="false" aria-controls="submenu-cliente">
										<span class="nav-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
												<path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
											</svg>
										</span>
										<span class="nav-link-text">Clientes</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
											</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-cliente" class="collapse submenu submenu-cliente" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="clientes.php">Ver Clientes</a></li>
											<li class="submenu-item"><a class="submenu-link" href="altaCliente.php">Nuevo Cliente</a></li>
										</ul>
									</div>
								</li>

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-inventario" aria-expanded="false" aria-controls="submenu-cliinventarioente">
										<span class="nav-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-boxes" viewBox="0 0 16 16">
												<path d="M7.752.066a.5.5 0 0 1 .496 0l3.75 2.143a.5.5 0 0 1 .252.434v3.995l3.498 2A.5.5 0 0 1 16 9.07v4.286a.5.5 0 0 1-.252.434l-3.75 2.143a.5.5 0 0 1-.496 0l-3.502-2-3.502 2.001a.5.5 0 0 1-.496 0l-3.75-2.143A.5.5 0 0 1 0 13.357V9.071a.5.5 0 0 1 .252-.434L3.75 6.638V2.643a.5.5 0 0 1 .252-.434zM4.25 7.504 1.508 9.071l2.742 1.567 2.742-1.567zM7.5 9.933l-2.75 1.571v3.134l2.75-1.571zm1 3.134 2.75 1.571v-3.134L8.5 9.933zm.508-3.996 2.742 1.567 2.742-1.567-2.742-1.567zm2.242-2.433V3.504L8.5 5.076V8.21zM7.5 8.21V5.076L4.75 3.504v3.134zM5.258 2.643 8 4.21l2.742-1.567L8 1.076zM15 9.933l-2.75 1.571v3.134L15 13.067zM3.75 14.638v-3.134L1 9.933v3.134z"/>
											</svg>
										</span>
										<span class="nav-link-text">Inventario</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
											</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-inventario" class="collapse submenu submenu-inventario" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="verProductos.php">Ver Productos</a></li>
											<?php 
												if($rolUsuario == "Administrador"){
													?>
													<li class="submenu-item"><a class="submenu-link" href="altaProducto.php">Registrar Productos</a></li>
													<li class="submenu-item"><a class="submenu-link" href="entradaMercancia.php">Movimientos de Mercancia</a></li>
													<li class="submenu-item"><a class="submenu-link" href="verCategorias.php">Ver Categorias</a></li>
													<li class="submenu-item"><a class="submenu-link" href="altaCategoria.php">Registrar Categorias</a></li>
													<?php
												}
											?>
										</ul>
									</div>
								</li>

								

								<?php 
									if($rolUsuario == "Administrador"){
										?>
										<li class="nav-item has-submenu">
											<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-servicio" aria-expanded="false" aria-controls="submenu-cliinventarioente">
												<span class="nav-icon">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-usb-symbol" viewBox="0 0 16 16">
													<path d="m7.792.312-1.533 2.3A.25.25 0 0 0 6.467 3H7.5v7.319a2.5 2.5 0 0 0-.515-.298L5.909 9.56A1.5 1.5 0 0 1 5 8.18v-.266a1.5 1.5 0 1 0-1 0v.266a2.5 2.5 0 0 0 1.515 2.298l1.076.461a1.5 1.5 0 0 1 .888 1.129 2.001 2.001 0 1 0 1.021-.006v-.902a1.5 1.5 0 0 1 .756-1.303l1.484-.848A2.5 2.5 0 0 0 11.995 7h.755a.25.25 0 0 0 .25-.25v-2.5a.25.25 0 0 0-.25-.25h-2.5a.25.25 0 0 0-.25.25v2.5c0 .138.112.25.25.25h.741a1.5 1.5 0 0 1-.747 1.142L8.76 8.99a3 3 0 0 0-.26.17V3h1.033a.25.25 0 0 0 .208-.389L8.208.312a.25.25 0 0 0-.416 0"/>
												</svg>
												</span>
												<span class="nav-link-text">Servicios</span>
												<span class="submenu-arrow">
													<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
													</svg>
												</span><!--//submenu-arrow-->
											</a>
											<div id="submenu-servicio" class="collapse submenu submenu-servicio" data-bs-parent="#menu-accordion">
												<ul class="submenu-list list-unstyled">
													<li class="submenu-item"><a class="submenu-link" href="verServicios.php">Ver Servicios</a></li>
													<li class="submenu-item"><a class="submenu-link" href="altaServicio.php">Registrar Servicio</a></li>
													<li class="submenu-item"><a class="submenu-link" href="verCatServicio.php">Ver Categorias de Servicio</a></li>
													<li class="submenu-item"><a class="submenu-link" href="altaCatServicio.php">Nueva Categoria de Servicios</a></li>
												</ul>
											</div>
										</li>

										<li class="nav-item has-submenu">
											<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-sucursales" aria-expanded="false" aria-controls="submenu-cliinventarioente">
												<span class="nav-icon">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-buildings" viewBox="0 0 16 16">
														<path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/>
														<path d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z"/>
													</svg>
												</span>
												<span class="nav-link-text">Sucursales</span>
												<span class="submenu-arrow">
													<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
													</svg>
												</span><!--//submenu-arrow-->
											</a>
											<div id="submenu-sucursales" class="collapse submenu submenu-sucursales" data-bs-parent="#menu-accordion">
												<ul class="submenu-list list-unstyled">
													<li class="submenu-item"><a class="submenu-link" href="verSucursales.php">Ver Sucursales</a></li>
													<li class="submenu-item"><a class="submenu-link" href="altaSucursal.php">Registrar Sucursal</a></li>
												</ul>
											</div>
										</li>

										<li class="nav-item has-submenu">
											<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-proveedores" aria-expanded="false" aria-controls="submenu-cliinventarioente">
												<span class="nav-icon">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bus-front-fill" viewBox="0 0 16 16">
														<path d="M16 7a1 1 0 0 1-1 1v3.5c0 .818-.393 1.544-1 2v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5V14H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2a2.5 2.5 0 0 1-1-2V8a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1V2.64C1 1.452 1.845.408 3.064.268A44 44 0 0 1 8 0c2.1 0 3.792.136 4.936.268C14.155.408 15 1.452 15 2.64V4a1 1 0 0 1 1 1zM3.552 3.22A43 43 0 0 1 8 3c1.837 0 3.353.107 4.448.22a.5.5 0 0 0 .104-.994A44 44 0 0 0 8 2c-1.876 0-3.426.109-4.552.226a.5.5 0 1 0 .104.994M8 4c-1.876 0-3.426.109-4.552.226A.5.5 0 0 0 3 4.723v3.554a.5.5 0 0 0 .448.497C4.574 8.891 6.124 9 8 9s3.426-.109 4.552-.226A.5.5 0 0 0 13 8.277V4.723a.5.5 0 0 0-.448-.497A44 44 0 0 0 8 4m-3 7a1 1 0 1 0-2 0 1 1 0 0 0 2 0m8 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m-7 0a1 1 0 0 0 1 1h2a1 1 0 1 0 0-2H7a1 1 0 0 0-1 1"/>
													</svg>
												</span>
												<span class="nav-link-text">Proveedores</span>
												<span class="submenu-arrow">
													<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
													</svg>
												</span><!--//submenu-arrow-->
											</a>
											<div id="submenu-proveedores" class="collapse submenu submenu-proveedores" data-bs-parent="#menu-accordion">
												<ul class="submenu-list list-unstyled">
													<li class="submenu-item"><a class="submenu-link" href="verProveedores.php">Ver Proveedores</a></li>
													<li class="submenu-item"><a class="submenu-link" href="altaProveedor.php">Registrar Proveedores</a></li>
												</ul>
											</div>
										</li>

										<li class="nav-item has-submenu">
											<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-categorias" aria-expanded="false" aria-controls="submenu-categorias">
												<span class="nav-icon">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-diagram-2" viewBox="0 0 16 16">
														<path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H11a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 5 7h2.5V6A1.5 1.5 0 0 1 6 4.5zM8.5 5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5zM3 11.5A1.5 1.5 0 0 1 4.5 10h1A1.5 1.5 0 0 1 7 11.5v1A1.5 1.5 0 0 1 5.5 14h-1A1.5 1.5 0 0 1 3 12.5zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm4.5.5a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1 9 12.5zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
													</svg>
												</span>
												<span class="nav-link-text">Categorias</span>
												<span class="submenu-arrow">
													<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
															<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
														</svg>
												</span><!--//submenu-arrow-->
											</a>
											<div id="submenu-categorias" class="collapse submenu submenu-categorias" data-bs-parent="#menu-accordion">
												<ul class="submenu-list list-unstyled">
													<li class="submenu-item"><a class="submenu-link" href="verCategorias.php">Ver Categorias</a></li>
													<li class="submenu-item"><a class="submenu-link" href="altaCategoria.php">Registrar Categorias</a></li>
												</ul>
											</div>
										</li>

										<li class="nav-item has-submenu">
											<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-usuarios" aria-expanded="false" aria-controls="submenu-cliinventarioente">
												<span class="nav-icon">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-gear" viewBox="0 0 16 16">
														<path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m.256 7a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1zm3.63-4.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
													</svg>
												</span>
												<span class="nav-link-text">Usuarios</span>
												<span class="submenu-arrow">
													<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
													</svg>
												</span><!--//submenu-arrow-->
											</a>
											<div id="submenu-usuarios" class="collapse submenu submenu-usuarios" data-bs-parent="#menu-accordion">
												<ul class="submenu-list list-unstyled">
													<li class="submenu-item"><a class="submenu-link" href="verUsuarioEmpr.php">Ver Usuarios</a></li>
													<li class="submenu-item"><a class="submenu-link" href="altaUsuariosEmpr.php">Registrar Usuarios</a></li>
												</ul>
											</div>
										</li>

										<?php
									}
								?>
								

					    		    
				    </ul><!--//app-menu-->
			    </nav><!--//app-nav-->

			    <div class="app-sidepanel-footer">
				    <nav class="app-nav app-nav-footer">
					    <ul class="app-menu footer-menu list-unstyled">
						    
						    
						    <li class="nav-item">
						        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
						        <a class="nav-link" href="logOut.php">
							        <span class="nav-icon">
												<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-file-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M12 1H4a1 1 0 0 0-1 1v10.755S4 11 8 11s5 1.755 5 1.755V2a1 1 0 0 0-1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z"/>
													<path fill-rule="evenodd" d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
												</svg>
							        </span>
			                <span class="nav-link-text">Salir</span>
						        </a><!--//nav-link-->
						    </li><!--//nav-item-->
					    </ul><!--//footer-menu-->
				    </nav>
			    </div><!--//app-sidepanel-footer-->
		       
	        </div><!--//sidepanel-inner-->
	    </div><!--//app-sidepanel-->
    </header><!--//app-header-->

		<script>
			let selpedido = document.getElementById('selPedidoNav');
			selpedido.addEventListener('change',function(){
				let trabajo = selpedido.value;

				window.location = 'verInfoTrabajo.php?data='+trabajo;
			})
		</script>