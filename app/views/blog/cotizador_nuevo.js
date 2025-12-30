$("#nombre").on("change", function(){
	$("#nombres_1").val($("#nombre").val());
});
$("#nombres_1").on("change", function(){
	$("#nombre").val($("#nombres_1").val());
});
$("body").on("click", function(e){
	if($("#miembros").parent().hasClass("show")){
		if(!$("#miembros").is(e.target)
			&& $("#miembros").has(e.target).length === 0
			&& $(".show").has(e.target).length === 0
		){
			$("#miembros").parent().removeClass("show");
			var div = $("#miembros").next();
			$(div).removeClass("show miembros-open");
			$(div).removeAttr("x-placement");
		}
	}
});
$("#miembros").on("click", function(){
	$(this).parent().toggleClass("show");
	var div = $(this).next();
	$(div).toggleClass("show miembros-open");
	if($(div).hasClass("show"))
		$(div).attr("x-placement", "bottom_start");
	else
		$(div).removeAttr("x-placement");
});
var adultos = 1;
var menores = 0;
var c = 0;
function addMiembro(tipo){
	var _class, _nombre, _min, _max, _last, _opts;
	var div = '';
	_last = $("div.adulto").last();
	c++;
	if(tipo=="A"){
		_class = 'adulto';
		_nombre = 'Adulto';
		_opts = ['', 'Conyugue', 'Hijo(a)'];
		_min = 18;
		_max = 69;
	}
	else{
		_class = 'menor';
		_nombre = 'Menor';
		_opts = ['Hijo(a)'];
		_min = 0;
		_max = 17;
		if($("div.menor").last().length>0)
			_last = $("div.menor").last();
	}
	div += '<div class="form-row mb-2 ' + _class + '">';
		div += '<div class="col-md-2">';
			div += '<label for="titulos_' + c + '" class="form-label d-none">Titulo:</label>';
			div += '<select id="titulos_' + c + '" name="titulos[]" class="form-control" required>';
			for(x=0;x<_opts.length;x++)
				div += '<option value="' + _opts[x] + '">' + _opts[x] + '</option>';
			div += '</select>';
		div += '</div>';
		div +='<div class="col-md-6">';
			div += '<label for="nombres_' + c + '" class="form-label d-none">Nombre completo:</label>';
			div +='<input type="text" id="nombres_' + c + '" name="nombres[]" class="form-control" required>';
		div +='</div>';
		div +='<div class="col-md-2">';
			div += '<label for="sexos_' + c + '" class="form-label d-none">Sexo:</label>';
			div +='<select id="sexos_' + c + '" name="sexos[]" class="form-control" required>';
				div +='<option value="">Seleccionar...</option>';
				div +='<option value="m">Hombre</option>';
				div +='<option value="f">Mujer</option>';
			div +='</select>';
		div +='</div>';
		div +='<div class="col-md-2">';
			div += '<label for="edades_' + c + '" class="form-label d-none">Edad:</label>';
			div +='<input type="number" id="edades_' + c + '" name="edades[]" class="form-control" min="' + _min + '" max="' + _max + '" required>';
		div +='</div>';
	div +='</div>';
	
	$(div).insertAfter(_last);
}
$(".btn-miembros").on("click", function(e){
	e.preventDefault();
	if($(this).hasClass("adultos")){
		if($(this).hasClass("minus")){
			if(adultos>1){
				adultos--;
				$("tr.adulto").last().remove();
			}
		}
		else{
			adultos++;
			addMiembro('A');
		}
	}
	else{
		if($(this).hasClass("minus")){
			if(menores>0){
				menores--;
				$("tr.menor").last().remove();
			}
		}
		else{
			menores++;
			addMiembro('M');
		}
	}
	$("#total").val((adultos + menores));
	$("#adultos").val(adultos);
	$("#menores").val(menores);
	var t = adultos + ' Adulto(s)';
	if(menores>0)
		t += ', ' + menores + ' Menor(es)';
	$("#miembros").html(t);
});

var $window = $( window ); // 1. Window Object.
var $featuredMedia = $( "#featured-media" ); // 1. The Video Container.
var $featuredVideo = $( "#featured-video" ); // 2. The Youtube Video.

var player; // 3. Youtube player object.
var _top = $featuredMedia.offset().top; // 4. The video position from the top of the document;
var offset = Math.floor( _top + ( $featuredMedia.outerHeight() / 2 ) ); //5. offset.

window.onYouTubeIframeAPIReady = function() {
	player = new YT.Player( "featured-video", {
	   events: {
	     "onStateChange": onPlayerStateChange,
	     "onReady": onPlayerReady
	   }
	} );
};

function onPlayerStateChange( event ) {

   var isPlay  = 1 === event.data;
   var isPause = 2 === event.data;
   var isEnd   = 0 === event.data;

   if ( isPlay ) {
      $featuredVideo.removeClass( "is-paused" );
      $featuredVideo.toggleClass( "is-playing" );
   }

   if ( isPause ) {
      $featuredVideo.removeClass( "is-playing" );
      $featuredVideo.toggleClass( "is-paused" );
   }

   if ( isEnd ) {
      $featuredVideo.removeClass( "is-playing", "is-paused" );
   }
}

function onPlayerReady(event){
	event.target.playVideo();
}

$window
.on( "resize", function() {
   _top = $featuredMedia.offset().top;
   offset = Math.floor( _top + ( $featuredMedia.outerHeight() / 2 ) );
} )
.on( "scroll", function() {
	console.log('Esta scrolleando: ' + $window.scrollTop());
   $featuredVideo.toggleClass( "is-sticky",
     $window.scrollTop() > offset && $featuredVideo.hasClass( "is-playing" )
   );
} );