@extends('layout.master')
@section('contenido')
	@include('layout.slider')
	@if(isset($contenido) && count($contenido) > 0)
		@foreach($contenido AS $html)
			{{$html}}
		@endforeach
	@endif
@stop
