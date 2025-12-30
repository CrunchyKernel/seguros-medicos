@section('contenido')
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                	<input type="hidden" id="idCotizacion" value="{{$cotizacionDatos->id_cotizacion}}">
                    <h5 class="lg-title mb10">{{$cotizacionDatos->nombre}}</h5>
                    <address>
                        <strong>{{$cotizacionDatos->e_mail}}</strong><br>
                        <strong>Teléfono:</strong> {{$cotizacionDatos->telefono}}<br>
                        {{$cotizacionDatos->estado}}, {{$cotizacionDatos->ciudad}}<br>
                        <strong>Cuenta con póliza:</strong> {{$cotizacionDatos->poliza_actual}}<br>
                        <strong>Integrantes:</strong> {{count($cotizacionDatos->integrantes)}}<br>
                        <strong>Whatsapp:</strong> <a href="https://wa.me/521{{$cotizacionDatos->telefono}}?text=Hola {{$cotizacionDatos->nombre}}" target="_blank"><i class="fa fa-whatsapp"></i></a><br>
                      	@if(!is_null($cotizacionDatos->mapfre_numero))
                      		<strong>Mapfre:</strong> <a href="https://zonaliados.mapfre.com.mx/Zonaliados.Multiplataforma/AYESalud?Cotizacion={{$cotizacionDatos->mapfre_numero}}" target="_blank">{{$cotizacionDatos->mapfre_numero}}</a>
                      		<br>
	                    @endif
                    </address>
                </div>
                <div class="col-sm-6 text-right">
                    <h4 class="text-primary">Cotización: {{$cotizacionDatos->id_cotizacion}}</h4>
                    <h5 class="text-primary"><strong>Estatus:</strong> {{$cotizacionDatos->estatus()->first()->estatus}}</h5>
                    @if($cotizacionDatos->estatus==1)
                    	<h5 class="text-primary"><strong>Paquete:</strong> {{$cotizacionDatos->me_interesa}}</h5>
                    @endif
                    <h5 class="text-primary"><strong>Dominio:</strong> {{$cotizacionDatos->dominio()->first()->nombre}}</h5>
                    <h5 class="text-primary"><strong>Ruta:</strong> {{$cotizacionDatos->ruta}}</h5>
                    <p><strong>Fecha registro:</strong> {{SistemaFunciones::fechaLetras(date('Y-m-d', strtotime($cotizacionDatos->fecha_registro)))}} - {{date('H:i', strtotime($cotizacionDatos->fecha_registro))}} hrs.</p>
                </div>
            </div>
            <div class="row">
            	<div class="col-sm-12 text-center">
            		<button class="btn btn-success" id="btnSend">Enviar siguiente mensaje</button>
            	</div>
            </div>
        </div>
    </div>
@stop
