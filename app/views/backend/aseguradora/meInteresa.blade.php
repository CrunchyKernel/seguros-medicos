@section('contenido')
   <ul class="nav nav-tabs nav-success">
		@if(isset($aseguradoras) && count($aseguradoras) > 0)
			<?php $n=0; ?>
        	@foreach($aseguradoras AS $aseguradora)
        		<li class="{{(($n == 0) ? 'active' : '')}}"><a href="#{{$aseguradora->aseguradora}}_tab" data-toggle="tab"><strong>{{$aseguradora->nombre}}</strong></a></li>
        		<?php $n++; ?>
        	@endforeach
        @endif
    </ul>
    <div class="tab-content mb30">
    	@if(isset($aseguradoras) && count($aseguradoras) > 0)
    		<?php $n=0; $paqueteN = 1; ?>
            <?php $id = 0; ?>
        	@foreach($aseguradoras AS $aseguradora)
        		<div class="tab-pane {{(($n == 0) ? 'active' : '')}}" id="{{$aseguradora->aseguradora}}_tab">
	        			<ul class="nav nav-tabs nav-info">
	        				<?php $m=0; ?>
		            		@foreach($aseguradora->Paquetes AS $paquete)
			                	<li class="{{(($m == 0) ? 'active' : '')}}"><a href="#{{$paquete->id_paquete . '-' . $paquete->paquete_campo}}" data-toggle="tab"><strong>{{$paquete->paquete}}</strong></a></li>
			                	<?php $m++; ?>
			                @endforeach
			            </ul>
			            <div class="tab-content tab-content-info mb30">
			            	<?php $m=0; ?>
			            	 @foreach($aseguradora->Paquetes as $paquete)
                                <div class="tab-pane {{(($m == 0) ? 'active' : '')}}" id="{{$paquete->id_paquete . '-' . $paquete->paquete_campo}}">
                                    <form>
                                       <!--$paquetes[$paqueteN-1]->descripcion_backend-->
                                        <textarea name="editor{{$paqueteN}}" rows="10" cols="10" class="ckeEditor {{$aseguradora->aseguradora}}">
                                            {{ $paquete->descripcion_me_interesa }}
                                        </textarea>
                                    </form>
                                    <br>
                                        <button class="btn btn-success" id="editor{{$paqueteN}}G" data-id="{{$paquete->id_paquete}}">Guardar descripción</button>
                                    <?php $paqueteN++; ?>
                                </div>
                                <?php $m++; ?>
                             @endforeach 
                        </div>
		        </div>
		        <?php $n++; ?>
        	@endforeach
        @endif
    </div>
   <!-- <script>
      CKEDITOR.replace('editor1', {
           customConfig: '/backend/js/ckeditor/config.js'
       });
       CKEDITOR.plugins.addExternal('editor1', '/backend/js/ckeditor/plugins/justify', 'plugin.js');
       CKEDITOR.replace('editor2', {
           customConfig: '/backend/js/ckeditor/config.js'
       });
       CKEDITOR.plugins.addExternal('editor2', '/backend/js/ckeditor/plugins/justify', 'plugin.js');
       CKEDITOR.replace('editor3', {
           customConfig: '/backend/js/ckeditor/config.js'
       });
       CKEDITOR.plugins.addExternal('editor3', '/backend/js/ckeditor/plugins/justify', 'plugin.js');
       CKEDITOR.replace('editor4', {
           customConfig: '/backend/js/ckeditor/config.js'
       });
       CKEDITOR.plugins.addExternal('editor4', '/backend/js/ckeditor/plugins/justify', 'plugin.js');
       CKEDITOR.replace('editor5', {
           customConfig: '/backend/js/ckeditor/config.js'
       });
       CKEDITOR.plugins.addExternal('editor5', '/backend/js/ckeditor/plugins/justify', 'plugin.js');
       CKEDITOR.replace('editor6', {
           customConfig: '/backend/js/ckeditor/config.js'
       });
       CKEDITOR.plugins.addExternal('editor6', '/backend/js/ckeditor/plugins/justify', 'plugin.js');
       CKEDITOR.replace('editor7', {
           customConfig: '/backend/js/ckeditor/config.js'
       });
       CKEDITOR.plugins.addExternal('editor7', '/backend/js/ckeditor/plugins/justify', 'plugin.js');
       CKEDITOR.replace('editor8', {
           customConfig: '/backend/js/ckeditor/config.js'
       });
       CKEDITOR.plugins.addExternal('editor8', '/backend/js/ckeditor/plugins/justify', 'plugin.js');
   </script>  !-->
@stop