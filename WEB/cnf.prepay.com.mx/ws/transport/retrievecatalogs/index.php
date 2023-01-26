<?php
/* 
 * **********************************************************************
 * Project           : Ocetech web services API
 * Program name      : sample.sas
 * Author            : David Vargas
 * Date created      : 20190629
 * Purpose           : Provide customer solution to Ocetech terminals
 * Module			 : Global
 * Story			 : Configuration
 */
if (filter_var(getenv('REQUEST_METHOD')) == 'POST'){
    include "../../_OT_Globals.php";
    $otGlobals = new OTConstants();
    $prefix = $otGlobals->getPrefix();
    include "../".$prefix.$otGlobals::CONNECTION_FILE;
    include $prefix.$otGlobals::DAO_FILE;
    //$input = filter_input_array(INPUT_POST);
    $json = file_get_contents('php://input');
	//grabJson($json);
    $input = json_decode($json, true);
    $reply = array();
    $conn=getConnection();
    //if(isset($input["imei"]) && $input["imei"]!= "" && is_numeric($input["imei"])){
	if(isset($input["imei"]) && $input["imei"]!= ""){
        //echo "valor del imei ".$input["imei"];
        $setupInfo = retrieveConfiguration($conn, $input); 
        //var_dump($setupInfo);
        if(!$setupInfo["error"]){
            $reply["status"] = true;
            $reply["message"] = $setupInfo["message"];
            $reply["response"] = $setupInfo["catalogs"];
        }else{
            $reply["status"] = false;
             $reply["message"] = "Failed retrieving catalogs.";
        }
    }else{
        $reply["status"] = true;
        $reply["message"] = "IMEI vacio o no válido";
    }
    //header("HTTP/1.1 200 OK");
    header('Content-type: application/json');
    echo json_encode($reply);
    exit();
}else{
    echo "This service is not browseable. Here you can find proper <a href='OcTecRechargeCatalogsDoc.rar' target='_blank'>documentation</a>";
}
function grabJson($txtJson){
	$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_catalogs.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}
