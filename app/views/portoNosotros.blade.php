@extends('layout.porto')

@section('contenido')
	<style>
		/* Extra breathing room between top-level sections */
		.nos-section {
			margin-bottom: 5rem;
		}

		.nos-img {
			border-radius: 15px !important;
			width: 100%;
			/* Las fotos miden 371px de ancho: evitamos escalarlas de más */
			max-width: 420px;
		}
	</style>

	<div class="container">
		<!-- HERO -->
		<div class="card shadow-sm nos-section mt-5"
			style="border-radius: 25px; border: none; background: linear-gradient(180deg, rgba(255,255,255,1) 0%, #B4E0E8 100%);">
			<div class="px-4 px-md-5 py-5 text-center">
				<h1 class="mt-0">Nosotros</h1>

				<p class="lead mb-0 mx-auto" style="max-width: 760px; text-align: center;">Somos un grupo de agentes de seguros llamados ProtectoDIEZ, con más de 40 años en el mercado.</p>
			</div>
		</div>
		<!-- QUIENES SOMOS -->

		<div class="nos-section">
			<div class="row align-items-center">
				<div class="col-md-7">
					<p>Nos hemos especializado en el ramo de Gastos Médicos Mayores, con lo que hemos participado en los comités de mejora de producto de una aseguradora como consejeros del producto.</p>

					<p>Día a día atendemos a clientes que requieren de nuestros servicios por la presencia de una enfermedad o de un accidente. Les ayudamos a que todo el proceso de su hospitalización y rembolsos se realicen de forma adecuada y sin complicaciones.</p>

					<p>Estamos comprometidos con el cliente, por cuidar su economía y por recomendar las mejores alternativas en seguros de gastos médicos a la hora de su contratación y renovación de póliza.</p>

					<p>Nuestra fuerte presencia en internet, así como en la plaza nos ha hecho una empresa fuerte en el ramo de Gastos Médicos.</p>

					<p class="mb-0">Nos dará mucho gusto poder atenderte y orientarte en el proceso de la prevención ante la aparición de una posible enfermedad o accidente en ti o en tu familia. No dudes en consultarnos.</p>
				</div>

				<div class="col-md-5 text-center mt-4 mt-md-0">
					<img src="{{asset('assets/images/fotos/nosotros.jpg')}}" class="img-fluid rounded nos-img" alt="Equipo de asesores en seguros de gastos médicos mayores">
				</div>
			</div>
		</div>
		<!-- MISION -->

		<div class="card shadow-sm p-4 p-md-5 nos-section"
			style="border-radius: 25px; border: none; background: linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(210,240,245,1) 100%);">
			<div class="row align-items-center">
				<div class="col-md-5 text-center mb-4 mb-md-0">
					<img src="{{asset('assets/images/fotos/mision.jpg')}}" class="img-fluid rounded nos-img" alt="Misión">
				</div>

				<div class="col-md-7">
					<h3 class="mt-0">Misión</h3>

					<p class="mb-0">Comercializar los mejores productos de Gastos Médicos del mercado, fomentando un cambio en la Cultura del Aseguramiento, a través de la Asesoría Profesional a nuestros Clientes tanto en el momento de la contratación de su póliza como en el del siniestro en que requieran utilizarla. Así recibirán los Beneficios que realmente esperan y aquellos por los que contrataron la póliza.</p>
				</div>
			</div>
		</div>
		<!-- VISION -->

		<div class="card shadow-sm p-4 p-md-5 nos-section"
			style="border-radius: 25px; background-color: #f5f5f5; border: none;">
			<div class="row align-items-center">
				<div class="col-md-7">
					<h3 class="mt-0">Visión</h3>

					<p class="mb-0">Somos una Empresa Moderna, Eficaz, de Calidad, Lider en el Mercado de Seguros, comprometidos con nuestros clientes, con nuestro personal, con la Sociedad y con las Aseguradoras..</p>
				</div>

				<div class="col-md-5 text-center mt-4 mt-md-0">
					<img src="{{asset('assets/images/fotos/vision.jpg')}}" class="img-fluid rounded nos-img" alt="Visión">
				</div>
			</div>
		</div>
		<!-- VALORES -->

		<div class="nos-section">
			<div class="row align-items-center">
				<div class="col-md-5 text-center mb-4 mb-md-0">
					<img src="{{asset('assets/images/fotos/valores.jpg')}}" class="img-fluid rounded nos-img" alt="Valores">
				</div>

				<div class="col-md-7">
					<h3 class="mt-0">Valores</h3>

					<div class="card shadow-sm p-4" style="border-radius: 15px; background-color: #f5f5f5; border: none;">
						<ul class="list-group list-group-flush">
							<li class="list-group-item d-flex align-items-start" style="background-color: transparent;">Cuidamos a nuestros Clientes sirviéndoles con EXCELENCIA</li>
							<li class="list-group-item d-flex align-items-start" style="background-color: transparent;">Vendemos solo Seguros de Calidad y de los que podamos estar orgullosos</li>
							<li class="list-group-item d-flex align-items-start" style="background-color: transparent;">Trabajamos en Equipo con un Espíritu de Colaboración, Armonía y Compañerismo.</li>
							<li class="list-group-item d-flex align-items-start" style="background-color: transparent;">Respetamos a los demás y logramos el éxito juntos.</li>
							<li class="list-group-item d-flex align-items-start" style="background-color: transparent; border-bottom: none;">Hacemos Historia trabajando duro para forjar un mejor futuro de nuestros clientes y propio.</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop
