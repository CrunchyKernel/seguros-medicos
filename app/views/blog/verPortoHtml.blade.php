@extends('layout.porto')

@section('contenido')
	<!--div class="page_title">
		<div class="container">
			<div class="title"><h1>{{--$contenido->titulo--}}</h1></div>
			<!--<div class="pagenation">&nbsp;<a href="index.html">Inicio</a> <i>/</i> <a href="#">Blog</a> <i>/</i> Small Image</div>>
			<div class="pagenation">{{--$rastroMigajasTexto--}}</div>
		</div>
	</div-->
	<div class="container">
		<div class="content_left">
			@if(isset($contenido->html) && count($contenido->html) > 0)
				@foreach($contenido->html AS $html)
					{{$html}}
				@endforeach
			@endif
		</div>
		
	</div>
@stop