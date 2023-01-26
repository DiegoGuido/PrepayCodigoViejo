<?php
/* 
 * **********************************************************************
 * Project           : Prepay web services API
 * Program name      : sample.sas
 * Author            : Joel Becerril
 * Date created      : 20210106
 * Purpose           : Concentrado Movimientos
 * Module			 : Global
 * Story			 : concentrated
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
		$setupInfo0 = getValidDevice($conn, $input); 
			//var_dump($setupInfo);
			if(!$setupInfo0["error"]){
				$reply["status"] = true;
				$reply["message"] = $setupInfo0["message"];            
			}else{
				$setupInfo1 = validateConcentrated($conn,$input);
				if($setupInfo1["error"]){
					$reply["status"] = true;
					$reply["message"] = $setupInfo1["message"];
					$reply["folio"] = $setupInfo1["folio"];
				}else{					
					// Inicia Concentrado de Recarga
					if(isset($input["conType"]) && $input["conType"] == "R"){
						if(isset($input["totalRecharge"]) && isset($input["totalSale"]) && isset($input["totalCard"])){
							$setupInfo2 = insertTotalRecharge($conn, $input); 
							//var_dump($setupInfo);
							if($setupInfo2["error"]){
								$reply["status"] = false;
								$reply["message"] = $setupInfo2["message"];            
							}else{
								$reply["status"] = true;
								$reply["message"] = $setupInfo2["message"];
								$reply["dateTimeTransaction"] = $input["dateTimeTransaction"];
								$reply["folio"] = $setupInfo2["folio"];
							}			
						}else{ 
						$reply["status"] = true;
						$reply["message"] = "Datos incompletos o invalidos";				
						}
					} // Termina Concentrado de Recarga
					// Inicia Concentrado de Cobro
					//Datos Provenientes de Validate**
					$jsntrj=$setupInfo1["jsntrj"];
					$cnt=$setupInfo1["cnt"];
					$trjtot=$setupInfo1["trjtot"];
					//echo "idxtot: ".$trjtot;
					if(isset($input["conType"]) && $input["conType"] == "C"){
						//if(isset($input["idUnit"]) && isset($input["totalCharge"]) && isset($input["totalCash"])){
						if(isset($input["idUnit"])){
							$setupInfo3 = insertTotalCharge($conn,$input,$trjtot,$cnt,$jsntrj); 
							//var_dump($setupInfo);
							if($setupInfo3["error"]){
								$reply["status"] = false;
								$reply["message"] = $setupInfo3["message"];           
							}else{
								$reply["status"] = true;
								$reply["message"] = $setupInfo3["message"];
								$reply["dateTimeTransaction"] = $input["dateTimeTransaction"];
								$reply["folio"] = $setupInfo3["folio"];
							}			
						}else{ 
						$reply["status"] = true;
						$reply["message"] = "Datos incompletos o invalidos";				
						}
					} // Termina Concentrado de Cobro
				} //Termina valid concentrated
			}//Termina Valid Device
	}else{
        $reply["status"] = true;
        $reply["message"] = "IMEI Incorrecto o Vacio";
    }
	
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
	$fp = fopen('log_consolidate.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}