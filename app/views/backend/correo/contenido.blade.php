
@section('contenido')
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

    <div class="container align-center">
        <br>
        <div class="row">
            <div class="col-lg-4">
            </div>
            <div class="col-lg-4">
                <h2>
                    Vista previa:
                </h2>
            </div>
            <div class="col-lg-4">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-11">
                <iframe id="vistaPrevia" name="vistaPrevia" src="https://www.segurodegastosmedicosmayores.mx/Correo/previsualizar/{{$idDominio}}" height="500px" width="100%"></iframe>
            </div>
            <div class="col-lg-1">
            </div>
        </div>
    </div>
    <br>
    <div class="panel-group" id="accordion">
        <div class="panel panel-info-alt">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                        Encabezado:
                    </a>
                </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse in">
                <div class="panel-body">
                    <form>
                        <textarea id="textoEncabezado" name="textoEncabezado" rows="20" cols="10">
                                {{$encabezado}}
                        </textarea>
                        <br>
                    </form>
                    <button class="btn btn-info" id="guardaEncabezado" >Guardar</button>
                </div>
            </div>
        </div>
        <div class="panel panel-success-alt">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                        Cuerpo:
                    </a>
                </h4>
            </div>
            <div id="collapse2" class="panel-collapse collapse">
                <div class="panel-body">
                    <form>
                        <textarea id="textoCuerpo" name="textoCuerpo" rows="20" cols="10">
                            {{$cuerpo}}
                        </textarea>
                        <br>
                    </form>
                    <button class="btn btn-success" id="guardaCuerpo" >Guardar</button>
                </div>
            </div>
        </div>
    	<div class="panel panel-warning-alt">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                        Pie:
                    </a>
                </h4>
            </div>
            <div id="collapse3" class="panel-collapse collapse">
                <div class="panel-body">
                    <form>
                        <textarea id="textoPie" name="textoPie" rows="20" cols="10">
                            {{$pie}}
                        </textarea>
                            <br>
                    </form>
                    <br>
                    <button class="btn btn-warning" id="guardaPie" >Guardar</button>
                </div>
            </div>
        </div>
    </div>
@stop
