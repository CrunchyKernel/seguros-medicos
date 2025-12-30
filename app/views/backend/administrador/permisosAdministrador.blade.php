
@section('contenido')
	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">Permisos para administrador: <strong>{{$administradorDatos->nombre}} {{$administradorDatos->apellido_paterno}} {{$administradorDatos->apellido_materno}}</strong></h4>
            <p>Seleccione el tipo de permiso para cada módulo.</p>
        </div><!-- panel-heading -->
        <div class="panel-body" style="padding: 0px !important;">
            {{$modulosHtml}}
        </div><!-- panel-body -->
    </div><!-- panel -->

@stop