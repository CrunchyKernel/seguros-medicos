@extends('layout.master')

@section('contenido')
	<div class="page_title">
		<div class="container">
			<div class="title"><h1>Búsqueda: {{$categoria}}</h1></div>
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
							<div class="image_frame small"><a href="{{URL::to('/' . $contenido->alias)}}"><img src="{{((strlen($contenido->imagen_medium) > 0) ? $contenido->imagen_medium : asset('assets/images/preview_medium.png'))}}" title="{{$contenido->titulo}}"></a></div>
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
            @endif
            <!--
       		<div class="blog_post">	
				<div class="blog_postcontent">
					<div class="image_frame small"><a href="#"><img src="{{asset('assets/images/blog/blog-img17.jpg')}}" alt=""></a></div>
					<div class="post_info_content_small">
						<a href="blog-archive.html" class="date"><strong>18</strong><i>October</i></a>
						<h3><a href="blog-post.html">Lorem simply dummy text of the industry</a></h3>
						<ul class="post_meta_links_small">
							<li class="post_by"><a href="#">Harris jo</a></li>
							<li class="post_categoty"><a href="#">Web tutorials</a></li>
							<li class="post_comments"><a href="#">18 Comments</a></li>
						</ul>
						<div class="clearfix"></div>
						<p>There are many variations passages of available but theses in majority have suffered alteration in some form injecte humou or randomised words which don't looks even slightly believable embarrassing hidden in the middle.</p>
					</div>
				</div>
			</div>
            
            <div class="clearfix divider_line3"></div>
            
            <div class="blog_post">	
				<div class="blog_postcontent">
                <div class="video_frame small"><iframe src="http://www.youtube.com/embed/4mu0Gh8GVfU?wmode=transparent"></iframe></div>
                <div class="post_info_content_small">
                <a href="blog-archive.html" class="date"><strong>17</strong><i>October</i></a>
				<h3><a href="blog-post.html">There are many variations passages</a></h3>
                    <ul class="post_meta_links_small">
                        <li class="post_by"><a href="#">Adam</a></li>
                        <li class="post_categoty"><a href="#">Photography</a></li>
                        <li class="post_comments"><a href="#">12 Comments</a></li>
                    </ul>
                 
                 <div class="clearfix"></div>
                 	
                <p>There are many variations passages of available but theses in majority have suffered alteration in some form injecte humou or randomised words which don't looks even slightly believable embarrassing hidden in the middle.</p>
                
                </div>
				</div>
			</div>
            
            <div class="clearfix divider_line3"></div>
            
            <div class="blog_post">	
				<div class="blog_postcontent">
                <div class="image_frame small"><a href="#"><img src="{{asset('assets/images/blog/blog-img20.jpg')}}" alt=""></a></div>
                <div class="post_info_content_small">
                <a href="blog-archive.html" class="date"><strong>16</strong><i>October</i></a>
				<h3><a href="blog-post.html">Lorem Ipsum passage and going through</a></h3>
                    <ul class="post_meta_links_small">
                        <li class="post_by"><a href="#">Adams</a></li>
                        <li class="post_categoty"><a href="#">WP Themes</a></li>
                        <li class="post_comments"><a href="#">10 Comments</a></li>
                    </ul>
                 
				<div class="clearfix"></div>
                
                <p>There are many variations passages of available but theses in majority have suffered alteration in some form injecte humou or randomised words which don't looks even slightly believable embarrassing hidden in the middle.</p>
                
                </div>
				</div>
			</div>
            
            <div class="clearfix divider_line3"></div>
            
            <div class="blog_post">	
				<div class="blog_postcontent">
                <div class="image_frame small"><a href="#"><img src="{{asset('assets/images/blog/blog-img12.jpg')}}" alt=""></a></div>
                <div class="post_info_content_small">
                <a href="blog-archive.html" class="date"><strong>15</strong><i>October</i></a>
				<h3><a href="blog-post.html">Injected humour words</a></h3>
                    <ul class="post_meta_links_small">
                        <li class="post_by"><a href="#">Harrison</a></li>
                        <li class="post_categoty"><a href="#">Web tutorials</a></li>
                        <li class="post_comments"><a href="#">18 Comments</a></li>
                    </ul>
                 
                 <div class="clearfix"></div>
				
                <p>There are many variations passages of available but theses in majority have suffered alteration in some form injecte humou or randomised words which don't looks even slightly believable embarrassing hidden in the middle.</p>
                
                </div>
				</div>
			</div>
            
            <div class="clearfix divider_line3"></div>
            
            <div class="blog_post">	
				<div class="blog_postcontent">
                <div class="image_frame small"><a href="#"><img src="{{asset('assets/images/blog/blog-img13.jpg')}}" alt=""></a></div>
                <div class="post_info_content_small">
                <a href="blog-archive.html" class="date"><strong>14</strong><i>October</i></a>
				<h3><a href="blog-post.html">Latin words comined handful of mode</a></h3>
                    <ul class="post_meta_links_small">
                        <li class="post_by"><a href="#">Harrison</a></li>
                        <li class="post_categoty"><a href="#">Web tutorials</a></li>
                        <li class="post_comments"><a href="#">18 Comments</a></li>
                    </ul>
                 
				<div class="clearfix"></div>
                
                <p>There are many variations passages of available but theses in majority have suffered alteration in some form injecte humou or randomised words which don't looks even slightly believable embarrassing hidden in the middle.</p>
                
                </div>
				</div>
			</div>
            
            <div class="clearfix divider_line3"></div>
            
            <div class="blog_post">	
				<div class="blog_postcontent">
                <div class="image_frame small"><a href="#"><img src="{{asset('assets/images/blog/blog-img16.jpg')}}" alt=""></a></div>
                <div class="post_info_content_small">
                <a href="blog-archive.html" class="date"><strong>13</strong><i>October</i></a>
				<h3><a href="blog-post.html">Latin words comined handful of mode</a></h3>
                    <ul class="post_meta_links_small">
                        <li class="post_by"><a href="#">Harrison</a></li>
                        <li class="post_categoty"><a href="#">Web tutorials</a></li>
                        <li class="post_comments"><a href="#">18 Comments</a></li>
                    </ul>
                 
				<div class="clearfix"></div>
                
                <p>There are many variations passages of available but theses in majority have suffered alteration in some form injecte humou or randomised words which don't looks even slightly believable embarrassing hidden in the middle.</p>
                
                </div>
				</div>
			</div>
            
            <div class="clearfix divider_line3"></div>
            
            <div class="blog_post">	
				<div class="blog_postcontent">
                <div class="image_frame small"><a href="#"><img src="{{asset('assets/images/blog/blog-img17.jpg')}}" alt=""></a></div>
                <div class="post_info_content_small">
                <a href="blog-archive.html" class="date"><strong>12</strong><i>October</i></a>
				<h3><a href="blog-post.html">Latin words comined handful of mode</a></h3>
                    <ul class="post_meta_links_small">
                        <li class="post_by"><a href="#">Harrison</a></li>
                        <li class="post_categoty"><a href="#">Web tutorials</a></li>
                        <li class="post_comments"><a href="#">18 Comments</a></li>
                    </ul>
                 
				<div class="clearfix"></div>
                
                <p>There are many variations passages of available but theses in majority have suffered alteration in some form injecte humou or randomised words which don't looks even slightly believable embarrassing hidden in the middle.</p>
                
                </div>
				</div>
			</div>
            
            <div class="clearfix divider_line3"></div>
            
            <div class="blog_post">	
				<div class="blog_postcontent">
                <div class="image_frame small"><a href="#"><img src="{{asset('assets/images/blog/blog-img18.jpg')}}" alt=""></a></div>
                <div class="post_info_content_small">
                <a href="blog-archive.html" class="date"><strong>11</strong><i>October</i></a>
				<h3><a href="blog-post.html">Latin words comined handful of mode</a></h3>
                    <ul class="post_meta_links_small">
                        <li class="post_by"><a href="#">Harrison</a></li>
                        <li class="post_categoty"><a href="#">Web tutorials</a></li>
                        <li class="post_comments"><a href="#">18 Comments</a></li>
                    </ul>
                 
				<div class="clearfix"></div>
                
                <p>There are many variations passages of available but theses in majority have suffered alteration in some form injecte humou or randomised words which don't looks even slightly believable embarrassing hidden in the middle.</p>
                
                </div>
				</div>
			</div>
            
            <div class="clearfix divider_line3"></div>
            
            <div class="blog_post">	
				<div class="blog_postcontent">
                <div class="image_frame small"><a href="#"><img src="{{asset('assets/images/blog/blog-img19.jpg')}}" alt=""></a></div>
                <div class="post_info_content_small">
                <a href="blog-archive.html" class="date"><strong>10</strong><i>October</i></a>
				<h3><a href="blog-post.html">Latin words comined handful of mode</a></h3>
                    <ul class="post_meta_links_small">
                        <li class="post_by"><a href="#">Harrison</a></li>
                        <li class="post_categoty"><a href="#">Web tutorials</a></li>
                        <li class="post_comments"><a href="#">18 Comments</a></li>
                    </ul>
                 
				<div class="clearfix"></div>
                
                <p>There are many variations passages of available but theses in majority have suffered alteration in some form injecte humou or randomised words which don't looks even slightly believable embarrassing hidden in the middle.</p>
                
                </div>
				</div>
			</div>
            
            <div class="clearfix divider_line3"></div>
            -->
            <div class="pagination">
                {{html_entity_decode($paginacion)}}
			</div><!-- end pagination -->
        
        </div>
		@include('layout.rightPostBar')
	</div>
	
@stop