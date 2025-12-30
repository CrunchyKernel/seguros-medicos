
@section('contenido')

<div class="row row-stat">
    <div class="col-md-4">
        <div class="panel panel-warning-alt noborder">
            <div class="panel-heading noborder">
                <div class="panel-icon"><i class="fa fa-plus"></i></div>
                <div class="media-body">
                    <h5 class="md-title nomargin">Nuevas</h5>
                    <h1 class="mt5">{{$cotizacionesNuevas}}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-success-alt noborder">
            <div class="panel-heading noborder">
                <div class="panel-icon"><i class="fa fa-pencil"></i></div>
                <div class="media-body">
                    <h5 class="md-title nomargin">En proceso</h5>
                    <h1 class="mt5">{{$cotizacionesProceso}}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-danger-alt noborder">
            <div class="panel-heading noborder">
                <div class="panel-icon"><i class="fa fa-phone"></i></div>
                <div class="media-body">
                    <h5 class="md-title nomargin">Por reintentar</h5>
                    <h1 class="mt5">{{$cotizacionesIntentos}}</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row row-stat">
    <div class="col-md-6">
        <div class="panel panel-info-alt noborder">
            <div class="panel-heading noborder">
                <div class="panel-icon"><i class="fa fa-calendar"></i></div>
                <div class="media-body">
                    <h5 class="md-title nomargin">Programadas anteriores / Prioridad</h5>
                    <h1 class="mt5">{{$pendientesAnteriores->count()}} / {{$cotizacionSeguimientoProgramadoPrioridad->count()}}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-success-alt noborder">
            <div class="panel-heading noborder">
                <div class="panel-icon"><i class="fa fa-pencil"></i></div>
                <div class="media-body">
                    <h5 class="md-title nomargin">Programadas para hoy / Priodidad</h5>
                    <h1 class="mt5">{{$pendientesHoy}} / {{$cotizacionSeguimientoHoyProgramadoPrioridad->count()}}</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<button class="btn btn-primary btn-block siguienteCotizacion" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-magic"></i> Siguiente cotización</button>
	
@stop