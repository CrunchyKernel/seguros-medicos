@extends('layout.master')

@section('contenido')
	<div class="page_title">
		<div class="container">
			<div class="title"><h1>{{$contenido->titulo}}</h1></div>
			<!--<div class="pagenation">&nbsp;<a href="index.html">Inicio</a> <i>/</i> <a href="#">Blog</a> <i>/</i> Small Image</div>-->
			<div class="pagenation">{{$rastroMigajasTexto}}</div>
		</div>
	</div>
	<div class="container">
		<div class="content_left">
            <div class="blog_post">	
				<div class="blog_postcontent">
					@if(strlen($contenido->imagen_large) > 0)
						<div class="image_frame">
	                		<img src="{{$contenido->imagen_large}}" title="{{$contenido->titulo}}">
	                	</div>
					@endif
	                <a href="" class="date"><strong>{{date('d', strtotime($contenido->fecha_publicacion))}}</strong><i>{{SistemaFunciones::mesNombre(date('n', strtotime($contenido->fecha_publicacion)))}}<br>{{date('Y', strtotime($contenido->fecha_publicacion))}}</i></a>
					<h4>{{$contenido->titulo}}</h4>
	                    <ul class="post_meta_links">
	                        <!--<li class="post_by"><a href="#">Adam Harrison</a></li>-->
	                        <li class="post_categoty"><a href="{{URL::to('/'.$contenido->categoria()->get()[0]->categoria_alias)}}">{{$contenido->categoria()->get()[0]->categoria}}</a></li>
	                        <!--<li class="post_comments"><a href="#">18 Comments</a></li>-->
	                    </ul>
	                 
					<div class="post_info_content">
	                	{{$contenido->contenido}}
	                </div>
				</div>
			</div>
			@if($contenido->incluir_cotizador == 1)
	        	<div class="clearfix divider_line"></div>
	            <div class="blog_post">
	            	@include('layout.cotizador')
	            </div>
	        @endif
            <div class="clearfix divider_line"></div>
            <div class="sharepost">
            	<table width="100%">
            		<tr>
            			<td style="vertical-align: middle;"><h4 style="margin-bottom: 0px;"><i>Comparte esta publicación</i></h4></td>
            			<td>
							<ul>
								<li><a href="{{Share::load(URL::to('/verContenido/'.$contenido->alias), $contenido->titulo)->digg()}}" target="_blank"><i class="fa fa-digg fa-lg"></i></a></li>
								<li><a href="{{Share::load(URL::to('/verContenido/'.$contenido->alias), $contenido->titulo)->facebook()}}" target="_blank">&nbsp;<i class="fa fa-facebook fa-lg"></i>&nbsp;</a></li>
								<li><a href="{{Share::load(URL::to('/verContenido/'.$contenido->alias), $contenido->titulo)->gplus()}}" target="_blank"><i class="fa fa-google-plus fa-lg"></i></a></li>
								<li><a href="{{Share::load(URL::to('/verContenido/'.$contenido->alias), $contenido->titulo)->linkedin()}}" target="_blank"><i class="fa fa-linkedin fa-lg"></i></a></li>
								<li><a href="{{Share::load(URL::to('/verContenido/'.$contenido->alias), $contenido->titulo)->pinterest()}}" target="_blank"><i class="fa fa-pinterest fa-lg"></i></a></li>
								<li><a href="{{Share::load(URL::to('/verContenido/'.$contenido->alias), $contenido->titulo)->reddit()}}" target="_blank"><i class="fa fa-reddit fa-lg"></i></a></li>
								<li><a href="{{Share::load(URL::to('/verContenido/'.$contenido->alias), $contenido->titulo)->tumblr()}}" target="_blank">&nbsp;<i class="fa fa-tumblr fa-lg"></i>&nbsp;</a></li>
								<li><a href="{{Share::load(URL::to('/verContenido/'.$contenido->alias), $contenido->titulo)->twitter()}}" target="_blank"><i class="fa fa-twitter fa-lg"></i></a></li>
							</ul>
            			</td>
            		</tr>
            	</table>
			</div>
            <div class="clearfix divider_line" style="height: 0px;"></div>
            <!--
        	<h4><i>About the Author</i></h4>
        	<div class="about_author">
            	<img src="images/blog/avatar.jpg" alt="">
            	<a href="http://themeforest.net/user/gsrthemes9/portfolio" target="_blank">GSR Themes</a><br>
            	I'm a freelance designer with satisfied clients worldwide. I design simple, clean websites and develop easy-to-use applications. Web Design is not just my job it's my passion. You need professional web designer you are welcome.
            </div>
            -->
            @if(isset($contenidosRelacionados) && count($contenidosRelacionados) > 0)
	            <div class="one_half"> 
	            	<div class="popular-posts-area">
	            		<h4><i>Publicaciones de la categoría</i></h4>
	            		<ul class="recent_posts_list">
	            			@foreach($contenidosRelacionados AS $contenidoRelacionado)
	            				<li>
	            					<!--URL::to('/'.Blogcategoria::find($contenidoRelacionado->id_blog_categoria)->categoria_alias.'/'.$contenidoRelacionado->alias)-->
								  	<span><a href="{{URL::to('/' . $contenidoRelacionado->alias)}}"><img src="{{((strlen($contenidoRelacionado->imagen_small) > 0) ? $contenidoRelacionado->imagen_small : asset('assets/images/preview_small.png'))}}" title="{{$contenidoRelacionado->titulo}}"></a></span>
									<a href="{{URL::to('/' . $contenidoRelacionado->alias)}}">{{$contenidoRelacionado->titulo}}</a>
									<i>{{Sistemafunciones::fechaLetras(date($contenidoRelacionado->fecha_publicacion))}}</i> 
								</li>
	            			@endforeach
						</ul>
					</div>
				</div>
			@endif
			@if(isset($contenidosPopulares) && count($contenidosPopulares) > 0)
				<div class="one_half last"> 
		       		<div class="popular-posts-area">
			       		<h4><i>Publicaciones relacionadas</i></h4>
			       		<ul class="recent_posts_list">
			       			@foreach($contenidosPopulares AS $contenidoPopular)
			       				<li>
			       					<!--URL::to('/'.Blogcategoria::find($contenidoPopular->id_blog_categoria)->categoria_alias.'/'.$contenidoPopular->alias)-->
								  	<span><a href="{{URL::to('/' . $contenidoPopular->alias)}}"><img src="{{((strlen($contenidoPopular->imagen_small) > 0) ? $contenidoPopular->imagen_small : asset('assets/images/preview_small.png'))}}" title="{{$contenidoPopular->titulo}}"></a></span>
									<a href="{{URL::to('/' . $contenidoPopular->alias)}}">{{$contenidoPopular->titulo}}</a>
									 <i>{{Sistemafunciones::fechaLetras(date($contenidoPopular->fecha_publicacion))}}</i> 
								</li>
			       			@endforeach
						</ul>
					</div>
				</div>
			@endif
			<!--
			<div class="clearfix divider_line"></div>
			<h4><i>Comments</i></h4>
			<div class="mar_top_bottom_lines_small3"></div>
			<div class="comment_wrap">
				<div class="gravatar"><img src="images/blog/people_img.jpg" alt=""></div>
				<div class="comment_content">
					<div class="comment_meta">
						<div class="comment_author">Admin - <i>October 12, 2013</i></div>
					</div>
					<div class="comment_text">
						<p>Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage.</p>
						<a href="#">Reply</a>
					</div>
				</div>
			</div>
			<div class="comment_wrap chaild">
				<div class="gravatar"><img src="images/blog/people_img.jpg" alt=""></div>
				<div class="comment_content">
					<div class="comment_meta">
						<div class="comment_author">Admin - <i>October 12, 2013</i></div>
					</div>
					<div class="comment_text">
						<p>Lorem ipsum dolor sit amet, consectetur rius a auctor enim accumsan.</p>
						<a href="#">Reply</a>
					</div>
				</div>
			</div>
			<div class="comment_wrap chaild">
				<div class="gravatar"><img src="images/blog/people_img.jpg" alt=""></div>
				<div class="comment_content">
					<div class="comment_meta">
						<div class="comment_author">Admin - <i>October 12, 2013</i></div>
					</div>
					<div class="comment_text">
						<p>Lorem ipsum dolor sit amet, consectetur rius a auctor enim accumsan.</p>
						<a href="#">Reply</a>
					</div>
				</div>
			</div>
			<div class="comment_wrap chaild">
				<div class="gravatar"><img src="images/blog/people_img.jpg" alt=""></div>
				<div class="comment_content">
					<div class="comment_meta">
						<div class="comment_author">Admin - <i>October 12, 2013</i></div>
					</div>
					<div class="comment_text">
						<p>Lorem ipsum dolor sit amet, consectetur rius a auctor enim accumsan.</p>
						<a href="#">Reply</a>
					</div>
				</div>
			</div>
			<div class="comment_form">
				<h4><i>Leave a Comment</i></h4>
				<form action="blog-post.html" method="post">
					<input type="text" name="yourname" id="name" class="comment_input_bg">
					<label for="name">Name*</label>
					<input type="text" name="email" id="mail" class="comment_input_bg">
					<label for="mail">Mail*</label>
					<input type="text" name="website" id="website" class="comment_input_bg">
					<label for="website">Website</label>
					<textarea name="comment" class="comment_textarea_bg" rows="20" cols="7"></textarea>
					<div class="clearfix"></div> 
					<input name="send" type="submit" value="Submit Comment" class="comment_submit">
					<p></p>
					<p class="comment_checkbox"><input type="checkbox" name="check"> Notify me of followup comments via e-mail</p>
				</form>
			</div>
			<div class="clearfix mar_top2"></div>
			-->
		</div>
		@include('layout.rightPostBar')
	</div>
	
@stop