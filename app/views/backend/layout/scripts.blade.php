{{HTML::script('backend/js/jquery-1.11.1.min.js')}}
{{HTML::script('backend/js/jquery-migrate-1.2.1.min.js')}}
{{HTML::script('backend/js/jquery-ui-1.10.3.min.js')}}
{{HTML::script('backend/js/bootstrap.min.js')}}
{{HTML::script('backend/js/modernizr.min.js')}}
{{HTML::script('backend/js/pace.min.js')}}
{{HTML::script('backend/js/retina.min.js')}}
{{HTML::script('backend/js/jquery.cookies.js')}}

{{HTML::style('backend/js/sweetalert/sweetalert.css')}}
{{HTML::script('backend/js/sweetalert/sweetalert.min.js')}}

{{HTML::script('backend/js/pnotify/pnotify.core.js')}}
{{HTML::style('backend/js/pnotify/pnotify.core.css')}}
{{HTML::script('backend/js/pnotify/pnotify.buttons.js')}}
{{HTML::style('backend/js/pnotify/pnotify.buttons.css')}}
{{HTML::script('backend/js/pnotify/pnotify.confirm.js')}}
{{HTML::script('backend/js/pnotify/pnotify.nonblock.js')}}
{{HTML::script('backend/js/pnotify/pnotify.desktop.js')}}
{{HTML::script('backend/js/pnotify/pnotify.history.js')}}
{{HTML::style('backend/js/pnotify/pnotify.history.css')}}
{{HTML::script('backend/js/pnotify/pnotify.callbacks.js')}}
{{HTML::script('backend/js/pnotify/pnotify.reference.js')}}

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

{{HTML::script('backend/js/custom.js')}}
{{HTML::script('backend/js/helpers/adminsis.js?20230522')}}

@if(isset($scripts))
	@foreach($scripts AS $script)
    	@if($script["tipo"] == 'css')
	        {{ HTML::style($script["archivo"].'.css') }}
    	@endif
    	@if($script["tipo"] == 'js')
    		{{ HTML::script($script["archivo"].'.js') }}
    	@endif
    @endforeach
@endif
