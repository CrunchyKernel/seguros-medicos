@extends('layout.porto')

@section('contenido')
	<div class="page_title">
		<div class="container">
			<h1 class="custom-primary-font font-weight-semibold text-transform-none text-9 text-center mb-5 appear-animation" data-appear-animation="bounceInLeft">Nosotros</h1>
		</div>
	</div>
	<div class="container">
		<div class="row pb-5">
			<div class="col-md-9 appear-animation" data-appear-animation="fadeIn" data-appear-animation-delay="400">
		    	<p>Somos un grupo de agentes de seguros llamados ProtectoDIEZ, con más de 40 años en el mercado.</p>
				<p>Nos hemos especializado en el ramo de Gastos Médicos Mayores, con lo que hemos participado en los comités de mejora de producto de una aseguradora como consejeros del producto.</p>
				<p>Día a día atendemos a clientes que requieren de nuestros servicios por la presencia de una enfermedad o de un accidente. Les ayudamos a que todo el proceso de su hospitalización y rembolsos se realicen de forma adecuada y sin complicaciones.</p>
				<p>Estamos comprometidos con el cliente, por cuidar su economía y por recomendar las mejores alternativas en seguros de gastos médicos a la hora de su contratación y renovación de póliza.</p>
				<p>Nuestra fuerte presencia en internet, así como en la plaza nos ha hecho una empresa fuerte en el ramo de Gastos Médicos.</p>
				<p>Nos dará mucho gusto poder atenderte y orientarte en el proceso de la prevención ante la aparición de una posible enfermedad o accidente en ti o en tu familia. No dudes en consultarnos.</p>
		    </div>
		    <div class="col-md-3">
		    	<img src="{{asset('assets/images/fotos/nosotros.jpg')}}" class="img-fluid mw-100 appear-animation" data-appear-animation="fadeInRight" data-appear-animation-delay="600">
		    </div>
		</div>
	    <div class="row pb-5">
			<div class="col-md-3">
		    	<img src="{{asset('assets/images/fotos/mision.jpg')}}" class="img-fluid mw-100 appear-animation" data-appear-animation="fadeInLeft">
		    </div>
		    <div class="col-md-9 appear-animation" data-appear-animation="fadeIn" data-appear-animation-delay="400">
		    	<h3 class="text-primary">Misión</h3>
		        <p>Comercializar los mejores productos de Gastos Médicos del mercado, fomentando un cambio en la Cultura del Aseguramiento, a través de la Asesoría Profesional a nuestros Clientes tanto en el momento de la contratación de su póliza como en el del siniestro en que requieran utilizarla. Así recibirán los Beneficios que realmente esperan y aquellos por los que contrataron la póliza.</p>
		    </div>
		</div>
		<div class="row pb-5">
			<div class="col-md-9 appear-animation" data-appear-animation="fadeIn">
		    	<h3 class="text-primary">Visión</h3>
		        <p>Somos una Empresa Moderna, Eficaz, de Calidad, Lider en el Mercado de Seguros, comprometidos con nuestros clientes, con nuestro personal, con la Sociedad y con las Aseguradoras..</p>
		    </div>
		    <div class="col-md-3">
		    	<img src="{{asset('assets/images/fotos/vision.jpg')}}" class="img-fluid mw-100 appear-animation" data-appear-animation="fadeInRight" data-appear-animation-delay="400">
		    </div>
		</div>
	    <div class="row pb-5">
			<div class="col-md-3">
		    	<img src="{{asset('assets/images/fotos/valores.jpg')}}" class="img-fluid mw-100 appear-animation" data-appear-animation="fadeInLeft">
		    </div>
		    <div class="col-md-9 appear-animation" data-appear-animation="fadeIn" data-appear-animation-delay="400">
		    	<h3 class="text-primary">Valores</h3>
		        <ul class="list-unstyled">
					<li><i class="fa fa-arrow-circle-right"></i> Cuidamos a nuestros Clientes sirviéndoles con EXCELENCIA</li>
					<li><i class="fa fa-arrow-circle-right"></i> Vendemos solo Seguros de Calidad y de los que podamos estar orgullosos</li>
					<li><i class="fa fa-arrow-circle-right"></i> Trabajamos en Equipo con un Espíritu de Colaboración, Armonía y Compañerismo.</li>
					<li><i class="fa fa-arrow-circle-right"></i> Respetamos a los demás y logramos el éxito juntos.</li>
					<li><i class="fa fa-arrow-circle-right"></i> Hacemos Historia trabajando duro para forjar un mejor futuro de nuestros clientes y propio.</li>
				</ul>
		    </div>
		</div>
	</div>
@stop