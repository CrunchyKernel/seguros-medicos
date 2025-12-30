
@section('contenido')
	<div class="row">
        <div class="col-md-12">
        	<form id="altaPublicacionForm" method="POST" enctype="multipart/form-data">
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">Alta de publicación</h4>
	                    <p>Complete el formulario con la mayor información posible.</p>
		                <div class="pull-right">
		                	@if(isset($publicacionDatos) && $publicacionDatos->id_blog > 0)
			                	<a href="{{URL::to('/'.$publicacionDatos->alias)}}" target="_blank">Ver publicación <span class="fa fa-external-link"></span></a>
		                	@endif
			            </div>
	                </div><!-- panel-heading -->
	                <div class="panel-body">
	                    <div class="col-sm-7">
	                        <div class="form-group">
	                            <label class="control-label">Título</label>
	                            <input type="text" id="titulo" name="titulo" class="form-control" value="{{((isset($publicacionDatos)) ? htmlentities($publicacionDatos->titulo) : '')}}" />
	                        </div><!-- form-group -->
	                    </div><!-- row -->
	                    <div class="col-sm-5">
	                        <div class="form-group">
	                            <label class="control-label">Alias</label>
	                            <input type="text" id="alias" name="alias" class="form-control" value="{{((isset($publicacionDatos)) ? htmlentities($publicacionDatos->alias) : '')}}" />
	                        </div><!-- form-group -->
	                    </div><!-- row -->
	                    <div class="col-sm-12">
	                        <div class="form-group">
	                            <label class="control-label">Contenido</label>
	                            <textarea id="contenido" name="contenido" placeholder="Enter text here..." class="form-control" rows="10">{{((isset($publicacionDatos)) ? $publicacionDatos->contenido : '')}}</textarea>
	                        </div><!-- form-group -->
	                    </div><!-- row -->
	                    <div class="col-sm-3">
	                        <div class="ckbox ckbox-success">
	                            <input type="checkbox" id="incluir_cotizador" name="incluir_cotizador" {{((isset($publicacionDatos)) ? (($publicacionDatos->incluir_cotizador == 1) ? 'checked' : '') : '')}}/>
                                <label for="incluir_cotizador">Incluir cotizador</label>
	                        </div><!-- form-group -->
	                    </div><!-- row -->
	                    <div class="col-sm-3">
	                        <div class="ckbox ckbox-success">
	                            <input type="checkbox" id="incluir_cotizador_nuevo" name="incluir_cotizador_nuevo" {{((isset($publicacionDatos)) ? (($publicacionDatos->incluir_cotizador_nuevo == 1) ? 'checked' : '') : '')}}/>
                                <label for="incluir_cotizador_nuevo">Incluir cotizador nuevo</label>
	                        </div><!-- form-group -->
	                    </div><!-- row -->
	                    <div class="col-sm-6">
	                    	<div class="text-right">
	                    		<button class="btn btn-primary mr5 publicar" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Publicar contenido</button>
	                    	</div>
	                    </div>
	                    <div class="col-sm-12">
	                        <div class="ckbox ckbox-success">
	                            <input type="checkbox" id="raw" name="raw" {{((isset($publicacionDatos)) ? (($publicacionDatos->raw == 1) ? 'checked' : '') : '')}}/>
                                <label for="raw">Raw</label>
	                        </div><!-- form-group -->
	                    </div><!-- row -->
	                    <div class="col-sm-6">
	                        <div class="form-group" style="padding-bottom: 20px; border-bottom: 1px dashed #ddd;">
	                            <label class="control-label">Meta key</label>
	                            <input id="metakey" name="metakey" class="form-control" value="{{((isset($publicacionDatos)) ? $publicacionDatos->metakey : '')}}" />
	                        </div><!-- form-group -->
	                    </div>
	                    <div class="col-sm-6">
	                    	<div class="form-group" style="padding-bottom: 20px; border-bottom: 1px dashed #ddd;">
	                            <label class="control-label">Meta descripción</label>
	                            <textarea class="form-control" id="metadesc" name="metadesc" rows="4" style="width: 100%;" maxlength="155">{{((isset($publicacionDatos)) ? $publicacionDatos->metadesc : '')}}</textarea>
	                        </div><!-- form-group -->
	                    </div>
	                   	<div class="col-sm-7">
	                        <div class="form-group" style="padding-bottom: 20px; border-bottom: 1px dashed #ddd;">
	                            <label class="control-label">Categoría</label>
	                            <select id="id_blog_categoria" name="id_blog_categoria" data-placeholder="Selecciona la categoria" style="width: 100%;">
	                            	{{$categoriasOption}}
	                            </select>
	                        </div><!-- form-group -->
	                    </div><!-- row -->
	                    <div class="col-sm-5">
	                        <div class="form-group" style="padding-bottom: 20px; border-bottom: 1px dashed #ddd;">
	                            <label class="control-label">Fecha publicación</label>
	                            <div class="input-group">
                                    <input type="text" class="form-control" placeholder="yyyy-mm-dd" id="fecha_publicacion" name="fecha_publicacion" value="{{((isset($publicacionDatos)) ? $publicacionDatos->fecha_publicacion : '')}}" readonly="" style="cursor: pointer;" >
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div><!-- input-group -->
	                        </div><!-- form-group -->
	                    </div><!-- row -->
	                    <div class="col-sm-6">
                            <label class="control-label">Imagen</label>
                            <div class="custom_file_upload">
								<div class="file_upload">
									<input type="file" id="imagen" name="imagen" accept="image/gif, image/jpeg, image/png, image/jpg">
								</div>
							</div>
                    		<label class="control-label">Vista previa</label><br>
                    		<div class="ckbox ckbox-warning">
                                <input type="checkbox" id="eliminarImagen" name="eliminarImagen" />
                                <label for="eliminarImagen">Eliminar imagen almacenada</label>
                            </div>
                            <img id="preview" src="{{((isset($publicacionDatos) && strlen($publicacionDatos->imagen_large) > 0) ? $publicacionDatos->imagen_large : asset('backend/images/preview.png') )}}" style="width: 100%; height: 300px;" />
                    	</div>
	                </div><!-- panel-body -->
	                <div class="panel-footer">
	                	<div class="row pull-right">
	            			<button class="btn btn-primary mr5 publicar" data-loading-text='Processando {{HTML::image('backend/images/loaders/loader31.gif')}}'>Publicar contenido</button>
	                	</div>
	                </div><!-- panel-footer -->  
	            </div><!-- panel -->
	            <input type="hidden" id="id_blog" name="id_blog" value="{{((isset($publicacionDatos)) ? $publicacionDatos->id_blog : '-1')}}">
            </form>
        </div><!-- col-md-6 -->
    </div><!-- row-->
@stop