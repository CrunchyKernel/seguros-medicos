<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Seguro de Gastos médicos">
    <meta name="author" content="li_s.javier@hotmail.com">
    
    <link rel="shortcut icon" href="{{asset('assets/images/icon/favicon.ico')}}">

    <title>Seguro de Gastos Médicos</title>

    {{HTML::style('backend/css/style.default.css')}}
    {{HTML::style('backend/css/custom.css')}}
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->

    <script type="text/javascript">
        var _root_ = '{{URL::to('/admingm/')}}/';
    </script>
    @if(isset($variablex))
        <script src="//cdn.ckeditor.com/4.5.9/standard/ckeditor.js"></script>
    @endif
	
</head>

<body>
