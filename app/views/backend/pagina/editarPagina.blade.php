@extends('backend.layout.masterPaginaEditar')

@section('contenido')
	<input type="hidden" id="idPagina" value="{{$paginaDatos->id_blog}}">
@stop