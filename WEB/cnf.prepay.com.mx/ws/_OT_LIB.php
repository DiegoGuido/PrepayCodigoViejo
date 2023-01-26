<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function sendEmail($data){
	$e=$data["email"];
	$s=$data["subject"]; 
	$b=$data["body"]; 
	$imgpath = isset($data["imagepath"])?$data["imagepath"]:"";
	$urlfile = isset($data["urlfile"])?$data["urlfile"]:"";
	$enable = true;
	if($enable){
		//$e.=(empty($e)?"":",")."siamonitoralertas@gmail.com";
		if(!empty($e)){
			if (!class_exists("phpmailer")) {
				require_once("ammenities/PHPMailer-FE_v4.11/_lib/class.phpmailer.php");
			}
			$b = getFooter($b);
			$u = base64_decode("");  
			$mail = new PHPMailer();
			$mail->From     = $u; //Direcci�n desde la que se enviar�n los mensajes. Debe ser la misma de los datos de el servidor SMTP.
			$mail->FromName = "Sistema OCTECH"; 
			$mails = explode(",", $e);
			foreach ($mails as $m){
                            if(!empty($m) && isEmail($m))
                                $mail->AddAddress($m);
			}
			//$mail->AddAddress("david.vargas85@gmail.com");
			$mail->WordWrap = 50; 
			$mail->IsHTML(true); 
			if(!empty($imgpath))
				$mail->AddEmbeddedImage($imgpath, 'emb_png');  
                        $logo = "img/bus-icon.png";
                        while(!is_file($logo)){
                            $logo = "../".$logo;
                        }
			$mail->AddEmbeddedImage($logo, 'logo_png');  
			$mail->Subject  =  $s;
			$mail->Body     =  $b;
			$mail->IsSMTP(); 
			$mail->SMTPSecure = "ssl"; 
			$mail->Host = base64_decode("");  // Servidor de Salida.
			$mail->Port = base64_decode("");  // Servidor de Salida.
			$mail->SMTPAuth = true; 
			$mail->Username = $u; 
			$mail->Password = base64_decode(""); //_U}v7@)EaF)L
			$mail->CharSet = 'UTF-8';
			$mail->Timeout = 60;
			if(!empty($urlfile)){
				$fileName = explode("/", $urlfile)[count(explode("/", $urlfile))-1];
				$mail->addAttachment($urlfile,$fileName);
			}
			try{
				if($mail->Send()){
					return true;
				}
				else{
					return false;
				}
			}catch(Exception $ex){
				 writeLog("wrong_emails.txt"," Problemas enviando a alguno de estos correos: $e");
			}
		}
	}else{
		return false;
	}
}
function getFooter($b){
	$b .= "<br /><p /><img src=\"cid:logo_png\" /><br />
			<b>Sistema de Transporte.</b><br />
			<p />🌎 <a href='http://coproit.com' target='_blank'>coproit.com</a>";
    return $b;
}
function isEmail($email){
    $isOk = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	if ($isOk)
       return true;
    else{
        writeLog("wrong_emails.txt"," Problemas: $email");
        return false; 
	}
}
function writeLog($file2log,$line){
    $file = fopen($file2log, "a");
    fwrite($file, date("Y-m-d H:i:s")." ".$line);
    fwrite($file, PHP_EOL);
    fclose($file);
}

function formatDate($date2f){   
    $date = DateTime::createFromFormat('d/m/Y', $date2f);
    return  $date->format('Y-m-d');
}
//include("_OT_LIB.php");
//$data= array();
//$data["email"]="david.vargas85@gmail.com,begolnx@gmail.com";
//$data["subject"]="Coordenadas no validas"; 
//$data["body"]  ="Por medio del presente se le notifica que el usuario <b>".$auData["usuario"]."</b> se  ha firmado desde el dispositivo <b>".$auData["imei"]."</b>";
//$data["body"] .="y las coordenadas enviadas no son válidas<br /><font color=red><b>".$auData["location"]."</b></font>."; 
//sendEmail($data);