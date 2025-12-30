	<!--<div class="clearfix"></div>-->
	<!-- Footer======================================= -->
	<div class="footer">
		<div class="container">
			<div class="one_third">
				<div class="footer_logo">
					<img class="img-thumbnail img-responsive" src="{{asset('protectodiez/sgmmPNG/seguro_de_gastos_medicos.png')}}"  height="130" width="250" alt="seguro de gastosmedicos mayores" />
				</div><!-- end footer logo -->
				<ul class="contact_address">
					<li>Seguros de Gastos Médicos Mayores</li>
					<li>
						<i class="fa fa-map-marker fa-lg"></i>
						&nbsp;Av. Inglaterra 2790-3<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guadalajara, Jalisco
					</li>
					<li><i class="fa fa-phone"></i>&nbsp; (33) 200-201-70</li>
					<li><i class="fa fa-envelope-o"></i>&nbsp; <a href="mailto:ventas@segurodegastosmedicosmayores.mx">ventas@segurodegastosmedicosmayores.mx</a></li>
					<!--<li><img src="{{asset('assets/images/footer-wmap.png')}}" alt="" /></li>-->
					<li>&nbsp;</li>
				</ul>
			</div><!-- end address section -->
			@if(!isset($PDF))
			<div class="one_third">
				<h2>Mapa <i>de sitio</i></h2>
				<ul class="list">
					{{$menuSitioFooter}}
				</ul>
			</div><!-- end useful links -->
			<!--
			<div class="one_fourth">
				&nbsp;
				<div class="twitter_feed">
					<h2>Latest <i>Tweets</i></h2>
					<div class="left"><i class="fa fa-twitter fa-lg"></i></div>
					<div class="right">
						<a href="https://twitter.com/gsrthemes9" target="_blank">gsrthemes9</a>: 
						Avira - Responsive html5 Professional and Brand New Look Template on ThemeForest.<br />
						<a href="#" class="small">.9 days ago</a>.
						<a href="#" class="small">reply</a>.
						<a href="#" class="small">retweet</a>.
						<a href="#" class="small">favorite</a>
					</div>
					<div class="clearfix divider_line4"></div>
					<div class="left"><i class="fa fa-twitter fa-lg"></i></div>
					<div class="right">
						<a href="https://twitter.com/gsrthemes9" target="_blank">gsrthemes9</a>:
						Kinvexy - Responsive HTML5 / CSS3, Professional Corporate Theme.<br />
						<a href="#" class="small">.10 days ago</a>.
						<a href="#" class="small">reply</a>.
						<a href="#" class="small">retweet</a>.
						<a href="#" class="small">favorite</a>
					</div>
				</div>
			</div>
			-->
			<!--div class="one_third last">
				<div>
					<div class="g-page" data-href="//plus.google.com/u/0/116913483414601504716" data-theme="dark" data-rel="publisher"></div>
                    <script type="text/javascript">
                    	//window.___gcfg = {lang: 'es'};
                    	//(function() {
                    	//	var po = document.createElement('script');
                    	//	po.type = 'text/javascript';
                    	//	po.async = true;
                    	//	po.src = 'https://apis.google.com/js/platform.js';
                    	//	var s = document.getElementsByTagName('script')[0];
                    	//	s.parentNode.insertBefore(po, s);
                    	//})();
                    </script>
				</div>
			</div-->
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="copyright_info">
		<div class="container">
			<div class="two_third">
				<b style="color: #fff !important;">Copyright © {{date('Y')}} Derechos reservados. <a href="{{URL::to('/aviso-privacidad')}}" style="color: #fff !important;">Políticas de privacidad</a></b>
			</div>
			<div class="one_third last">
				<ul class="footer_social_links">
					<li><a href="{{Share::load(Request::url(), 'Seguro de gastos medicos')->digg()}}" target="_blank"><i class="fa fa-digg fa-lg" style="color: #fff !important;"></i></a></li>
					<li><a href="{{Share::load(Request::url(), 'Seguro de gastos medicos')->facebook()}}" target="_blank">&nbsp;<i class="fa fa-facebook fa-lg" style="color: #fff !important;"></i>&nbsp;</a></li>
					<li><a href="{{Share::load(Request::url(), 'Seguro de gastos medicos')->gplus()}}" target="_blank"><i class="fa fa-google-plus fa-lg" style="color: #fff !important;"></i></a></li>
					<li><a href="{{Share::load(Request::url(), 'Seguro de gastos medicos')->linkedin()}}" target="_blank"><i class="fa fa-linkedin fa-lg" style="color: #fff !important;"></i></a></li>
					<li><a href="{{Share::load(Request::url(), 'Seguro de gastos medicos')->pinterest()}}" target="_blank"><i class="fa fa-pinterest fa-lg" style="color: #fff !important;"></i></a></li>
					<li><a href="{{Share::load(Request::url(), 'Seguro de gastos medicos')->reddit()}}" target="_blank"><i class="fa fa-reddit fa-lg" style="color: #fff !important;"></i></a></li>
					<li><a href="{{Share::load(Request::url(), 'Seguro de gastos medicos')->tumblr()}}" target="_blank">&nbsp;<i class="fa fa-tumblr fa-lg" style="color: #fff !important;"></i>&nbsp;</a></li>
					<li><a href="{{Share::load(Request::url(), 'Seguro de gastos medicos')->twitter()}}" target="_blank"><i class="fa fa-twitter fa-lg" style="color: #fff !important;"></i></a></li>
				</ul>
        	</div>
    	</div>
	</div><!-- end copyright info -->
	<a href="#" class="scrollup">Scroll</a><!-- end scroll to top of the page-->



