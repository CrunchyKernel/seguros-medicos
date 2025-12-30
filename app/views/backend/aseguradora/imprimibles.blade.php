@section('contenido')
<div>
	</div>

<div class="row">
	<div class="col-sm-12">
		<div class="form-group">
			<label class="control-label">Dominio</label>
			<select id="id_dominio" style="width: 100%;">
                @if(isset($dominios) && $dominios->count() > 0)
                    @foreach($dominios AS $dominio)
                        <option value="{{$dominio->id_dominio}}">{{$dominio->nombre}}</option>
                    @endforeach
                @endif
            </select>
		</div>
	</div>
</div>

<div class="panel-group" id="accordion">
    <div class="panel panel-info-alt">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Beneficios ProtectoDiez</a>
            </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse in">
            <div class="panel-body">
                <form>
                    <textarea id="textoProtecto" name="textoProtecto" rows="20" cols="10">
                        {{$textoProtecto->texto_pdf}}
                    </textarea>
                    <br>
                </form>
                <button class="btn btn-info" id="guardaTP" >Guardar</button>
            </div>
        </div>
    </div>
    <div class="panel panel-success-alt">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Texto saludo-bienvenida</a>
            </h4>
        </div>
        <div id="collapse2" class="panel-collapse collapse">
            <div class="panel-body">
                <form>
                    <textarea id="textoSaludo" name="textoSaludo" rows="20" cols="10">
                        {{$textoSaludo->texto_pdf}}
                    </textarea>
                    <br>
                </form>
                <button class="btn btn-success" id="guardaSB" >Guardar</button>
            </div>
        </div>
    </div>
    <div class="panel panel-warning-alt">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Contenido de correo</a>
            </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse">
            <div class="panel-body">
                <form>
                    <textarea id="textoMail" name="textoMail" rows="20" cols="10">
                        Texto de correo
                    </textarea>
                    <br>
                </form>
                <br>
                <button class="btn btn-warning" id="guardaCorreo" >Guardar</button>
            </div>
        </div>
    </div>
     <div class="panel panel-info-alt">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Cotizacion encabezado</a>
            </h4>
        </div>
        <div id="collapse4" class="panel-collapse collapse">
            <div class="panel-body">
                <form>
                    <textarea id="textoCEncabezado" name="textoCEncabezado" rows="20" cols="10">
                         {{$textoCEncabezado->texto_pdf}}
                    </textarea>
                    <br>
                </form>
                <br>
                <button class="btn btn-info" id="guardaCEncabezado" >Guardar</button>
            </div>
        </div>
    </div>
    <div class="panel panel-warning-alt">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse6">Cotizacion abajo de cotizador</a>
            </h4>
        </div>
        <div id="collapse6" class="panel-collapse collapse">
            <div class="panel-body">
                <form>
                    <textarea id="textoCAbajode" name="textoCAbajode" rows="20" cols="10">
                         {{$textoCAbajode}}
                    </textarea>
                    <br>
                </form>
                <br>
                <button class="btn btn-info" id="guardaCAbajode" >Guardar</button>
            </div>
        </div>
    </div>
     <div class="panel panel-success-alt">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">Cotizacion pie de pagina</a>
            </h4>
        </div>
        <div id="collapse5" class="panel-collapse collapse">
            <div class="panel-body">
                <form>
                    <textarea id="textoCPie" name="textoCPie" rows="20" cols="10">
                         {{$textoCPie->texto_pdf}}
                    </textarea>
                    <br>
                </form>
                <br>
                <button class="btn btn-success" id="guardaCPie" >Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- <script>
          CKEDITOR.replace('textoProtecto', {
  		  customConfig: '/backend/js/ckeditor/config.js'
  		});
    </script>!-->

@stop

