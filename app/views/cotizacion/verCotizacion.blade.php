@section('contenido')
	<div class="page_title">
		<div class="container">
			<div class="title"><h1>Cotización</h1></div>
	        <!--<div class="pagenation">&nbsp;<a href="index.html">Home</a> <i>/</i> <a href="#">Features</a> <i>/</i> Pricing Tables</div>-->
		</div>
	</div>
	<div class="container">
		<div class="content_fullwidth" style="margin-bottom: 20px;">
			{{$tablaClienteDatos}}
			{{$tablaIntegrantes}}
			
			@if(count($tablaDatos) > 0)
				<div id="tabs">
					<ul class="tabs">
						@foreach($tablaDatos AS $key=>$tablaDato)
							<li class=""><a href="#{{$key}}">{{$tablaDato['titulo']}}</a></li>
						@endforeach
					</ul>
					<div class="tab-container">
						@foreach($tablaDatos AS $key=>$tablaDato)
							<div id="{{$key}}" class="tab-content ">
								<h3>{{$tablaDato['nombre']}}</h3>
								{{$tablaDato['tabla']}}
								@if($key == 'sa_da')
									<br>
									<p><strong>*Se recomienda para edades mayores</strong></p>
									<br>
								@endif
								<a class="but_star enviarCotizacinEmail" style="cursor: pointer;" data-idcotizacion="{{$cotizacionDatos->id_cotizacion}}" data-sa="{{$tablaDato['s']}}" data-ded="{{$tablaDato['d']}}" data-secret="{{$cotizacionDatos->secret}}" data-loading-text='Procesando...'><i class="fa fa-envelope-o fa-lg"></i> Envíar cotización por e-mail [ {{strtoupper($tablaDato['s'].'-'.$tablaDato['d'])}} ]</a>
								<a href="{{URL::to('verCotizacionPDF/'.$cotizacionDatos->id_cotizacion.'/'.$cotizacionDatos->secret.'/'.$tablaDato['s'].'/'.$tablaDato['d'])}}" target="_blank" class="but_wifi"><i class="fa fa-file-pdf-o fa-lg"></i> Ver cotización PDF [ {{strtoupper($tablaDato['s'].'-'.$tablaDato['d'])}} ]</a>
							</div>
						@endforeach
					</div>
				</div>
			@endif
		</div>
		<div class="content_fullwidth">
			<div id="div3" class="notice">
                <div class="message-box-wrap">
                	<p>Los costos aqu&iacute; mostrados son c&aacute;lculos que pueden tener variaciones de acuerdo a la aseguradora, cambios repentinos, errores, o la salud del asegurado. No constituyen un compromiso para la aseguradora. Consulte con su Asesor de Seguros. Aplican restricciones.</p>
                </div>
			</div>
		</div>
		<div class="clearfix divider_line2" style="margin: 0px 0px 0px 0px; height: 5px;"></div>
		<div class="content_fullwidth">
			<div class="framed-box">
				<div class="framed-box-wrap">
					<div class="pricing-title">
						<h3><center><b>ProtectoDIEZ</b></center></h3>
					</div>
					<div class="pricing-text-list" style="text-align: left;">
                        <img src="{{asset('assets/images/fotos/nosotros.jpg')}}" class="pull-right" style="padding: 15px;">
                    	<p>Al comprar tu póliza con nosotros, ProtectoDIEZ tendrás adicionalmente y sin costo beneficios importantes y exclusivos en tu póliza, no importando en qué empresa decidas adquirir tus gastos médicos. Estos beneficios son:</p>
                    	<ul class="list1" style="text-align: justify;">
	                        <li><i class="fa fa-arrow-right"></i> <strong>Gastos Médicos Menores:</strong> Pensando en el día a día, en la prevención y en gastos médicos menores, hemos creado un convenio para nuestros clientes con varias unidades médicas de Primer Nivel, con el que tenemos costo preferencial: para Médico General, Ginecólogo, Pediatra y Traumatólogo de $200 ó $250 pesos</li>
	                        <li><i class="fa fa-arrow-right"></i> <strong>Especialidades Médicas:</strong> Se tienen consultas de $250 a $350 en una Unidad Médica conveniada con especialidades tales como: Traumatología, Ortopedia, Dermatología, Gastroenterología, Oftalmología, Otorrinolaringólogo, Nutrición, Terapia física, Cardiología, Neurología, Homeopatía, Dentista, Oncología, por mencionar algunas.  </li>
	                        <li><i class="fa fa-arrow-right"></i> <strong>Exámenes de Laboratorio y Rayos X:</strong> En la unidad médica hay paquetes de Chequeo general y estudios de laboratorio con 50% de descuento. Electros, Mamografía, Tomografías, Resonancias con 40% de descuento. Además tenemos autorizado un 30% de descuento en estudios de laboratorio en Unidad de Patología (y descuentos en otros estudios). </li>
	                        <li><i class="fa fa-arrow-right"></i> <strong>Asesoría:</strong> como Agentes de Seguros Profesionales especialistas en el ramo, te asesoramos durante todo el transcurso de tu póliza, no solo en renovaciones automáticas y cobros, sino en enfermedades que se presenten y qué hacer en cada caso, trámites de reembolso y demás que necesites con una u otra compañía de seguros. Conocemos los procedimientos, conocemos a la gente de las compañías y te ayudaremos en el momento. </li>
	                        <li><i class="fa fa-arrow-right"></i> <strong>Servicio:</strong> Nuestro servicio va más allá de la venta. Se refiere a un trato personalizado, una emisión correcta, entrega, cobranza, renovación automática, y sobre todo asistencia y apoyo a la hora del siniestro con la compañía de seguros, sea cual fuere. La confianza y el apoyo que le queremos brindar es lo que hará la diferencia de ProtectoDIEZ, Asesoría Patrimonial en Seguros.</li>
	                        <li><i class="fa fa-arrow-right"></i> <strong>Antigüedad:</strong> Si tienes vigente o recién terminada una póliza, tu antigüedad se respeta, con lo que eliminas periodos de espera en ambas compañías, y todas tus enfermedades cubiertas lo están desde el principio. </li>
	                        <li><i class="fa fa-arrow-right"></i> Los planes pueden tener variaciones a tu gusto en sumas aseguradas y algunas condiciones. Tomando un plan base vemos tu inquietud y cotizaremos alternativas. </li>
	                    </ul>
	                    <p>Puedes contactarnos en los teléfonos al calce, y con gusto te asesoraremos para que escojas tu opción óptima en precio y coberturas, justo a tus necesidades. Quedo a tus órdenes.</p>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix divider_line2"></div>
	</div>
	{{HTML::script('backend/js/helpers/cotizacion/verCotizacion2.js')}}

	<!-- Google Code for Gastos Medicos 2017 Conversion Page -->
	<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 1025142637;
		var google_conversion_language = "en";
		var google_conversion_format = "3";
		var google_conversion_color = "ffffff";
		var google_conversion_label = "Um2KCOLz1G4Q7d7p6AM";
		var google_remarketing_only = false;
		/* ]]> */
	</script>
	<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
	<noscript>
		<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1025142637/?label=Um2KCOLz1G4Q7d7p6AM&amp;guid=ON&amp;script=0"/>
		</div>
	</noscript>
@stop