<!-- ######### JS FILES ######### -->

<!-- main menu -->
{{HTML::script('assets/js/mainmenu/ddsmoothmenu.js')}}

{{HTML::script('assets/js/mainmenu/selectnav.js')}}

<!-- jquery jcarousel -->
{{HTML::script('assets/js/jcarousel/jquery.jcarousel.min.js')}}

<!-- SLIDER REVOLUTION 4.x SCRIPTS  -->
{{HTML::script('assets/js/revolutionslider/rs-plugin/js/jquery.themepunch.plugins.min.js')}}
{{HTML::script('assets/js/revolutionslider/rs-plugin/js/jquery.themepunch.revolution.min.js')}}
{{HTML::script('assets/js/tabs/tabwidget/tabwidget.js')}}

{{HTML::script('assets/js/mainmenu/scripts.js')}}

{{HTML::script('assets/js/helpers/custom.js')}}

<!-- scroll up -->
<script type="text/javascript">
    $(document).ready(function(){
 		$(window).scroll(function(){
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });
 
        $('.scrollup').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 500);
            return false;
        });
 
    });
</script>

<!-- jquery jcarousel -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#mycarousel').jcarousel();
		jQuery('#mycarouseltwo').jcarousel();
		jQuery('#mycarouselthree').jcarousel();
		jQuery('#mycarouselfour').jcarousel();
	});
</script>

<!-- accordion -->
{{HTML::script('assets/js/accordion/custom.js')}}

<!-- REVOLUTION SLIDER -->
<script type="text/javascript">

	var revapi;

	jQuery(document).ready(function() {
		
		revapi = jQuery('.tp-banner').revolution(
			{
				delay:9000,
				startwidth:1170,
				startheight:500,
				hideThumbs:10,
				fullWidth:"on",
				forceFullWidth:"on"
			});
		
	});	//ready

</script>
<!-- fade news -->
<script type="application/javascript">
(function() {
    var quotes = $(".quotes");
    var quoteIndex = -1;
    
    function showNextQuote() {
        ++quoteIndex;
        quotes.eq(quoteIndex % quotes.length)
            .fadeIn(1000)
            .delay(4000)
            .fadeOut(1000, showNextQuote);
    }
    
    showNextQuote();
	
})();
</script>
	@endif
{{HTML::script('assets/js/sticky-menu/core.js')}}
{{HTML::script('assets/js/sticky-menu/modernizr.custom.75180.js')}}

<!-- fancyBox -->
{{HTML::script('assets/js/portfolio/source/jquery.fancybox.js')}}
{{HTML::script('assets/js/portfolio/source/helpers/jquery.fancybox-media.js')}}

<!-- cotizador -->
{{HTML::script('assets/js/helpers/cotizador1.js')}}

<script type="text/javascript">
    $(document).ready(function() {
        /* Simple image gallery. Uses default settings */
        $('.fancybox').fancybox();
        /* media effects*/  
        $(document).ready(function() {
            $('.fancybox-media').fancybox({
                openEffect  : 'none',
                closeEffect : 'none',
                helpers : {
                    media : {}
                }
            });
        });

    });
</script>


</body>
</html>
