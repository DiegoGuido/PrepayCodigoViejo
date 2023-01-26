<?php
/* 
 * **********************************************************************
 * Project           : PREPAY web services API
 * Program name      : sample.sas
 * Author            : JBG
 * Date created      : 030221
 * Module            : GPS
 * Purpose           : GPS TESTING
 * Story             : GPS PROJECT COLLECTION
 */
setlocale(LC_MONETARY, 'es_MX');
if (filter_var(getenv('REQUEST_METHOD')) == 'POST'){
    include "../_OT_Globals.php";
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
    $ok = false;
	if(empty($input["dat1"]) || empty($input["dat2"])){		
	    $reply["status"] = false;
        $reply["message"] = "No hay datos para insertar";
    }else{
		$reply = insertTrackGPS($conn, $input);
		//$reply["gpstimereport"] = 1;
		$reply["motorstop"] = true;
	}	
    //header("HTTP/1.1 200 OK");
    header('Content-type: application/json');
    echo json_encode($reply);
    exit();
}else{
    echo "This service is not browseable. Here you can find proper <a href='OcTecRechargeCatalogsDoc.rar' target='_blank'>documentation</a>";
}

function grabJson($txtJson){
	//$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_track.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}
