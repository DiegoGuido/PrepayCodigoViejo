<?php
/* 
 * **********************************************************************
 * Project           : Prepay web services API
 * Program name      : sample.sas
 * Author            : Joel Becerril
 * Date created      : 20200803
 * Purpose           : Card Validator
 * Module			 : Global
 * Story			 : Validator
 */
if (filter_var(getenv('REQUEST_METHOD')) == 'POST'){
    include "../../_OT_Globals.php";
    $otGlobals = new OTConstants();
    $prefix = $otGlobals->getPrefix();
    include "../".$prefix.$otGlobals::CONNECTION_FILE;
    include $prefix.$otGlobals::DAO_FILE;
    //$input = filter_input_array(INPUT_POST);
    $json = file_get_contents('php://input');
	grabJson($json);
    $input = json_decode($json, true);
    $reply = array();
    $conn=getConnection();
	$today = $currDate = date("Y-m-d H:i:s");	
	if(isset($input["imei"]) && $input["imei"]!= ""){
		//inicia validar dispositivo
		$setupInfo1 = getValidDevice($conn, $input); 
			//var_dump($setupInfo);
			if(!$setupInfo1["error"]){
				$reply["status"] = true;
				$reply["message"] = $setupInfo1["message"];            
			}else{
				//$reply["status"] = false;
				//$reply["message"] = $setupInfo["message"];				
				//}				
				if(isset($input["idCard"])){
					$setupInfo2 = getValidCard($conn, $input); 
					//var_dump($setupInfo);
					if(!$setupInfo2["error"]){
						$reply["status"] = true;
						$reply["message"] = $setupInfo2["message"];            
					}else{
						$reply["status"] = false;
						$reply["message"] = $setupInfo2["message"];
						//$reply["insert"] = $setupInfo2["insert"];
					}			
				}else{ 
				$reply["status"] = true;
				$reply["message"] = "Tarjeta Invalida";				
				}
			}//Termina Valid Device
	}else{
        $reply["status"] = true;
        $reply["message"] = "IMEI Incorrecto o Vacio";
    }
	$reply["dtServer"] = $today;
    //header("HTTP/1.1 200 OK");
    header('Content-type: application/json');
    echo json_encode($reply);
    exit();
}else{
    echo "This service is not browseable. Here you can find proper <a href='prepaydocs.rar' target='_blank'>documentation</a>";
}
function grabJson($txtJson){
	$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_validate.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}