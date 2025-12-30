<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!-- If you delete this tag, the sky will fall on your head -->
        <meta name="viewport" content="width=device-width" />

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!--Seguro de Gastos Médicos Mayores-->
        <title>{{ucwords(strtolower($cotizacionDatos->nombre))}} - Cotizaci&oacute;n - {{$cotizacionDatos->dominio()->first()->nombre}}</title>
        <style type="text/css">
        	body{
        		font-family: 'calibri' !important;
    			font-size: 16px !important;
        	}
        </style>
    </head>
    <body>
    <!--table width="100%">
		<tr>
			<!--//asset('protectodiez/logos/gastosmedicosmayores180.jpg')--
			<td width="25%"><img src="{{asset($cotizacionDatos->dominio()->first()->logo)}}" width="250px"></td>
			<td width="50%"></td>
			<td width="25%"><img src="{{asset('protectodiez/logos/PROTECTODIEZ-LOGO-500-253-.jpg')}}" width="250px"></td>
		</tr>
	</table>
	<br>
	<table width="100%">
		<tr>
			<td width="50%"></td>
			<td width="50%" style="text-align: right;">Guadalajara, Jalisco, {{sistemaFunciones::fechaLetras(date('Y-m-d'))}}</td>
		</tr>
	</table-->
    <div class="container">
    	@if($mensaje=="")
        	{{$encabezado}}
        @else
        	{{$mensaje}}
        @endif
    </div>
    <div class="container">
    	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="componentContainerButton" style="background-color: transparent; min-width: 100%; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    		<tbody>
    			<tr>
    				<td style="padding-top: 9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px; mso-line-height-rule: exactly; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" align="center" class="componentContainerCellButton">
    					<table border="0" cellpadding="0" cellspacing="0" class="componentBlockButton" style="border-collapse: separate !important; border: 2px solid #ff7a00; border-radius: 10px; background-color: #ff7a00; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    						<tbody>
    							<tr>
    								<td align="center" valign="middle" class="componentBlockCellButton" style="font-family: verdana,sans-serif; font-size: 20px; padding: 14px; mso-line-height-rule: exactly; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    									<a class="componentButton" title="" href="{{$cotizacionDatos->dominio()->first()->dominio . (($cotizacionDatos->cotizar_para > 0) ? $cotizacionDatos->dominio()->first()->ver_cotizacion_nuevo : $cotizacionDatos->dominio()->first()->ver_cotizacion) . '/' . $id_cotizacion . '/' . $secret}}" target="_blank" style="letter-spacing: normal; font-style: normal; font-weight: normal; line-height: 100%; text-align: center; text-decoration: none; color: #ffffff; mso-line-height-rule: exactly; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: block; word-break: break-word !important;">
    										Cotizaci&oacute;n en l&iacute;nea
										</a>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
        {{$cuerpo}}
        <!--$signature-->
    </div>
    </body>
</html>