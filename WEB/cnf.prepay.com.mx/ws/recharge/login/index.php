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
	grabJson($json);
    $input = json_decode($json, true);
    $reply = array();
    $conn=getConnection();
	$today = $currDate = date("Y-m-d H:i:s");
	$trnday = date("Y-m-d");
	//$trnday = "2021-02-20";
    if(isset($input["imei"]) && $input["imei"]!= ""){		
		$setupInfo1 = getValidDevice($conn, $input); 			
			if(!$setupInfo1["error"]){
				$reply["status"] = false;
				$reply["message"] = $setupInfo1["message"];            
			}else{
				if(isset($input["usuario"]) && isset($input["password"])){
					$userInfo = authenticateUser($conn,$input);				
					if($userInfo != null){
						$valopusr = valOpUsr($conn,$input,$trnday);
						if ($valopusr["error"]){
							$reply["status"] = false;
							$reply["message"] = $valopusr["message"];
						}else{
							$reply["status"] = true;
							$reply["message"] = $otGlobals::WELCOME_MESSAGE;
							$value = array();
							$value["user"]=$userInfo;
							$reply["response"] = $value;
							$reply["dtServer"] = $today;
						}
					}else{
						$reply["status"] = false;
						$reply["message"] = $otGlobals::AUTHENTICATION_FAILED;
					}
				}else{
					$reply["status"] = false;
					$reply["message"] = $otGlobals::AUTHENTICATION_FAILED;
				}
				//header("HTTP/1.1 200 OK");
			}
	}else{
        $reply["status"] = true;
        $reply["message"] = "IMEI Incorrecto o Vacio";
    }
    echo json_encode($reply);
    exit();
}else{
    echo "This service is not browseable. Here you can find proper <a href='OcTecRechargeLoginDoc.rar' target='_blank'>documentation</a>";
}
function grabJson($txtJson){
	//$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_login.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}
?>
