<?php
/* 
 * **********************************************************************
 * Project           : Ocetech web services API
 * Program name      : sample.sas
 * Author            : David Vargas
 * Date created      : 20190629
 * Purpose           : Provide customer solution to Ocetech terminals
 *
 */
  header('Content-type: application/json');
if (filter_var(getenv('REQUEST_METHOD')) == 'POST'){
    include "../../_OT_Globals.php";
    $otGlobals = new OTConstants();
    $prefix = $otGlobals->getPrefix();
    include "../".$prefix.$otGlobals::CONNECTION_FILE;
    include $prefix.$otGlobals::DAO_FILE;
    //$input = filter_input_array(INPUT_POST);
    $json = file_get_contents('php://input');
	
    $input = json_decode($json, true);
    $reply = array();
    $conn=getConnection();
    if(isset($input["imei"]) && !empty($input["imei"])){
        $profileInfo = loadCompanyProfile($conn,$input);
        $reply["status"] = !$profileInfo["error"];
        $reply["message"] = $profileInfo["message"];
        if( $reply["status"]){
			$reply["companyProfile"] = $profileInfo["companyProfile"];
		}
		$reply["message"] = $profileInfo["message"];
    }else{
        $reply["status"] = false;
        //$reply["message"] = $otGlobals::AUTHENTICATION_FAILED;
        $reply["message"] = "Dato imei es necesario.";
    }
    //header("HTTP/1.1 200 OK");
   
    echo json_encode($reply);
    exit();
}else{
    echo "This service is not browseable. Here you can find proper <a href='OcTecRechargeLoginDoc.rar' target='_blank'>documentation</a>";
}
function grabJson($txtJson){
	$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_login.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}
