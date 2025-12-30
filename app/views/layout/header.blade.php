<!doctype html>
<!--[if IE 7 ]>    <html lang="en-gb" class="isie ie7 oldie no-js"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en-gb" class="isie ie8 oldie no-js"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en-gb" class="isie ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en-gb" class="no-js">
<!--<![endif]-->
<head>
	<title>{{((isset($metaTitulo)) ? $metaTitulo : '')}}</title>
	
	<meta charset="utf-8">
	<meta property="og:title" content="{{((isset($metaTitulo)) ? $metaTitulo.' - ' : '')}}Seguro de gastos médicos mayores"/>
    <meta property="og:url" content="https://segurodegastosmedicosmayores.mx"/>
    <meta property="og:description" content="{{((isset($metaDescripcion)) ? $metaDescripcion : 'Seguro de Gastos médicos')}}"/>
    <meta name="description" content="{{((isset($metaDescripcion)) ? $metaDescripcion : 'Contrata tu póliza de Seguro de Gastos')}}">
    <meta name="keywords" content="{{((isset($metaKeys)) ? $metaKeys : 'gastos mediocs,mapfre,multiva,metlife,plan seguro,polizas,siniestros,hospitales,seguros,aseguradoras')}}">
    
    <!-- Favicon --> 
	<link rel="shortcut icon" href="{{asset('assets/images/icon/favicon.ico')}}">
    
    <!-- this styles only adds some repairs on idevices  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <!-- Google fonts - witch you want to use - (rest you can just remove) -->
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans:400,800,700italic,700,600italic,600,400italic,300italic,300|Roboto:100,300,400,500,700&amp;subset=latin,latin-ext' type='text/css' />
    
    <!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
    
    {{HTML::style('assets/css/bootstrap/css/bootstrap.min.css')}}
    <!-- ######### CSS STYLES ######### -->
    {{HTML::style('assets/css/style.css')}}
    {{HTML::style('assets/css/custom.css')}}
    {{HTML::style('backend/css/bootstrap.min.css')}}
    {{HTML::style('assets/css/reset.css')}}
    {{HTML::style('assets/css/font-awesome/css/font-awesome.min.css')}}
    
    <!-- responsive devices styles -->
	{{HTML::style('assets/css/responsive-leyouts.css')}}
      
<!-- just remove the below comments witch bg patterns you want to use --> 
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-default.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-one.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-two.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-three.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-four.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-five.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-six.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-seven.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-eight.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-nine.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-ten.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-eleven.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-twelve.css" />-->
    <!--<link rel="stylesheet" href="css/bg-patterns/pattern-thirteen.css" />-->
    
    <!-- style switcher -->
    {{HTML::style('assets/js/style-switcher/color-switcher.css')}}
    
    <!-- sticky menu -->
    {{HTML::style('assets/js/sticky-menu/core.css')}}
    
    <!-- REVOLUTION SLIDER -->
   	{{HTML::style('assets/js/revolutionslider/rs-plugin/css/settings.css')}}
    {{HTML::style('assets/js/revolutionslider/css/slider_main.css')}}
    
    <!-- jquery jcarousel -->
    {{HTML::style('assets/js/jcarousel/skin.css')}}
    {{HTML::style('assets/js/jcarousel/skin2.css')}}
    {{HTML::style('assets/js/jcarousel/skin3.css')}}
	
	<!-- faqs -->
    {{HTML::style('assets/js/accordion/accordion.css')}}
    
    {{HTML::script('assets/js/style-switcher/jquery-1.js')}}
    {{HTML::script('assets/js/style-switcher/styleselector.js')}}

    {{HTML::script('assets/js/universal/jquery.js')}}
    {{HTML::script('assets/js/jquery.validate.min.js')}}
    {{HTML::script('backend/js/bootstrap.min.js')}}

    {{HTML::style('assets/js/tabs/tabs.css')}}
    {{HTML::script('assets/js/tabs/tabs.js')}}

    {{HTML::script('backend/js/sweetalert/sweetalert.min.js')}}
    {{HTML::style('backend/js/sweetalert/sweetalert.css')}}
    
    <!-- fancyBox -->
    {{HTML::style('assets/js/portfolio/source/jquery.fancybox.css')}}
    <script type="text/javascript">
        var _root_ = '{{URL::to('/')}}/';
    </script>
    <script type="text/css">
        *{font-family: Arial !important;
          font-size: large !important;  }
    </script>
	<!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-171027083-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-171027083-1');
    </script>
</head>

<body class="bg-cover">

