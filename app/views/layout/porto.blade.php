<!DOCTYPE html>
<html lang="es">

<head>

	<!-- Basic -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<title>{{((isset($metaTitulo)) ? $metaTitulo : '')}}</title>

	<meta name="keywords" content="" />
	<meta name="description" content="">

	<!-- Favicon -->

	<link rel="apple-touch-icon" sizes="57x57" href="{{asset('assets/images/icon/icon/favicon-57x57.png')}}">
	<link rel="apple-touch-icon" sizes="60x60" href="{{asset('assets/images/icon/icon/favicon-60x60.png')}}">
	<link rel="apple-touch-icon" sizes="72x72" href="{{asset('assets/images/icon/icon/favicon-72x72.png')}}">
	<link rel="apple-touch-icon" sizes="76x76" href="{{asset('assets/images/icon/icon/favicon-76x76.png')}}">
	<link rel="apple-touch-icon" sizes="114x114" href="{{asset('assets/images/icon/icon/favicon-114x114.png')}}">
	<link rel="apple-touch-icon" sizes="120x120" href="{{asset('assets/images/icon/icon/favicon-120x120.png')}}">
	<link rel="apple-touch-icon" sizes="144x144" href="{{asset('assets/images/icon/icon/favicon-144x144.png')}}">
	<link rel="apple-touch-icon" sizes="152x152" href="{{asset('assets/images/icon/icon/favicon-152x152.png')}}">
	<link rel="apple-touch-icon" sizes="180x180" href="{{asset('assets/images/icon/icon/favicon-180x180.png')}}">
	<link rel="icon" type="image/png" sizes="192x192" href="{{asset('assets/images/icon/icon/favicon-192x192.png')}}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{asset('assets/images/icon/icon/favicon-32x32.png')}}">
	<link rel="icon" type="image/png" sizes="96x96" href="{{asset('assets/images/icon/icon/favicon-96x96.png')}}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{asset('assets/images/icon/icon/favicon-16x16.png')}}">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="{{asset('assets/images/icon/icon/favicon-144x144.png')}}">

	<!-- Mobile Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">

	<!-- Web Fonts  -->
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab:300,400,700" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
	<link href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css" rel="stylesheet"
		type="text/css">

	<!-- Vendor CSS -->
	{{HTML::style('porto/vendor/bootstrap/css/bootstrap.min.css')}}
	{{HTML::style('porto/vendor/fontawesome-free/css/all.min.css')}}
	{{HTML::style('porto/vendor/animate/animate.min.css')}}
	{{HTML::style('porto/vendor/simple-line-icons/css/simple-line-icons.min.css')}}
	{{HTML::style('porto/vendor/owl.carousel/assets/owl.carousel.min.css')}}
	{{HTML::style('porto/vendor/owl.carousel/assets/owl.theme.default.min.css')}}
	{{HTML::style('porto/vendor/magnific-popup/magnific-popup.min.css')}}

	<!-- Theme CSS -->
	{{HTML::style('porto/css/theme.css')}}
	{{HTML::style('porto/css/theme-elements.css')}}
	{{HTML::style('porto/css/theme-blog.css')}}
	{{HTML::style('porto/css/theme-shop.css')}}

	<!-- Current Page CSS -->
	{{HTML::style('porto/vendor/rs-plugin/css/settings.css')}}
	{{HTML::style('porto/vendor/rs-plugin/css/layers.css')}}
	{{HTML::style('porto/vendor/rs-plugin/css/navigation.css')}}

	<!-- Demo CSS -->
	{{HTML::style('porto/css/demo-insurance.css')}}

	<!-- Skin CSS -->
	{{HTML::style('porto/css/skins/skin-insurance.css?20220202')}}

	<!-- Theme Custom CSS -->
	{{HTML::style('porto/css/custom.css')}}
	{{HTML::style('porto/css/estilo.css?20240125')}}

	<!-- Head Libs -->
	{{HTML::script('porto/vendor/modernizr/modernizr.min.js')}}

	@if(isset($contenido))
		@if($contenido->incluir_cotizador_nuevo == 1)
			{{HTML::style('assets/css/cotizadorNuevo.css?20241113')}}
		@endif
		@if($contenido->incluir_cotizador_nuevo == 2)
			{{HTML::style('assets/css/cotizadorNuevo.css?20241113')}}
		@endif
	@else
		@if(isset($cotizadorNuevo))
			{{HTML::style('assets/css/cotizadorNuevo.css?20241113')}}
		@endif
	@endif
	<!-- Smartsupp Live Chat script -->
	<script type="text/javascript">
		var _smartsupp = _smartsupp || {};
		_smartsupp.key = 'c73c5168cc87f6579e14775a174287403bc2761f';
		window.smartsupp || (function (d) {
			var s, c, o = smartsupp = function () { o.push(arguments) }; o = [];
			s = d.getElementsByTagName('script')[0]; c = d.createElement('script');
			c.type = 'text/javascript'; c.charset = 'utf-8'; c.async = true;
			c.src = 'https://www.smartsuppchat.com/loader.js?'; s.parentNode.insertBefore(c, s);
		})(document);
	</script>
