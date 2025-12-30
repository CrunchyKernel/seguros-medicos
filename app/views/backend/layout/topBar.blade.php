<header>
	<div class="headerwrapper">
		<div class="header-left">
			<a href="{{URL::to('/admingm')}}" class="logo">
				<img src="{{asset('/protectodiez/logos/gastosmedicosmayores300.jpg')}}" class="img-responsive">
			<div class="pull-right">
				<a href="#" class="menu-collapse"><i class="fa fa-bars"></i></a>
			</div>
		</div><!-- header-left -->
		<div class="header-right">
			<div class="pull-right">
				<div class="btn-group btn-group-list btn-group-notification">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="background-color: #eea236 !important;">Nuevas <span class="badge">{{\Cotizacion::select('id_cotizacion')->whereIn('estatus', array(1,2))->count()}}</span></button>
					<div class="dropdown-menu pull-right">
						<h5>Cotizaciones nuevas</h5>
						<ul class="media-list dropdown-list">
							@foreach(\Cotizacion::select('id_cotizacion','nombre','secret','fecha_registro')->whereIn('estatus', array(1,2))->limit(5)->orderBy('fecha_registro','desc')->get() As $cotizacionNueva)
								<li class="media">
									<a href="{{\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacionNueva->id_cotizacion.'/'.$cotizacionNueva->secret)}}" style="padding: 0px;">
										<div class="media-body">
											<strong>{{$cotizacionNueva->id_cotizacion}} - {{ucwords($cotizacionNueva->nombre)}}</strong>
											<small class="date"><i class="fa fa-calendar"></i> {{$cotizacionNueva->fecha_registro}}</small>
										</div>
									</a>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
				<div class="btn-group btn-group-list btn-group-notification">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="background-color: #5cb85c !important;">Proceso <span class="badge">{{\Cotizacion::select('id_cotizacion')->where('id_agente',\Auth::user()->id_usuario)->where('estatus', 3)->count()}}</span></button>
					<div class="dropdown-menu pull-right">
						<h5>Cotizaciones en proceso</h5>
						<ul class="media-list dropdown-list">
							@foreach(\Cotizacion::select('id_cotizacion','nombre','secret','fecha_registro')->where('id_agente',\Auth::user()->id_usuario)->where('estatus', 3)->get() As $cotizacionProceso)
								<li class="media">
									<a href="{{\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacionProceso->id_cotizacion.'/'.$cotizacionProceso->secret)}}" style="padding: 0px;">
										<div class="media-body">
											<strong>{{$cotizacionProceso->id_cotizacion}} - {{ucwords($cotizacionProceso->nombre)}}</strong>
											<small class="date"><i class="fa fa-calendar"></i> {{$cotizacionProceso->fecha_registro}}</small>
										</div>
									</a>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
				<div class="btn-group btn-group-list btn-group-notification">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="background-color: #5bc0de !important;">Prog Ant. <span class="badge">{{$pendientesAnteriores->count()}} / {{$cotizacionSeguimientoProgramadoPrioridad->count()}}</span></button>
					<div class="dropdown-menu pull-right">
						<h5>Cotizaciones en proceso</h5>
						<ul class="media-list dropdown-list">
							@foreach($cotizacionSeguimientoProgramadoPrioridad As $cotizacionAnt)
								<li class="media">
									<a href="{{\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacionAnt->id_cotizacion.'/'.$cotizacionAnt->secret)}}" style="padding: 0px;">
										<div class="media-body">
											<strong>{{$cotizacionAnt->id_cotizacion}} - {{ucwords($cotizacionAnt->nombre)}}</strong>
											<small class="date"><i class="fa fa-calendar"></i> {{$cotizacionAnt->fecha_registro}}</small>
										</div>
									</a>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
				<div class="btn-group btn-group-list btn-group-notification">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="background-color: #5cb85c !important;">Prog Hoy. <span class="badge">{{$pendientesHoy}} / {{$cotizacionSeguimientoHoyProgramadoPrioridad->count()}}</span></button>
					<div class="dropdown-menu pull-right">
						<h5>Cotizaciones en proceso</h5>
						<ul class="media-list dropdown-list">
							@foreach($cotizacionSeguimientoHoyProgramadoPrioridad As $cotizacionHoy)
								<li class="media">
									<a href="{{\URL::to('/admingm/cotizacion/verCotizacion/'.$cotizacionHoy->id_cotizacion.'/'.$cotizacionHoy->secret)}}" style="padding: 0px;">
										<div class="media-body">
											<strong>{{$cotizacionHoy->id_cotizacion}} - {{ucwords($cotizacionHoy->nombre)}}</strong>
											<small class="date"><i class="fa fa-calendar"></i> {{$cotizacionHoy->fecha_registro}}</small>
										</div>
									</a>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
				<div class="btn-group btn-group-option">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-caret-down"></i> <span style="color: #fff;">Opciones</span></button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li><a href="{{URL::to('/admingm/administrador/miPerfil')}}"><i class="glyphicon glyphicon-user"></i> Mi perfil</a></li>
						<!--<li><a href="#"><i class="glyphicon glyphicon-star"></i> Activity Log</a></li>-->
						<!--<li><a href="#"><i class="glyphicon glyphicon-cog"></i> Account Settings</a></li>-->
						<!--<li><a href="#"><i class="glyphicon glyphicon-question-sign"></i> Help</a></li>-->
						<li class="divider"></li>
						<li><a href="{{\URL::to('/admingm/login/cerrarSesion')}}"><i class="glyphicon glyphicon-log-out"></i>Cerrar sesión</a></li>
					</ul>
				</div><!-- btn-group -->
			</div><!-- pull-right -->
		</div><!-- header-right -->
	</div><!-- headerwrapper -->
</header>

<section>
    <div class="mainwrapper">
