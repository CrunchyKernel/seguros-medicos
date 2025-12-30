@section('contenido')
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <img class="img-responsive pull-left" src="https://segurodegastosmedicosmayores.mx/protectodiez/logos/gastosmedicosmayores180.jpg">
            </div>
            <div class="col-sm-6">
                <img class="img-responsive pull-right" src="https://segurodegastosmedicosmayores.mx/protectodiez/logos/PROTECTODIEZ-LOGO-500-253-.jpg" >
            </div>
        </div>
    </div>
    <div class="container">
        <h4>Hola {{$nombre}}:</h4>
        <p class="lead"><small>{{sistemaFunciones::fechaLetras(date('Y-m-d'))}}</small></p>
        {{$encabezado}}
    </div>
    <div class="container">
        <div style="border: 1px solid rgb(204, 204, 204); padding: 5px 10px; text-align: center; background: rgb(238, 238, 238);">
            <a class="btn soc-btn fb" href="{{URL::to('verCotizacion/'.$id_cotizacion.'/'.$secret)}}" target="_blank">
                Cotizaci&oacute;n en l&iacute;nea
            </a>
        </div>
        <br>
        {{$cuerpo}}
    </div>
    <div class="container">
        {{$pie}}
        <p>
            © 2005 - {{date('Y')}} protectodiez.com. Todos los derechos reservados. |
            <a href="#"  target="_blank">Aviso de privacidad</a>
            {{HTML::style('backend/css/bootstrap.min.css')}}
            {{HTML::script('backend/js/bootstrap.min.js')}}
        </p>
    </div>
@stop
