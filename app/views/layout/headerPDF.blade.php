<!doctype html>
<!--[if IE 7 ]>    <html lang="en-gb" class="isie ie7 oldie no-js"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en-gb" class="isie ie8 oldie no-js"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en-gb" class="isie ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en-gb" class="no-js">
<!--<![endif]-->
<head>
    <title>{{((isset($metaTitulo)) ? $metaTitulo.' - ' : '')}}Gastos médicos</title>

    <meta charset="utf-8">
    <meta property="og:title" content="{{((isset($metaTitulo)) ? $metaTitulo.' - ' : '')}}Seguro de gastos médicos mayores"/>
    <meta property="og:url" content="http://gastos-medicos.com.mx"/>
    <meta property="og:description" content="{{((isset($metaDescripcion)) ? $metaDescripcion : 'Seguro de Gastos médicos')}}"/>
    <meta name="description" content="{{((isset($metaDescripcion)) ? $metaDescripcion : 'Contrata tu póliza de Seguro de Gastos')}}">
    <meta name="keywords" content="{{((isset($metaKeys)) ? $metaKeys : 'gastos medicos,mapfre,multiva,metlife,plan seguro,polizas,siniestros,hospitales,seguros,aseguradoras')}}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/icon/favicon.ico')}}">

    <!-- this styles only adds some repairs on idevices  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Google fonts - witch you want to use - (rest you can just remove) -->

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- ######### CSS STYLES ######### -->




<!-- responsive devices styles -->


    {{HTML::script('assets/js/universal/jquery.js')}}
    {{HTML::script('assets/js/jquery.validate.min.js')}}

    <script type="text/javascript">
        var _root_ = '{{URL::to('/')}}/';
    </script>
</head>
<body>

