
@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-3">
                <h4 class="md-title mb5">Categorías</h4>
                <select id="id_blog_categoria" name="id_blog_categoria" data-placeholder="Selecciona la categoria" style="width: 100%;">
                    <option value="-1">Todos</option>
                    {{$categoriasOption}}
                </select>
            </div>
        </div>
    </div>
    <div class="mb20"></div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">Publicaciones de blog</h4>
            <p>Ordene las publicaciones arrastrandolas.</p>
        </div><!-- panel-heading -->
        <div class="panel-body">
            <div class="dd" id="publicacionesBlog">
                <ol class="dd-list">
                    @if(isset($publicaciones) && count($publicaciones) > 0)
                        @foreach($publicaciones AS $publicacion)
                            <li class="dd-item dd3-item" data-id="{{$publicacion->id_blog}}">
                                <div class="dd-handle dd3-handle">Drag</div>
                                <div class="dd3-content">
                                    {{$publicacion->titulo}} {{((isset($publicacion->categoria()->get()[0]->categoria)) ? '[ '.$publicacion->categoria()->get()[0]->categoria.' ]' : '')}} [ {{$publicacion->fecha_publicacion}} ]
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ol>
            </div>
        </div><!-- panel-body -->
        <div class="panel-footer">
            <div class="row pull-right">
                <button class="btn btn-primary mr5 actualizarPublicacionesOrden" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Guardar</button>
            </div>
        </div><!-- panel-footer -->  
    </div><!-- panel -->
	
@stop