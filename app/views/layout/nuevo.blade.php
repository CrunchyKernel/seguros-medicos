<!doctype html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<link rel="shortcut icon" href="{{asset('assets/images/icon/favicon.ico')}}">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Candal|Open+Sans|Raleway&display=swap">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css">
		{{HTML::style('assets/css/nuevo-animate.css')}}
		{{HTML::style('assets/css/nuevo-estilo.css')}}
		<title>{{((isset($metaTitulo)) ? $metaTitulo : '')}}</title>
	</head>
	<body class="tp-124">
		<div class="loading">
			<div class="spinner-border text-secondary" role="status">
				<span class="sr-only">Loading...</span>
			</div>
		</div>
		<div class="container-fluid barra-superior">
			<div class="row">
				<div class="container">
					<div class="row">
						<div class="col align-self-center text-right">
							<i class="far fa-envelope"></i> <a href="mailto:ventas@segurodegastosmedicosmayores.mx" class="blanco">Ventas</a>
							&nbsp;
							<i class="fas fa-phone-alt"></i> <a href="tel:5213320020170" class="blanco">33-200-201-70</a><br>
						</div>
					</div>
				</div>
			</div>
		</div>
		<nav class="navbar navbar-expand-lg navbar-default navbar-light fixed-top top-nav-collapse">
			<div class="container">
				<a class="navbar-brand" href="{{URL::to('/')}}">
					<img src="{{asset('/protectodiez/sgmmPNG/gastosmedicosmayores180.png')}}" alt="Seguro de gastos medicos mayores" class="img-fluid">
				</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Mostrar/Ocultar Menu">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse align-self-end" id="menu">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item"><a href="{{URL::to('/cotizador')}}" class="nav-link active">COTIZADOR</a></li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="aAseguradoras" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aseguradoras</a>
							<div class="dropdown-menu ddm-icons" aria-labelledby="aAseguradoras">
								<ul class="list-inline">
									<li class="d-md-inline pl-3"><a href="{{URL::to('/mapfre')}}"><img src="/assets/images/nuevo/mnu-mapfre.png" clasS="img-fluid"></a></li>
									<li class="d-md-inline pl-3"><a href="{{URL::to('/sisnova')}}"><img src="/assets/images/nuevo/mnu-sisnova.png" clasS="img-fluid"></a></li>
									<li class="d-md-inline pl-3"><a href="{{URL::to('/ve-por-mas')}}"><img src="/assets/images/nuevo/mnu-bx+.png" clasS="img-fluid"></a></li>
									<li class="d-md-inline pl-3 pr-3"><a href="{{URL::to('/plan-seguro')}}"><img src="/assets/images/nuevo/mnu-plan-seguro.png" clasS="img-fluid"></a></li>
								</ul>
							</div>
						</li>
						<li class="nav-item"><a href="{{URL::to('/seguros-maternidad')}}" class="nav-link">MATERNIDAD</a></li>
						<li class="nav-item"><a href="{{URL::to('/blog-principal')}}" class="nav-link">BLOG</a></li>
						<li class="nav-item"><a href="{{URL::to('/preguntas-frecuentes')}}" class="nav-link">PREGUNTAS FRECUENTES</a></li>
						<li class="nav-item"><a href="{{URL::to('/contacto')}}" class="nav-link">CONTACTO</a></li>
					</ul>
				</div>
			</div>
		</nav>
		@yield('contenido')
		<footer id="footer">
			<div class="top-footer">
				<div class="container">
					<div class="row">
						<div class="col-md-4 col-sm-4 marb20">
							<img class="img-fluid rounded" src="{{asset('protectodiez/sgmmPNG/seguro_de_gastos_medicos.png')}}"  alt="seguro de gastosmedicos mayores" />
						</div>
						<div class="col-md-4 col-sm-4 marb20 align-self-end">
							<div class="info-sec">
								Seguros de Gastos Médicos Mayores<br>
								<i class="fas fa-map-marker-alt"></i> Av. Inglaterra 2790-3<br>
								Guadalajara, Jalisco<br>
								<i class="fas fa-phone-alt"></i> <a href="tel:5213320020170" class="blanco">33-200-201-70</a><br>
								<i class="far fa-envelope"></i> <a href="mailto:ventas@segurodegastosmedicosmayores.mx" class="blanco">ventas@segurodegastosmedicosmayores.mx</a>
							</div>
						</div>
						<div class="col-md-4 col-sm-4 marb20 align-self-end">
							<div class="ftr-tle">
								<h4 class="white no-padding">Síguenos</h4>
							</div>
							<div class="info-sec">
								<ul class="social-icon">
									<li class="bglight-blue"><i class="fab fa-facebook-f"></i></li>
									<li class="bgred"><i class="fab fa-google-plus-g"></i></li>
									<li class="bgdark-blue"><i class="fab fa-linkedin-in"></i></li>
									<li class="bglight-blue"><i class="fab fa-twitter"></i></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="footer-line">
				<div class="container">
					<div class="row">
						<div class="col-md-12 text-center">
							Copyright &copy; 2020 Derechos reservados.
						</div>
					</div>
				</div>
			</div>
		</footer>
		<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		<script src="https://kit.fontawesome.com/956bff7830.js"></script>
		{{HTML::script('assets/js/jquery.fittext.js')}}
		{{HTML::script('assets/js/jquery.lettering.js')}}
		{{HTML::script('assets/js/jquery.textillate.js')}}
		{{HTML::script('assets/js/jquery.waypoints.min.js')}}
		{{HTML::script('assets/js/bootstrap-notify.min.js')}}
		<script>
			$(document).ajaxStart(function(){
	   			$(".loading").show();
			});
			$(document).ajaxStop(function(){
				$(".loading").hide();
			});
			$(document).ready(function(){
				// Efectos con texto
				$(".tlt").textillate();
				// Smooth scroll
				$(".navbar a, a.btn-appoint, .quick-info li a, .overlay-detail a").on('click', function(event) {
					var hash = this.hash;
					if (hash) {
						event.preventDefault();
						$('html, body').animate({
							scrollTop: $(hash).offset().top
						}, 900, function() {
							window.location.hash = hash;
						});
					}
				});
				// Waypoints con data-attributes
				$(".wp").each(function(ix, el){
					var wps = $(el).data("wps");
					var offset = $(el).data("wp-offset");
					$(el).waypoint(function(d){
						$.each(wps, function(i, wp){
							$(wp).removeClass("invisible").addClass($(wp).data("wp-class"));
						});
					}, {
						offset: offset
					});
				});
			});
		</script>
		@if($contenido->incluir_cotizador==1)
			<script src="https://www.youtube.com/iframe_api"></script>
			{{HTML::script('assets/js/helpers/cotizador_nuevo.js')}}
		@endif
	</body>
</html>