</head>

<body>

	<div class="loading">
		<div class="spinner-border text-azul" role="status">
			<span class="sr-only">Loading...</span>
		</div>
	</div>
	<div class="body">
		<header id="header" class="header-effect-shrink"
			data-plugin-options="{'stickyEnabled': true, 'stickyEffect': 'shrink', 'stickyEnableOnBoxed': true, 'stickyEnableOnMobile': true, 'stickyChangeLogo': true, 'stickyStartAt': 30, 'stickyHeaderContainerHeight': 70}">
			<div class="header-body header-body-bottom-border border-top-0">
				<div class="header-container container">
					<div class="header-row">
						<div class="header-column">
							<div class="header-row">
								<div class="header-logo">
									<a href="{{URL::to('/')}}">
										<!--48 x 108-->
										<img class="logo-img" alt="Seguro de gastos medicos mayores" width="135"
											height="60" data-sticky-width="82" data-sticky-height="40"
											src="{{asset('/protectodiez/sgmmPNG/gastosmedicosmayores180.png')}}">
									</a>
								</div>
							</div>
						</div>
						<div class="header-column justify-content-end">
							<div class="header-row">
								<div class="header-nav header-nav-line header-nav-bottom-line">
									<div
										class="header-nav-main header-nav-main-square header-nav-main-dropdown-no-borders header-nav-main-effect-2 header-nav-main-sub-effect-1">
										<nav class="collapse">
											<ul class="nav nav-pills" id="mainNav">
												<li>
													<a class="nav-link active" href="{{URL::to('/main')}}">
														Inicio
													</a>
												</li>
												<li class="dropdown">
													<?php
if (isset($_SESSION["menuAseguradora"]))
	$finalMenu = $_SESSION["menuAseguradora"];
else {
	$menus = \Gmsitiomenu::where("id_padre", 3)->orderBy("orden", "asc")->get();
	$finalMenu = [];
	foreach ($menus as $menu) {
		$subs = \Gmsitiomenu::where("id_padre", $menu->id_sitio_menu)->orderBy("orden", "asc")->get();
		foreach ($subs as $sub) {
			$subs2 = \Gmsitiomenu::where("id_padre", $sub->id_sitio_menu)->orderBy("orden", "asc")->get();
			$sub["submenu"] = $subs2;
		}
		$menu["submenu"] = $subs;
		$finalMenu[] = $menu;
	}
	$_SESSION["menuAseguradora"] = $finalMenu;
}
														?>
													<a class="nav-link dropdown-toggle" href="#">
														Aseguradoras
													</a>
													<ul class="dropdown-menu">
														@foreach($finalMenu as $menu)
															@if(count($menu->submenu) == 0)
																<li><a href="{{URL::to('/' . $menu->url_amigable)}}"
																		class="dropdown-item">{{$menu->titulo}}</a></li>
															@else
																<li class="dropdown-submenu">
																	<a href="{{URL::to('/' . $menu->url_amigable)}}"
																		class="dropdown-item dropdown-toggle">{{$menu->titulo}}</a>
																	<ul class="dropdown-menu">
																		@foreach($menu->submenu as $submenu)
																			@if(count($submenu->submenu) == 0)
																				<li><a href="{{URL::to('/' . $submenu->url_amigable)}}"
																						class="dropdown-item">{{$submenu->titulo}}</a>
																				</li>
																			@else
																				<li class="dropdown-submenu">
																					<a href="{{URL::to('/' . $submenu->url_amigable)}}"
																						class="dropdown-item dropdown-toggle"
																						data-toggle="dropdown">{{$submenu->titulo}}</a>
																					<ul class="dropdown-menu">
																						@foreach($submenu->submenu as $submenu2)
																							<li><a href="{{URL::to('/' . $submenu2->url_amigable)}}"
																									class="dropdown-item">{{$submenu2->titulo}}</a>
																							</li>
																						@endforeach
																					</ul>
																				</li>
																			@endif
																		@endforeach
																	</ul>
																</li>
															@endif
														@endforeach
													</ul>
												</li>
												<li>
													<a class="nav-link" href="{{URL::to('/seguros-maternidad')}}">
														Maternidad
													</a>
												</li>
												<li>
													<a class="nav-link" href="{{URL::to('/blog-principal')}}">
														Blog
													</a>
												</li>
												<li class="dropdown">
													<?php
