
@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-3">
                <h4 class="md-title mb5">Buscar</h4>
                <div class="input-group">
                    <input type="search" class="form-control" id="buscar" name="buscar">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                </div><!-- input-group -->
            </div>
            <div class="col-md-3">
                <h4 class="md-title mb5">Categorías</h4>
                <select id="id_blog_categoria" name="id_blog_categoria" data-placeholder="Selecciona la categoria" style="width: 100%;">
                    {{$categoriasOption}}
                </select>
            </div>
            <div class="col-md-3">
                <h4 class="md-title mb5">Tipo de página</h4>
                <select id="tipo" name="tipo" data-placeholder="Selecciona la categoria" style="width: 100%;">
                    <option value="-1">Todos</option>
                    <option value="1">Blog</option>
                    <option value="2">Página</option>
                </select>
            </div>
            <div class="col-md-3">
                <h4 class="md-title mb5">Estatus</h4>
                <select id="estatus" name="estatus" data-placeholder="Selecciona la categoria" style="width: 100%;">
                    <option value="-1">Todos</option>
                    <option value="1">Activos</option>
                    <option value="2">Inactivos</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb20"></div>
            <button class="btn btn-primary btn-block actualizarTabla">Filtrar resultados <i class="fa fa-magic"></i></button>
        </div>
    </div>
    <div class="mb20"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Publicaciones</h4>
                    <p>Listado con todas las publicaciones del blog</p>
                    <div class="pull-right">
                        <a class="tooltips actualizarTabla" data-toggle="tooltip" href="#" data-original-title="Actualizar tabla"><i class="fa fa-refresh"></i></a>
                    </div>
                </div><!-- panel-heading -->
                <div class="panel-body" style="padding: 0px;">
                    <table id="basicTable" class="table table-striped table-bordered table-hover responsive">
                        <thead>
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Título</th>
                                <th class="alignCenter">Alias</th>
                                <th class="alignCenter">Categoria</th>
                                <th class="alignCenter">Fecha publicación</th>
                                <th class="alignCenter">Tipo</th>
                                <th class="alignCenter">Estatus</th>
                                <th class="alignCenter"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th class="alignCenter">#</th>
                                <th class="alignCenter">Título</th>
                                <th class="alignCenter">Alias</th>
                                <th class="alignCenter">Categoria</th>
                                <th class="alignCenter">Fecha publicación</th>
                                <th class="alignCenter">Tipo</th>
                                <th class="alignCenter">Estatus</th>
                                <th class="alignCenter"></th>              
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- panel-body -->
            </div><!-- panel -->
        </div>
    </div>
    
@stop