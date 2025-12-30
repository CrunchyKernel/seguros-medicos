
@section('contenido')

<div class="mb20"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Deducibles</h4>
                    <p>Tabla de precios para los deducibles de paquetes</p>
                </div>
                <div class="panel-body" style="padding: 0px;">
                	<br>
                	<div class="row">
						<div class="col-md-12">
							<div class="col-md-4">
								<select id="aseguradora" class="select2" style="width: 250px;">
	                                <option value="-1">Seleccione una aseguradora...</option>
									<?php
										if(isset($aseguradoras) && count($aseguradoras) > 0){
											foreach($aseguradoras AS $aseguradora){
												echo '<option value="'.$aseguradora->aseguradora.'">'.$aseguradora->nombre.'</option>';
											}
										}
									?>
								</select>
							</div>
							<div class="col-md-4">
								<select id="paquete" class="select2" style="width: 250px;">
					                <option value="-1">Seleccione un paquete...</option>
								</select>
							</div>
							<div class="col-md-4"></div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-3">
								<p style="padding-left: 100px;">SADA</p>
								<div class="scrollable"  style="height: 480px; overflow: hidden !important;" data-height="480">
									<div id="deducibleSADADiv"></div>
								</div>
							</div>
							<div class="col-md-3">
								<p style="padding-left: 100px;">SADB</p>
								<div class="scrollable"  style="height: 480px; overflow: hidden !important;" data-height="480">
									<div id="deducibleSADBDiv"></div>
								</div>
							</div>
							<div class="col-md-3">
								<p style="padding-left: 100px;">SBDA</p>
								<div class="scrollable"  style="height: 480px; overflow: hidden !important;" data-height="480">
									<div id="deducibleSBDADiv"></div>
								</div>
							</div>
							<div class="col-md-3">
								<p style="padding-left: 100px;">SBDB</p>
								<div class="scrollable"  style="height: 480px; overflow: hidden !important;" data-height="480">
									<div id="deducibleSBDBDiv"></div>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-8">
							<button type="button" id="guardarDeduciblesBtn" class="btn btn-success btn-block" data-aseguradora="-1" data-paquete="-1"><i class="fa fa-save"></i> Guardar tarifas</button>
						</div>
						<div class="col-md-2"></div>
					</div>
					<br>
                </div>
            </div>
        </div>
    </div>
</div>
	
@stop