if (isset($_SESSION["menuFaq"]))
	$finalMenu = $_SESSION["menuFaq"];
else {
	$menus = \Gmsitiomenu::where("id_padre", 29)->orderBy("orden", "asc")->get();
	$finalMenu = [];
	foreach ($menus as $menu) {
		$subs = \Gmsitiomenu::where("id_padre", $menu->id_sitio_menu)->orderBy("orden", "asc")->get();
		foreach ($subs as $sub) {
			$subs2 = \Gmsitiomenu::where("id_padre", $sub->id_sitio_menu)->orderBy("orden", "asc")->get();
			$sub["submenu"] = $subs2;
		}
		$menu["submenu"] = $subs;
		$finalMenu[] = $menu;
	}
	$_SESSION["menuFaq"] = $finalMenu;
}
														?>
													<a class="nav-link dropdown-toggle" href="#">
														Preguntas Frecuentes
													</a>
													<ul class="dropdown-menu">
														@foreach($finalMenu as $menu)
															@if(count($menu->submenu) == 0)
																<li><a href="{{URL::to('/' . $menu->url_amigable)}}"
																		class="dropdown-item">{{$menu->titulo}}</a></li>
															@else
																<li class="dropdown-submenu">
																	<a href="{{URL::to('/' . $menu->url_amigable)}}"
																		class="dropdown-item dropdown-toggle">{{$menu->titulo}}</a>
																	<ul class="dropdown-menu">
																		@foreach($menu->submenu as $submenu)
																			@if(count($submenu->submenu) == 0)
																				<li><a href="{{URL::to('/' . $submenu->url_amigable)}}"
																						class="dropdown-item">{{$submenu->titulo}}</a>
																				</li>
																			@else
																				<li class="dropdown-submenu">
																					<a href="{{URL::to('/' . $submenu->url_amigable)}}"
																						class="dropdown-item dropdown-toggle"
																						data-toggle="dropdown">{{$submenu->titulo}}</a>
																					<ul class="dropdown-menu">
																						@foreach($submenu->submenu as $submenu2)
																							<li><a href="{{URL::to('/' . $submenu2->url_amigable)}}"
																									class="dropdown-item">{{$submenu2->titulo}}</a>
																							</li>
																						@endforeach
																					</ul>
																				</li>
																			@endif
																		@endforeach
																	</ul>
																</li>
															@endif
														@endforeach
													</ul>
												</li>
												<li class="dropdown">
													<?php
if (isset($_SESSION["menuContacto"]))
	$menus = $_SESSION["menuContacto"];
