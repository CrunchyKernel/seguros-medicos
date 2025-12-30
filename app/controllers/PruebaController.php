<?php

class PruebaController extends BaseController {
	protected $layout = 'layout.master';
	
	public function prueba()
	{
		echo "<pre>";
		//print_r(existeCorreo('123@protectodiez.mx'));
	    //echo "<hr>";
	    //print_r(existeCorreo('javiers@protectodiez.mx'));
	    //print_r(existeCorreo('yashiroiori@yahoo.com'));
	    //echo "<hr>";
	    //print_r(existeCorreo('li_s.javier@hotmail.com'));
	    print_r(verifyEmail('javiers@segurosautos.com.mx', 'javiers@segurosautos.com.mx', true));
	    //print_r(existeCorreo('yashiroiori@gmail.com'));
	    //$existeCorreo = existeCorreo('javiers@protectodiez.mx');
	    //echo "<pre>";
	    //print_r($existeCorreo);
	    exit;
	}

}

function verifyEmail($toemail, $fromemail, $getdetails = false){
	$details = '';
	$email_arr = explode("@", $toemail);
	$domain = array_slice($email_arr, -1);
	$domain = $domain[0];
	// Trim [ and ] from beginning and end of domain string, respectively
	$domain = ltrim($domain, "[");
	$domain = rtrim($domain, "]");
	if( "IPv6:" == substr($domain, 0, strlen("IPv6:")) ) {
		$domain = substr($domain, strlen("IPv6") + 1);
	}
	$mxhosts = array();
	if( filter_var($domain, FILTER_VALIDATE_IP) )
		$mx_ip = $domain;
	else
		getmxrr($domain, $mxhosts, $mxweight);
	if(!empty($mxhosts) )
		$mx_ip = $mxhosts[array_search(min($mxweight), $mxhosts)];
	else {
		if( filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
			$record_a = dns_get_record($domain, DNS_A);
		}
		elseif( filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
			$record_a = dns_get_record($domain, DNS_AAAA);
		}
		if( !empty($record_a) )
			$mx_ip = $record_a[0]['ip'];
		else {
			$result   = "invalid";
			$details .= "No suitable MX records found.";
			return ( (true == $getdetails) ? array($result, $details) : $result );
		}
	}
	
	$connect = @fsockopen($mx_ip, 25); 
	if($connect){ 
		if(preg_match("/^220/i", $out = fgets($connect, 1024))){
			fputs ($connect , "HELO $mx_ip\r\n"); 
			$out = fgets ($connect, 1024);
			$details .= $out."\n";
 
			fputs ($connect , "MAIL FROM: <$fromemail>\r\n"); 
			$from = fgets ($connect, 1024); 
			$details .= $from."\n";
			fputs ($connect , "RCPT TO: <$toemail>\r\n"); 
			$to = fgets ($connect, 1024);
			$details .= $to."\n";
			fputs ($connect , "QUIT"); 
			fclose($connect);
			if(!preg_match("/^250/i", $from) || !preg_match("/^250/i", $to)){
				$result = "invalid"; 
			}
			else{
				$result = "valid";
			}
		} 
	}
	else{
		$result = "invalid";
		$details .= "Could not connect to server";
	}
	if($getdetails){
		return array($result, $details);
	}
	else{
		return $result;
	}
}

function existeCorreo($email){
    global $HTTP_HOST;
    $resultado = array();  
    //if (!eregi("^[_\.0-9a-z\-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6}$",$email)) {  
    if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
        $resultadoado[0]=false;  
        $resultado['code']="702";  
        return $resultado;  
    }  
    list ( $Username, $dominio ) = explode("@",$email);  
    if(getmxrr($dominio, $MXHost)){
        $conecta_dominio = $MXHost[0];
    }else{
        $conecta_dominio = $dominio;
    }
    $conectar = fsockopen($conecta_dominio, 25);  
    if ($conectar){
        //if (ereg("^220", $ver = fgets($conectar, 1024))) {  
        if(preg_match("/^220/", $ver = fgets($conectar, 1024))) {
            fputs($conectar, "HELO $HTTP_HOST\r\n");
            $ver = fgets( $conectar, 1024 );
            fputs($conectar, "MAIL FROM: <{$email}>\r\n");  
            $From = fgets( $conectar, 1024 );  
            fputs($conectar, "RCPT TO: <{$email}>\r\n");  
            $To = fgets($conectar, 1024);  
            fputs($conectar, "QUIT\r\n");  
            fclose($conectar);  
            //if (!ereg ("^250", $From) || !ereg ( "^250", $To )) {  
            print_r($From);
            echo "<hr>";
            print_r($To);
            exit;
            if(!preg_match("/^250/", $From) || !preg_match("/^250/", $To) ) {
                $resultado['existe']=false;  
                $resultado['code']="700";  
                return $resultado;  
            }
        }else{  
            $resultado['existe'] = false;  
            $resultado['code'] = "Død";  
            return $resultado;  
        }
    }else{  
        $resultado['existe']=false;  
        $resultado['code']="701";  
        return $resultado;  
    }  
    $resultado['existe']=true;  
    $resultado['code']="200";  
    return $resultado;  
}