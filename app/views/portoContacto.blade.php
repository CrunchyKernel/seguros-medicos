@extends('layout.porto')

@section('contenido')
	<style>
		/* Extra breathing room between top-level sections */
		.con-section {
			margin-bottom: 5rem;
		}

		.con-card {
			border-radius: 25px;
			border: none;
			background-color: #f5f5f5;
		}

		.con-form .form-group {
			margin-bottom: 1.25rem;
		}

		.con-form .form-label {
			display: block;
			font-weight: 600;
			margin-bottom: 0.35rem;
		}

		.con-form .form-control {
			padding: 0.65rem 0.9rem;
			height: auto;
		}

		.con-datos p {
			text-align: left;
			margin-bottom: 0.35rem;
		}

		.con-mapa {
			width: 100%;
			height: 100%;
			min-height: 260px;
			border: 0;
			border-radius: 15px;
			overflow: hidden;
			background-color: rgb(229, 227, 223);
		}
	</style>

	<div class="container">
		<!-- HERO -->
		<div class="card shadow-sm con-section mt-5"
			style="border-radius: 25px; border: none; background: linear-gradient(180deg, rgba(255,255,255,1) 0%, #B4E0E8 100%);">
			<div class="px-4 px-md-5 py-5 text-center">
				<h1 class="mt-0">Contacto</h1>

				<p class="lead mb-0 mx-auto" style="max-width: 760px; text-align: center;">Nos hemos especializado en el ramo de Gastos Médicos Mayores, con lo que hemos participado en los comités de mejora de producto de una aseguradora como consejeros del producto.</p>
			</div>
		</div>
		<!-- FORMULARIO Y DATOS -->

		<div class="row con-section">
			<div class="col-lg-7 mb-4 mb-lg-0">
				<div class="card shadow-sm p-4 p-md-5 h-100 con-card con-form">
					<h3 class="mt-0">Formulario <strong>contacto</strong></h3>

					<div id="error"></div>

					<form id="contactoForm" name="contactoForm" method="post">
						<div class="form-group">
							<label for="nombre" class="form-label">Nombre completo</label>
							<input placeholder="Mi nombre" id="nombre" name="nombre" type="text" autocomplete="name" class="form-control nr-form-control validate">
						</div>
						<div class="form-group">
							<label for="e_mail" class="form-label">Correo electrónico</label>
							<input placeholder="micorreo@dominio.com" id="e_mail" name="e_mail" type="email" autocomplete="email" class="form-control nr-form-control validate">
						</div>
						<div class="form-group">
							<label for="mensaje" class="form-label">Mensaje</label>
							<textarea placeholder="Contenido del mensaje" id="mensaje" name="mensaje" class="form-control nr-form-control validate" cols="20" rows="7"></textarea>
						</div>
						<button class="btn btn-primary btn-lg enviarContacto" style="border-radius: 25px;">Enviar</button>
					</form>
				</div>
			</div>

			<div class="col-lg-5">
				<div class="card shadow-sm p-4 p-md-5 h-100 con-card con-datos">
					<h3 class="mt-0">Dirección</h3>

					<p><strong>Seguros de Gastos Médicos Mayores</strong></p>

					<p>Av. Inglaterra 2790-3, Guadalajara, Jalisco</p>

					<p>Teléfono: <a href="tel:+523320020170">(33) 200-201-70</a></p>

					<p>Lunes a Viernes 9:00am - 2:00 pm y 4:00pm - 6:00pm</p>

					<p><a href="mailto:info@segurodegastosmedicosmayores.mx">info@segurodegastosmedicosmayores.mx</a></p>

					<div class="mt-3 flex-grow-1 d-flex">
						<iframe id="map" class="con-mapa" title="Nuestra Ubicación" loading="lazy" frameborder="0" src="https://www.google.com/maps/embed/v1/place?q=place_id:Ej1BdiBJbmdsYXRlcnJhIDI3OTAsIFZhbGxhcnRhLCA0NDY5MCBHdWFkYWxhamFyYSwgSmFsLiwgTWV4aWNvIlESTwo0CjIJ2YKN63OuKIQRrpy6UBrqNV4aHgsQ7sHuoQEaFAoSCcEtLB5uriiEETlztajtpqb3DBDmFSoUChIJv2jOY3SuKIQRtoLbAYVzhF4&key=AIzaSyArtT8rdV6F-bEZwPjCQZbgDfPpX0bjK1Y"></iframe>
						<!--div id="map-canvas" style="width: 100%; height:300px; position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div-->
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
	{{HTML::script('https://maps.googleapis.com/maps/api/js?key=AIzaSyArtT8rdV6F-bEZwPjCQZbgDfPpX0bjK1Y')}}
	<script type="text/javascript">
		$(document).ready(function(){
			// El mapa interactivo solo se inicializa si el contenedor existe en la página
			if (document.getElementById('map-canvas')) {
				$('#map-canvas').addClass('loading');
				var latlng = new google.maps.LatLng(20.669815,-103.386472);
				var settings = {
					zoom: 16,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					mapTypeControl: false,
					scrollwheel: true,
					draggable: true,
					styles: [{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]}],
					mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
					navigationControl: true,
					navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
				};

				var map = new google.maps.Map(document.getElementById("map-canvas"), settings);
				google.maps.event.addDomListener(window, "resize", function() {
					var center = map.getCenter();
					google.maps.event.trigger(map, "resize");
					map.setCenter(center);
					$('#map-canvas').removeClass('loading');
				});

				var center = map.getCenter();
				map.setCenter(center);
				$('#map-canvas').removeClass('loading');

				var companyMarker = new google.maps.Marker({
					position: latlng,
					map: map,
					title:"Seguro de Gastos Médicos Mayores",
					zIndex: 3
				});
			}

			$("#contactoForm").submit(function(e){
				e.preventDefault();
				var data = $(this).serialize();
				$.ajax({
					url:'/postContacto',
					method:'post',
					data:data,
					dataType:'html',
					success: function(data, status, jqXhr){
						$("#contactoForm")[0].reset();
						$.notify({message: "Muchas gracias! Nos pondremos en contacto contigo tan pronto como sea posible"}, {type: "success", z_index: 2000});
					}
				});
			});
		});
	</script>
@stop