else {
	$menus = \Gmsitiomenu::where("id_padre", 9)->orderBy("orden", "asc")->get();
	$_SESSION["menuContacto"] = $menus;
}
														?>
													<a class="nav-link" href="{{URL::to('/contacto')}}">
														Contacto
													</a>
													<ul class="dropdown-menu">
														@foreach($menus as $menu)
															<li><a href="{{URL::to('/' . $menu->url_amigable)}}"
																	class="dropdown-item">{{$menu->titulo}}</a></li>
														@endforeach
													</ul>
												</li>
											</ul>
										</nav>
									</div>
									<a href="{{URL::to('/cotizador')}}"
										class="btn btn-outline btn-rounded btn-primary text-1 ml-3 btnCotizador bntContactar">Contactar
										un Asesor</a>
									<a href="{{URL::to('/cotizador')}}"
										class="btn btn-rounded btn-primary text-1 ml-3 btnCotizador">Cotiza tu
										seguro</a>
									<button class="btn header-btn-collapse-nav" data-toggle="collapse"
										data-target=".header-nav-main nav">
										<i class="fas fa-bars"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>

		<div role="main" class="main">
			@yield('contenido')
		</div>
		@include('layout.portoFooter')
	</div>

	<!-- Vendor -->
	{{HTML::script('porto/vendor/jquery/jquery.min.js')}}
	{{HTML::script('porto/vendor/jquery.appear/jquery.appear.min.js')}}
	{{HTML::script('porto/vendor/jquery.easing/jquery.easing.min.js')}}
	{{HTML::script('porto/vendor/jquery.cookie/jquery.cookie.min.js')}}
	{{HTML::script('porto/vendor/popper/umd/popper.min.js')}}
	{{HTML::script('porto/vendor/bootstrap/js/bootstrap.min.js')}}
	{{HTML::script('porto/vendor/common/common.min.js')}}
	{{HTML::script('porto/vendor/jquery.validation/jquery.validate.min.js?20241125')}}
	{{HTML::script('porto/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js')}}
	{{HTML::script('porto/vendor/jquery.gmap/jquery.gmap.min.js')}}
	{{HTML::script('porto/vendor/jquery.lazyload/jquery.lazyload.min.js')}}
	{{HTML::script('porto/vendor/isotope/jquery.isotope.min.js')}}
	{{HTML::script('porto/vendor/owl.carousel/owl.carousel.min.js')}}
	{{HTML::script('porto/vendor/magnific-popup/jquery.magnific-popup.min.js')}}
	{{HTML::script('porto/vendor/vide/jquery.vide.min.js')}}
	{{HTML::script('porto/vendor/vivus/vivus.min.js')}}
	{{HTML::script('porto/js/bootstrap-notify.min.js')}}

	<!-- Theme Base, Components and Settings -->
	{{HTML::script('porto/js/theme.js')}}

	<!-- Current Page Vendor and Views -->
	{{HTML::script('porto/vendor/rs-plugin/js/jquery.themepunch.tools.min.js')}}
	{{HTML::script('porto/vendor/rs-plugin/js/jquery.themepunch.revolution.min.js')}}

	<!-- Current Page Vendor and Views -->
	{{HTML::script('porto/js/views/view.contact.js')}}

	<!-- Demo -->
	{{HTML::script('porto/js/demo-insurance.js')}}

	<!-- Theme Custom -->
	{{HTML::script('porto/js/custom.js')}}

	<!-- Theme Initialization Files -->
	{{HTML::script('porto/js/theme.init.js')}}

	@if(isset($contenido))
		@if($contenido->incluir_cotizador == 1)
			<!--script src="https://www.youtube.com/iframe_api"></script-->
			{{HTML::script('assets/js/helpers/cotizador_nuevo.js?20220208')}}
		@endif
		@if($contenido->incluir_cotizador_nuevo == 1)
			{{HTML::script('assets/js/helpers/jquery.mask.min.js')}}
			{{HTML::script('assets/js/helpers/cotizadorNuevo.js?20241125')}}
		@endif
		@if($contenido->incluir_cotizador_nuevo == 2)
			{{HTML::script('assets/js/helpers/jquery.mask.min.js')}}
			{{HTML::script('assets/js/helpers/cotizadorTest.js?20240927')}}
		@endif
	@else
		@if(isset($cotizadorNuevo))
			{{HTML::script('assets/js/helpers/jquery.mask.min.js')}}
			{{HTML::script('assets/js/helpers/cotizadorNuevo.js?20241125')}}
		@endif
	@endif

	<script>
		$(document).ajaxStart(function () {
			$(".loading").show();
		});
		$(document).ajaxStop(function () {
			$(".loading").hide();
		});
		$("ul.dropdown-menu [data-toggle='dropdown']").on("click", function (event) {
			event.preventDefault();
			event.stopPropagation();
			$(this).siblings().toggleClass("show");
			if (!$(this).next().hasClass('show')) {
				$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
			}
			$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
				$('.dropdown-submenu .show').removeClass("show");
			});
		});
	</script>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-210259844-2"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());

		gtag('config', 'UA-210259844-2');
	</script>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZWP3QWS7FQ"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());

		gtag('config', 'G-ZWP3QWS7FQ');
	</script>

	@yield('js')

	<script type="text/javascript" async
		src="https://d335luupugsy2.cloudfront.net/js/loader-scripts/e39fd218-d19b-4913-882d-ea20b8e25c92-loader.js"></script>
</body>

</html>