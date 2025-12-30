
@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <!--
            <div class="col-md-3">
                <h4 class="md-title mb5">Buscar</h4>
                <div class="input-group">
                    <input type="search" class="form-control" id="buscar" name="buscar">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                </div>
            </div>
            -->
            <div class="col-md-3">
                <h4 class="md-title mb5">Agentes</h4>
                <select id="id_agente" name="id_agente" data-placeholder="Selecciona la categoria" style="width: 100%;">
                    <option value="-2">Todos</option>
                    @if(isset($agentes) && $agentes->count() > 0)
                        @foreach($agentes AS $agente)
                            <option value="{{$agente->id_usuario}}">{{$agente->nombre}} {{$agente->apellido_paterno}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <h4 class="md-title mb5">Estatus</h4>
                <select id="estatus" name="estatus" data-placeholder="Selecciona la categoria" style="width: 100%;">
                    <option value="-1">Todos</option>
                    @foreach($cotizacionEstatus AS $estatus)
                        <option value="{{$estatus->id_estatus}}">{{$estatus->estatus}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <h4 class="md-title mb5">Valor</h4>
               	<input type="text" id="valor" name="valor" data-placeholder="Valor" style="width: 100%;"> 
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb20"></div>
            <button class="btn btn-primary btn-block actualizarTabla">Filtrar resultados <i class="fa fa-magic"></i></button>
        </div>
    </div>
    <!--div class="mb20"></div>
    <div class="row">
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
    </div-->
    <div class="mb20"></div>
    <div class="row">
    	<div>
            <div class="tab-pane active">
                <table id="listadoCotizaciones2" class="table table-striped table-bordered table-hover responsive">
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
