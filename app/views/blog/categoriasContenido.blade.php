@extends('layout.master')

@section('contenido')
	<div class="page_title">
		<div class="container">
			<div class="title"><h1>Categoría: {{$categoria->categoria}}</h1></div>
			<div class="pagenation">{{$rastroMigajasTexto}}</div>
			<!--<div class="pagenation">&nbsp;<a href="index.html">Inicio</a> <i>/</i> <a href="#">Blog</a> <i>/</i> Small Image</div>-->
		</div>
	</div>
	<div class="container">
		<div class="content_left">
			@if(isset($contenidosArray) && count($contenidosArray) > 0)
				@foreach($contenidosArray AS $contenido)
					<div class="blog_post">	
						<div class="blog_postcontent">
							<!--URL::to('/'.$contenido->categoria()->get()[0]->categoria_alias.'/'.$contenido->alias)-->
							<div class="image_frame small"><a href="{{URL::to('/' . $contenido->alias)}}"><img src="{{((strlen($contenido->imagen_medium) > 0) ? $contenido->imagen_medium : asset('assets/images/preview_medium.png'))}}" title="{{$contenido->titulo}}"> </a></div>
							<div class="post_info_content_small">
								<a class="date"><strong>{{date('d', strtotime($contenido->fecha_publicacion))}}</strong><i>{{Sistemafunciones::mesNombre(date('n', strtotime($contenido->fecha_publicacion)))}}<br>{{date('Y', strtotime($contenido->fecha_publicacion))}}</i></a>
								<h4><a href="{{URL::to('/' . $contenido->alias)}}">{{str_limit($contenido->titulo, 100)}}</a></h4>
								<ul class="post_meta_links_small">
									<!--<li class="post_by"><a href="#">Harris jo</a></li>-->
									<li class="post_categoty"><a href="{{URL::to('/'.$contenido->categoria()->get()[0]->categoria_alias)}}">{{$contenido->categoria()->get()[0]->categoria}}</a></li>
									<!--<li class="post_comments"><a href="#">18 Comments</a></li>-->
								</ul>
								<div class="clearfix"></div><br>
								<p>{{strip_tags(html_entity_decode($contenido->introtext))}}</p>
							</div>
						</div>
					</div>
					<div class="clearfix divider_line3"></div>
            	@endforeach
            @endif<div class="pagination">
                {{html_entity_decode($paginacion)}}
			</div>
        </div>
		@include('layout.rightPostBar')
	</div>
@stop