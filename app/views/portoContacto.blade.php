@extends('layout.porto')

@section('contenido')
	<div class="page_title">
		<div class="container">
			<h1 class="custom-primary-font font-weight-semibold text-transform-none text-9 text-center mb-5 appear-animation" data-appear-animation="bounceInLeft">Contacto</h1>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<p>Nos hemos especializado en el ramo de Gastos Médicos Mayores, con lo que hemos participado en los comités de mejora de producto de una aseguradora como consejeros del producto.</p>
				<br>
				<h3 class="text-primary">Formulario <strong>contacto</strong></h3>
				<div id="error"></div>
				<form id="contactoForm" name="contactoForm" method="post">
					<div class="form-group">
						<label for="nombre" class="form-label">Nombre completo</label>
						<input placeholder="Mi nombre" id="nombre" name="nombre" type="text" class="form-control validate">
					</div>
					<div class="form-group">
						<label for="e_mail" class="form-label">Correo electrónico</label>
						<input placeholder="micorreo@dominio.com" id="e_mail" name="e_mail" type="email" class="form-control validate">
					</div>
					<div class="form-group">
						<label for="mensaje" class="blocklabel">Mensaje</label>
						<textarea placeholder="Contenido del mensaje" id="mensaje" name="mensaje" class="form-control validate" cols="20" rows="7"></textarea>
					</div>
					<button class="btn btn-primary enviarContacto">ENVIAR</button>
				</form> 
			</div>
			<div class="col-md-6">
				<div class="address-info">
					<h3 class="text-primary">Dirección</h3>
					<ul>
						<li>
							<strong>Seguros de Gastos Médicos Mayores</strong><br>
							Av. Inglaterra 2790-3, Guadalajara, Jalisco<br>
							Teléfono: (33) 200-201-70<br>
							Lunes a Viernes 9:00am - 2:00 pm y 4:00pm - 6:00pm<br>
							<a href="mailto:info@segurodegastosmedicosmayores.mx">info@segurodegastosmedicosmayores.mx</a>
						</li>
					</ul>
				</div>
				<h3 class="text-primary">Nuestra Ubicación</h3>
				<iframe id="map" frameborder="0" style="width: 100%; height:300px; position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);" src="https://www.google.com/maps/embed/v1/place?q=place_id:Ej1BdiBJbmdsYXRlcnJhIDI3OTAsIFZhbGxhcnRhLCA0NDY5MCBHdWFkYWxhamFyYSwgSmFsLiwgTWV4aWNvIlESTwo0CjIJ2YKN63OuKIQRrpy6UBrqNV4aHgsQ7sHuoQEaFAoSCcEtLB5uriiEETlztajtpqb3DBDmFSoUChIJv2jOY3SuKIQRtoLbAYVzhF4&key=AIzaSyArtT8rdV6F-bEZwPjCQZbgDfPpX0bjK1Y"></iframe>
				<!--div id="map-canvas" style="width: 100%; height:300px; position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div-->
			</div>
		</div>
	</div>
	<p>&nbsp;</p>
	
@stop

@section('js')
	{{HTML::script('https://maps.googleapis.com/maps/api/js?key=AIzaSyArtT8rdV6F-bEZwPjCQZbgDfPpX0bjK1Y')}}
	<script type="text/javascript">
		$(document).ready(function(){
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