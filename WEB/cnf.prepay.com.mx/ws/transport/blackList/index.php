<?php
/* 
 * **********************************************************************
 * Project           : Prepay web services API
 * Program name      : sample.sas
 * Author            : Joel Becerril
 * Date created      : 20200824
 * Purpose           : Black List
 * Module			 : Global
 * Story			 : Card Validator
 */
if (filter_var(getenv('REQUEST_METHOD')) == 'POST'){
    include "../../_OT_Globals.php";
    $otGlobals = new OTConstants();
    $prefix = $otGlobals->getPrefix();
    include "../".$prefix.$otGlobals::CONNECTION_FILE;
    include $prefix.$otGlobals::DAO_FILE;
	date_default_timezone_set("America/Mexico_City");
    //$input = filter_input_array(INPUT_POST);
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);
    $reply = array();
	$lstupd = date("Y-m-d H:m:s");
    $conn=getConnection();
    //if(isset($input["imei"]) && $input["imei"]!= "" && is_numeric($input["imei"]) && strlen($input["imei"])==15){
	if(isset($input["imei"]) && $input["imei"]!= ""){
        $setupInfo = getBlackList($conn, $input); 
        //var_dump($setupInfo["cards"]);
		//echo "error: ".$setupInfo["error"];
        if(!$setupInfo["error"]){
            $reply["status"] = true;
            $reply["message"] = $setupInfo["message"];
			$reply["lstupd"] = $lstupd;
			$reply["response"] = $setupInfo["cards"];			
        }else{
            $reply["status"] = false;
            $reply["message"] = $setupInfo["message"];						
        }
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