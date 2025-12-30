
@section('contenido')
	<div class="page_title">
		<div class="container">
			<div class="title"><h1>Contacto</h1></div>
			<!--<div class="pagenation">&nbsp;<a href="index.html">Home</a> <i>/</i> <a href="#">Pages</a> <i>/</i> Full Width</div>-->
		</div>
	</div>
	<div class="container">
		<div class="content_fullwidth">
			<div class="one_half">
				<p>Nos hemos especializado en el ramo de Gastos Médicos Mayores, con lo que hemos participado en los comités de mejora de producto de una aseguradora como consejeros del producto.</p>
				<br>
				<h3>Formulario <strong>contacto</strong></h3>
				<div id="error"></div>
				<form id="contactoForm" name="contactoForm" method="post">
					<fieldset>
						<label for="nombre" class="blocklabel">Nombre completo</label>
						<p class=""><input placeholder="Mi nombre" id="nombre" name="nombre" type="text" class="input_bg validate"></p>
						<label for="e_mail" class="blocklabel">Correo electrónico</label>
						<p class=""><input placeholder="micorreo@dominio.com" id="e_mail" name="e_mail" type="email" class="input_bg validate"></p>
						<label for="mensaje" class="blocklabel">Mensaje</label>
						<p class=""><textarea placeholder="Contenido del mensaje" id="mensaje" name="mensaje" class="textarea_bg" cols="20" rows="7" class="validate"></textarea></p>
						<div class="clearfix"></div>
						<input type="button" value="ENVIAR" class="comment_submit enviarContacto"><p></p>
					</fieldset>
				</form> 
			</div>
			<div class="one_half last">
				<div class="address-info">
					<h3><strong>Dirección</strong></h3>
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
				<h3>Nuestra <strong>Ubicación</strong></h3>
				<div id="map-canvas" style="width: 100%; height:300px; position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
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
						var companyMarker = new google.maps.Marker({
																position: latlng,
																map: map,
																title:"Seguro de Gastos Médicos Mayores",
																zIndex: 3
															});
					});
				</script>
			</div>
		</div>
	</div>
	<div class="clearfix divider_line2"></div>
	
@stop
