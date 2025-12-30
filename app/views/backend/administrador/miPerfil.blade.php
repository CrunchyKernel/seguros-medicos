
@section('contenido')

<div class="contentpanel">
    <div class="row">
        <div class="col-sm-4 col-md-3">
            <div class="text-center">
                <img src="{{asset('/backend/images/logo-gm.png')}}" class="img-circle img-offline img-responsive img-profile" alt="">
                <h4 class="profile-name mb5">{{Auth::user()->nombre}} {{Auth::user()->apellido_paterno}}</h4>
                <div><i class="fa fa-envelope"></i> {{Auth::user()->e_mail}}</div>
                <div class="mb20"></div>
            </div>
        </div>
        <div class="col-sm-8 col-md-9">
            <ul class="nav nav-tabs nav-line">
                <li class="active"><a href="#password" data-toggle="tab"><strong>Contraseña</strong></a></li>
            </ul>
            <div class="tab-content nopadding noborder">
                <div class="tab-pane active" id="password">
                	<form id="actualizarPasswordForm">
	                    <div class="row">
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Contraseña actual</label>
	                                <input type="password" id="passwordActual" name="passwordActual" class="form-control" placeholder="Contraseña actual">
	                            </div>
	                        </div>
	                        <div class="col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label">Contraseña nueva</label>
	                                <input type="password" id="passwordNuevo" name="passwordNuevo" class="form-control" placeholder="Contraseña nueva">
	                            </div>
	                        </div>
	                        <div class="col-sm-4"></div>
	                    </div>
	                    <div class="row">
	                    	<div class="col-sm-4">
	                    		<button type="submit" class="btn btn-primary actualizarAdministradorPassword" data-loading-text="Processando <img src='{{asset('/backend/images/loaders/loader31.gif')}}'>">Actualizar</button>
	                    	</div>
	                    </div>
                	</form>
                </div>
            </div>
            <br>
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
    </div>
</div>

@stop
