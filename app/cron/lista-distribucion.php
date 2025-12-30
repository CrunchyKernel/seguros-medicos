<?php
$host = "localhost";
$username = "cotizars_sgmm";
$password = "Jw4SzXEp";
$database = "cotizars_sgmm";
$token = "EAAxt5HxflOYBO7HMHgEdSZCDN7ReKmRZCVhZA7UZANUnWf2hGxZCvHxr2bmDu8nXZAmKKu7cHlZCtb70suwJ0WRWFppNdyNBiMolQYC9F7Gh1ksbgZCQUsNIvd32ZB7qQHk4v3xH4RqHdiLOFTwu1IwipmAnDjSh6KePjake2wesPtjp286QmD5zwRmxwA7i5VmVs1AZDZD";
$message = "";

$mysqli = new mysqli($host, $username, $password, $database);
$select = "call sp_sel_cotizaciones_whatsapp ()";

$rows = $mysqli->query($select, MYSQLI_STORE_RESULT);
while($row = $rows->fetch_array(MYSQLI_ASSOC)){
	$jsonPars = '';
	$url = "https://www.segurodegastosmedicosmayores.mx/cotizacion-nuevo/" . $row["id_cotizacion"] . "/" . $row["secret"];
	$lang = '';
	switch($row["plantilla"]){
		case "protecto_mensaje_1":
			$tipo = "";
			switch($row["cotizar_para"]){
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
			$jsonPars = '{"type":"text", "parameter_name":"nombre", "text":"' . $row["nombre_simple"] . '"}';
			$jsonPars .= ', {"type":"text", "parameter_name":"tipo", "text":"' . $tipo . '"}';
			$jsonPars .= ', {"type":"text", "parameter_name":"url", "text":"' . $url . '"}';
			$lang = 'es_MX';
			
			$message = "¿Que te pareció tu cotización?<br><br>Estimado(a) {{nombre}} hace algunos días recibimos una solicitud para generar una cotización de seguro de gastos médicos mayores para {{tipo}}. Queremos saber si tienes alguna necesidad en especial.<br><br>Te dejamos un enlace directo a tu cotización {{url}}";
			break;
		case "protecto_mensaje_2":
			$jsonPars = '{"type":"text", "parameter_name":"nombre", "text":"' . $row["nombre_simple"] . '"}';
			$jsonPars .= ', {"type":"text", "parameter_name":"url", "text":"' . $url . '"}';
			$lang = 'es_MX';
			
			$message = "Estamos para ayudarte<br><br>Hola {{nombre}}, queremos recordarte que estamos para ayudarte a conseguir tus objetivos.<br><br>Te dejamos una liga con el acceso directo a tu cotizacion {{url}}";
			break;
	}
	$idCotizacion = $row["id_cotizacion"];
	$idLista = $row["id_lista_distribucion"];
	$plantilla = $row["plantilla"];
	$json = '{
		      "messaging_product": "whatsapp",
		      "recipient_type": "individual",
		      "to": "52' . $row["telefono"] . '",
		      "type": "template",
		      "template": {
		      	"name": "' . $plantilla . '",
		      	"language": {"code": "' . $lang . '"},
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
	
	while($mysqli->next_result()){
		if($l_result = $mysqli->store_result()){
			$l_result->free();
		}
    }
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v23.0/614394131767163/messages");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: Bearer " . $token,
		"Content-Type: application/json"
	));
	$res = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);
	
	$wamid = '';
	$jsonRes = json_decode($res);
	if(isset($jsonRes->messages[0]->id))
		$wamid = $jsonRes->messages[0]->id;
	
	$select = "call sp_ins_cotizaciones_whatsapp (" . $idCotizacion . ", " . $idLista . ", '" . $plantilla . "', '" . $res . "', '" . $err . "')";
	$mysqli->query($select, MYSQLI_STORE_RESULT);
	
	if($message!=""){
		$message = str_replace("{{nombre}}", $row["nombre_simple"], $message);
		$message = str_replace("{{tipo}}", $tipo, $message);
		$message = str_replace("{{url}}", $url, $message);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://whatsapp.mas-ti.mx/demo/php/register-message.php");
		curl_setopt($ch, CURLOPT_POST, true);
		$data = "name=" . $row["nombre"] . "&phone=" . $row["telefono"] . "&id=2004157567026728&wamid=" . $wamid . "&message=" .$message ;
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    	$res = curl_exec($ch);
	}
}
$rows->free();
$mysqli->close();

/*$types = "";
$parameters = array();
$query = $mysqli->prepare($select);
if($types!="")
	$query->bind_param($types, ...$parameters);
if($query->execute()){
	$result = $query->get_result();
	if($result){
		echo "Si tiene resultados-";
		print_r($result);
		while($row = $result->fetch_assoc()){
			echo "lee resultado-";
			$jsonPars = '';
			$url = "https://www.segurodegastosmedicosmayores.mx/cotizacion-nuevo/" . $row["id_cotizacion"] . "/" . $row["secret"];
			switch($row["plantilla"]){
				case "protecto_mensaje_1":
					echo "tipo de plantilla 1-";
					$tipo = "";
					switch($row["cotizar_para"]){
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
					$jsonPars = '{"type":"text", "parameter_name":"nombre", "text":"' . $row["nombre_simple"] . '"}';
					$jsonPars .= ', {"type":"text", "parameter_name":"tipo", "text":"' . $tipo . '"}';
					$jsonPars .= ', {"type":"text", "parameter_name":"url", "text":"' . $url . '"}';
					break;
				case "protecto_mensaje_2":
					$jsonPars = '{"type":"text", "parameter_name":"nombre", "text":"' . $row["nombre_simple"] . '"}';
					$jsonPars .= ', {"type":"text", "parameter_name":"url", "text":"' . $url . '"}';
					break;
			}
			$json = '{
				      "messaging_product": "whatsapp",
				      "recipient_type": "individual",
				      "to": "52' . $row["telefono"] . '",
				      "type": "template",
				      "template": {
				      	"name": "' . $row["plantilla"] . '",
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
			echo "formo el json-";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v15.0/112834604817687/messages");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: Bearer " . $token,
				"Content-Type: application/json"
			));
			$res = curl_exec($ch);
			$err = curl_error($ch);
			curl_close($ch);
			echo "corrio el curl-";
			
			
			$mysqli = new mysqli($host, $username, $password, $database);
			$select = "call sp_ins_cotizaciones_whatsapp (?, ?, ?, ?, ?)";
			$types = "iisss";
			$parameters = array(
				$row["id_cotizacion"],
				$row["id_lista_distribucion"],
				$row["plantilla"],
				$res,
				$err
			);
			$query = $mysqli->prepare($select);
			if($types!="")
				$query->bind_param($types, ...$parameters);
			$query->execute();
			echo "corrio el insert-";
		}
		$result->close();
	}
	echo "1";
}*/
?>