<div class="right_sidebar">
    <div class="site-search-area">
        <form method="get" id="site-searchform" action="{{URL::to('/buscar')}}">
            <div>
                <input class="input-text" name="buscar" id="s" placeholder="Estoy buscando..." type="text">
                <input id="searchsubmit" value="Buscar" type="submit">
            </div>
        </form>
    </div>
    <div class="clearfix mar_top4"></div>
	<div class="sidebar_widget">
    	<div class="sidebar_title"><h3><i>Categorías</i></h3></div>
		<ul class="arrows_list1">
            {{$blogCategoriasMenu}}
		</ul>
	</div>
    <div class="clearfix mar_top4"></div>
    <div class="sidebar_widget">
    	<div class="sidebar_title"><h3>Publicaciones <i>recientes</i></h3></div>
			<ul class="recent_posts_list">
                @if(isset($contenidosReciente) && count($contenidosReciente) > 0)
                    @foreach($contenidosReciente AS $contenidoReciente)
                        <li>
                        	<!--URL::to('/'.Blogcategoria::find($contenidoReciente->id_blog_categoria)->categoria_alias.'/'.$contenidoReciente->alias)-->
                            <span><a href="{{URL::to('/' . $contenidoReciente->alias)}}"><img src="{{((strlen($contenidoReciente->imagen_small) > 0) ? $contenidoReciente->imagen_small : asset('assets/images/preview_small.png'))}}"></a></span>
                            <a href="{{URL::to('/' . $contenidoReciente->alias)}}">{{$contenidoReciente->titulo}}</a>
                            <i>{{Sistemafunciones::fechaLetras(date($contenidoReciente->fecha_publicacion))}}</i> 
                        </li>
                    @endforeach
                @endif
            </ul>
	</div>
    <!--
    <div class="clearfix mar_top4"></div>
    <div class="clientsays_widget">
    	<div class="sidebar_title"><h3>Happy <i>Client Say's</i></h3></div>
        <img src="{{asset('assets/images/site-img25.jpg')}}" alt="">
        <strong>- Henry Brodie</strong><p>Lorem Ipsum passage, and going through the cites of the word here classical literature passage discovered undou btable source. which looks reasonable of the generated charac eristic words.</p>  
	</div>
    <div class="clearfix mar_top4"></div>
    <div class="sidebar_widget">
    	<div class="sidebar_title"><h3>Portfolio <i>Widget</i></h3></div>
        <div class="portfolio_sidebar_widget">
        	<div class=" jcarousel-skin-tango">
                <div class="jcarousel-container jcarousel-container-horizontal" style="position: relative; display: block;">
                    <div class="jcarousel-clip jcarousel-clip-horizontal" style="position: relative;">
                        <ul id="mycarouseltwo" class="jcarousel-list jcarousel-list-horizontal" style="overflow: hidden; position: relative; top: 0px; margin: 0px; padding: 0px; left: 0px; width: 1575px;">
                            <li class="jcarousel-item jcarousel-item-horizontal jcarousel-item-1 jcarousel-item-1-horizontal" jcarouselindex="1" style="float: left; list-style: none;">
                                <div class="item">                        
                                    <div class="fresh_projects_list">
                                        <section class="cheapest">
                                            <div class="display">                  
                                                <div class="small-group">        
                                                    <div class="small money">
                                                            <img src="{{asset('assets/images/site-img01.jpg')}}" alt="">
                                                            <div class="info">
                                                                <div class="title">Many Webs Available</div>
                                                                <div class="additionnal">
                                                                	There are many variations pasages
                                                                    lpsum available but in the majority
                                                                    have suffered alteration [...]<br><br>
                                                                    <b><a href="#">more &gt;</a></b>
                                                                </div>
                                                            </div>
                                                            <div class="hover"></div> 
                                                    </div>        
                                                </div>     
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </li>
                            <li class="jcarousel-item jcarousel-item-horizontal jcarousel-item-2 jcarousel-item-2-horizontal" jcarouselindex="2" style="float: left; list-style: none;">
                                <div class="item">                        
                                    <div class="fresh_projects_list">
                                        <section class="cheapest">
                                            <div class="display">                  
                                                <div class="small-group">        
                                                    <div class="small money">
                                                            <img src="{{asset('assets/images/site-img02.jpg')}}" alt="">
                                                            <div class="info">
                                                                <div class="title">Suffered Alteration</div>
                                                                <div class="additionnal">
                                                                	There are many variations pasages
                                                                    lpsum available but in the majority
                                                                    have suffered alteration [...]<br><br>
                                                                    <b><a href="#">more &gt;</a></b>
                                                                </div>
                                                            </div>
                                                            <div class="hover"></div> 
                                                    </div>        
                                                </div>     
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </li>
                            <li class="jcarousel-item jcarousel-item-horizontal jcarousel-item-3 jcarousel-item-3-horizontal" jcarouselindex="3" style="float: left; list-style: none;">
                                <div class="item">                        
                                    <div class="fresh_projects_list">
                                        <section class="cheapest">
                                            <div class="display">                  
                                                <div class="small-group">        
                                                    <div class="small money">
                                                            <img src="{{asset('assets/images/site-img03.jpg')}}" alt="">
                                                            <div class="info">
                                                                <div class="title">The Random Words</div>
                                                                <div class="additionnal">
                                                                	There are many variations pasages
                                                                    lpsum available but in the majority
                                                                    have suffered alteration [...]<br><br>
                                                                    <b><a href="#">more &gt;</a></b>
                                                                </div>
                                                            </div>
                                                            <div class="hover"></div>
                                                    </div>        
                                                </div>     
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </li>
                            <li class="jcarousel-item jcarousel-item-horizontal jcarousel-item-4 jcarousel-item-4-horizontal" jcarouselindex="4" style="float: left; list-style: none;">
                                <div class="item">                        
                                    <div class="fresh_projects_list">
                                        <section class="cheapest">
                                            <div class="display">                  
                                                <div class="small-group">        
                                                    <div class="small money">
                                                            <img src="{{asset('assets/images/site-img04.jpg')}}" alt="">
                                                            <div class="info">
                                                                <div class="title">Themes Believable</div>
                                                                <div class="additionnal">
                                                                	There are many variations pasages
                                                                    lpsum available but in the majority
                                                                    have suffered alteration [...]<br><br>
                                                                    <b><a href="#">more &gt;</a></b>
                                                                </div>
                                                            </div>
                                                            <div class="hover"></div>  
                                                    </div>        
                                                </div>     
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </li>
                            <li class="jcarousel-item jcarousel-item-horizontal jcarousel-item-5 jcarousel-item-5-horizontal" jcarouselindex="5" style="float: left; list-style: none;">
                                <div class="item">                        
                                    <div class="fresh_projects_list">
                                        <section class="cheapest">
                                            <div class="display">                  
                                                <div class="small-group">        
                                                    <div class="small money">
                                                            <img src="{{asset('assets/images/site-img05.jpg')}}" alt="">
                                                            <div class="info">
                                                                <div class="title">Suspendisse Opens</div>
                                                                <div class="additionnal">
                                                                	There are many variations pasages
                                                                    lpsum available but in the majority
                                                                    have suffered alteration [...]<br><br>
                                                                    <b><a href="#">more &gt;</a></b>
                                                                </div>
                                                            </div>
                                                            <div class="hover"></div>
                                                    </div>        
                                                </div>     
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="jcarousel-prev jcarousel-prev-horizontal jcarousel-prev-disabled jcarousel-prev-disabled-horizontal" disabled="disabled" style="display: block;"></div>
                    <div class="jcarousel-next jcarousel-next-horizontal" style="display: block;"></div>
                </div>
            </div>
		</div>
	</div>
    <div class="clearfix mar_top4"></div>
	<div class="sidebar_widget"> 
        <div class="sidebar_title"><h3>Text <i>Widget</i></h3></div>
        <p>Going to use a passage of lorem lpsum you need to be sure there anything embarrassin hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend the repeat predefined chunks as thenecessary making this the first true generator.</p>      
	</div>
    -->
</div>