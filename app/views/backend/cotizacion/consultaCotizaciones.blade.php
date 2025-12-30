
@section('contenido')
    <div class="row">
        <button class="btn btn-primary btn-block siguienteCotizacion" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-magic"></i> Siguiente cotización</button>
        <br>
        <div>
            <ul class="nav nav-tabs nav-info nav-justified">
                <li class="active"><a href="#contratar" data-toggle="tab"><strong>Desea contratar</strong></a></li>
                <li class=""><a href="#espera" data-toggle="tab"><strong>En espera</strong></a></li>
                <li class=""><a href="#proceso" data-toggle="tab"><strong>En proceso</strong></a></li>
                <li class=""><a href="#programadas" data-toggle="tab"><strong>Programadas</strong></a></li>
                <li class=""><a href="#otros" data-toggle="tab"><strong>otros</strong></a></li>
            </ul>
            <div class="tab-content mb30">
                @foreach($estatusTable AS $key=>$estatuTable)
                    <div class="tab-pane {{(($key == 1) ? 'active' : '')}}" id="{{$estatuTable}}">
                        <table id="listadoCotizaciones{{$key}}" class="table table-striped table-bordered table-hover responsive">
                            <thead class="">
                                <tr>
                                    <th class="alignCenter">#</th>
                                    <th class="alignCenter">Nombre</th>
                                    <th class="alignCenter">Ingreso</th>
                                    <th class="alignCenter">Contacto</th>
                                    <th class="alignCenter">Ubicación</th>
                                    <th class="alignCenter">Integrantes</th>
                                    <th class="alignCenter">Agente</th>
                                    <th class="alignCenter">Estatus</th>
                                    <th class="alignCenter"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot class="">
                                <tr>
                                    <th class="alignCenter">#</th>
                                    <th class="alignCenter">Nombre</th>
                                    <th class="alignCenter">Ingreso</th>
                                    <th class="alignCenter">Contacto</th>
                                    <th class="alignCenter">Ubicación</th>
                                    <th class="alignCenter">Integrantes</th>
                                    <th class="alignCenter">Agente</th>
                                    <th class="alignCenter">Estatus</th>
                                    <th class="alignCenter"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endforeach
                
            </div>
        </div>
    </div>
    
@stop