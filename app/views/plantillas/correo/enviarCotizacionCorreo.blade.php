<?php
	// URL de la cotización en línea: se usa el dominio de la cotización cuando está disponible
	$dominio = null;
	$urlCotizacion = URL::to('verCotizacion/' . $id_cotizacion . '/' . $secret);
	if (isset($cotizacionDatos) && is_object($cotizacionDatos)) {
		$dominio = $cotizacionDatos->dominio()->first();
		if ($dominio) {
			$rutaCotizacion = ($cotizacionDatos->cotizar_para > 0) ? $dominio->ver_cotizacion_nuevo : $dominio->ver_cotizacion;
			$urlCotizacion = $dominio->dominio . $rutaCotizacion . '/' . $id_cotizacion . '/' . $secret;
		}
	}
	$nombreSitio = ($dominio && $dominio->nombre) ? $dominio->nombre : 'Seguro de Gastos Médicos Mayores';
	$nombreCliente = isset($nombre) ? ucwords(strtolower($nombre)) : '';
	$hayMensaje = isset($mensaje) && trim($mensaje) != '';
?>
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="format-detection" content="telephone=no" />
	<title>{{$nombreCliente}} - Cotizaci&oacute;n - {{$nombreSitio}}</title>
	<!--[if mso]>
	<xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
	<![endif]-->
	<style type="text/css">
		body, table, td, p, a, li { -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; }
		table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
		img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
		body, #bodyTable { margin: 0; padding: 0; width: 100% !important; height: 100% !important; }
		body { background-color: #eaf5f8; }
		a { color: #4192AD; }
		#outlook a { padding: 0; }
		.ExternalClass { width: 100%; }
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
		a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }
		td, p, a, li, ul, ol, span, div, strong, b, em, i, h1, h2, h3, h4, h5, h6, blockquote { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif !important; }
		.email-card { max-width: 600px !important; }

		@media only screen and (max-width: 600px) {
			.email-card { width: 100% !important; max-width: 100% !important; }
			.px { padding-left: 24px !important; padding-right: 24px !important; }
			.h1 { font-size: 24px !important; }
			.p { font-size: 16px !important; }
			.btn-a { display: block !important; }
		}
	</style>
</head>

<body style="margin: 0; padding: 0; background-color: #eaf5f8; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
	<!-- Texto de vista previa (no se muestra en el correo) -->
	<div style="display: none; font-size: 1px; color: #eaf5f8; line-height: 1px; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all;">
		Tu cotizaci&oacute;n de Gastos M&eacute;dicos Mayores est&aacute; lista. La puedes ver en l&iacute;nea o en el PDF adjunto.
	</div>

	@if($hayMensaje)
	<!-- MENSAJE PERSONALIZADO (sustituye al encabezado) -->
	<center>
		<table id="bodyTable" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
			style="width: 100%; background-color: transparent;">
			<tbody>
				<tr>
					<td align="center" valign="top" style="padding: 28px 12px 0;">
						<!--[if (gte mso 9)|(IE)]><table border="0" cellpadding="0" cellspacing="0" width="600" style="width:600px"><tr><td valign="top" width="600" style="width:600px;"><![endif]-->
						<table class="email-card" role="presentation" border="0" cellpadding="0" cellspacing="0" width="600"
							style="width: 600px; max-width: 600px; background-color: #ffffff; border-radius: 24px 24px 0 0; overflow: hidden;">
							<tbody>
								<tr>
									<td align="center" valign="top" bgcolor="#cdeef4"
										style="background-color: #cdeef4; background-image: linear-gradient(180deg, #ffffff 0%, #c6e9f0 100%); padding: 38px 24px 30px; border-radius: 24px 24px 0 0;">
										<img alt="{{$nombreSitio}}" border="0" width="240"
											src="{{asset('/protectodiez/sgmmPNG/gastosmedicosmayores180.png')}}"
											style="display: block; max-width: 240px; width: 70%; height: auto;" />
									</td>
								</tr>
								<tr>
									<td class="px p" valign="top"
										style="padding: 34px 40px 24px; font-size: 16px; line-height: 1.7; color: #555555;">
										{{$mensaje}}
									</td>
								</tr>
							</tbody>
						</table>
						<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
					</td>
				</tr>
			</tbody>
		</table>
	</center>
	@else
	<!-- ENCABEZADO (parte superior de la tarjeta) -->
	{{$encabezado}}
	@endif

	<!-- BOT&Oacute;N: COTIZACI&Oacute;N EN L&Iacute;NEA -->
	<center>
		<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
			style="width: 100%; background-color: transparent;">
			<tbody>
				<tr>
					<td align="center" valign="top" style="padding: 0 12px;">
						<!--[if (gte mso 9)|(IE)]><table border="0" cellpadding="0" cellspacing="0" width="600" style="width:600px"><tr><td valign="top" width="600" style="width:600px;"><![endif]-->
						<table class="email-card" role="presentation" border="0" cellpadding="0" cellspacing="0" width="600"
							style="width: 600px; max-width: 600px; background-color: #ffffff;">
							<tbody>
								<tr>
									<td class="px" align="center" valign="top" style="padding: 6px 40px 30px;">
										<table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto;">
											<tbody>
												<tr>
													<td align="center">
														<a class="btn-a" href="{{$urlCotizacion}}" target="_blank"
															style="display: inline-block; padding: 15px 34px; font-size: 17px; font-weight: 400; line-height: 1.2; color: #ffffff; text-decoration: none; border-radius: 100px; background-color: #4192AD;">Cotizaci&oacute;n en l&iacute;nea</a>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
						<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
					</td>
				</tr>
			</tbody>
		</table>
	</center>

	<!-- CUERPO (parte inferior de la tarjeta) -->
	{{$cuerpo}}

	@if(isset($signature) && trim($signature) != '')
	<!-- FIRMA -->
	<center>
		<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
			style="width: 100%; background-color: transparent;">
			<tbody>
				<tr>
					<td align="center" valign="top" style="padding: 0 12px 18px;">
						<!--[if (gte mso 9)|(IE)]><table border="0" cellpadding="0" cellspacing="0" width="600" style="width:600px"><tr><td valign="top" width="600" style="width:600px;"><![endif]-->
						<table class="email-card" role="presentation" border="0" cellpadding="0" cellspacing="0" width="600"
							style="width: 600px; max-width: 600px;">
							<tbody>
								<tr>
									<td class="px" align="center" valign="top" style="padding: 0 40px;">{{$signature}}</td>
								</tr>
							</tbody>
						</table>
						<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
					</td>
				</tr>
			</tbody>
		</table>
	</center>
	@endif

	<!-- PIE -->
	<center>
		<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
			style="width: 100%; background-color: transparent;">
			<tbody>
				<tr>
					<td align="center" valign="top" style="padding: 0 12px 32px;">
						<!--[if (gte mso 9)|(IE)]><table border="0" cellpadding="0" cellspacing="0" width="600" style="width:600px"><tr><td valign="top" width="600" style="width:600px;"><![endif]-->
						<table class="email-card" role="presentation" border="0" cellpadding="0" cellspacing="0" width="600"
							style="width: 600px; max-width: 600px;">
							<tbody>
								@if(isset($pie) && trim($pie) != '')
								<tr>
									<td class="px" valign="top"
										style="padding: 8px 40px 0; font-size: 13px; line-height: 1.7; color: #7a8a8f;">
										{{$pie}}
									</td>
								</tr>
								@endif
								<tr>
									<td class="px" align="center" valign="top"
										style="padding: 14px 40px 0; font-size: 11px; line-height: 1.6; color: #9aa6aa;">
										&copy; 2005 - {{date('Y')}} {{$nombreSitio}}. Todos los derechos reservados.
									</td>
								</tr>
							</tbody>
						</table>
						<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
					</td>
				</tr>
			</tbody>
		</table>
	</center>
</body>

</html>
