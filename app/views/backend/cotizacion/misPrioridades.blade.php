
@section('contenido')
    <div class="row">
        <button class="btn btn-primary btn-block siguienteCotizacion" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'><i class="fa fa-magic"></i> Siguiente cotización</button>
        <br>
        <div>
            <div class="tab-pane active" id="cotizaciones">
                <table id="listadoCotizaciones" class="table table-striped table-bordered table-hover responsive">
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
        </div>
    </div>
    
@stop
