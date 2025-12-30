<?php
class Whatsapp{
	const TOKEN = "EAAxt5HxflOYBO7HMHgEdSZCDN7ReKmRZCVhZA7UZANUnWf2hGxZCvHxr2bmDu8nXZAmKKu7cHlZCtb70suwJ0WRWFppNdyNBiMolQYC9F7Gh1ksbgZCQUsNIvd32ZB7qQHk4v3xH4RqHdiLOFTwu1IwipmAnDjSh6KePjake2wesPtjp286QmD5zwRmxwA7i5VmVs1AZDZD";
	
	public static function sendTemplate1($cotizacionDatos){
		$template = "protecto_mensaje_1";
		switch($cotizacionDatos->cotizar_para){
			case 1:
				$tipo = "1 persona";
				break;
			case 2:
				$tipo = "tu pareja y tu";
				break;
			case 3:
				$tipo = "tu pareja, tu y tus hijos";
				break;
			case 4:
				$tipo = "tu pareja e hijos";
				break;
			case 5:
				$tipo = "tu y tus hijos";
				break;
		}
		$url = "https://www.segurodegastosmedicosmayores.mx/cotizacion-nuevo/" . $cotizacionDatos->id_cotizacion . "/" . $cotizacionDatos->secret;
		$jsonPars = '{"type":"text", "parameter_name":"nombre", "text":"' . $cotizacionDatos->nombre . '"}';
		$jsonPars .= ', {"type":"text", "parameter_name":"tipo", "text":"' . $tipo . '"}';
		$jsonPars .= ', {"type":"text", "parameter_name":"url", "text":"' . $url . '"}';
		
		$json = '{
			      "messaging_product": "whatsapp",
			      "recipient_type": "individual",
			      "to": "52' . $cotizacionDatos->telefono . '",
			      "type": "template",
			      "template": {
			      	"name": "' . $template . '",
			      	"language": {"code": "es_MX"},
			      	"components": [
			      	  {
			      	  	"type": "body",
			      	  	"parameters": [
			      	  	  ' . $jsonPars . '
			      	  	]
			      	  }
			      	]
			      }
			    }';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v15.0/112834604817687/messages");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Authorization: Bearer " . self::TOKEN,
			"Content-Type: application/json"
		));
		$res = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
		return $err;
	}
}
?>