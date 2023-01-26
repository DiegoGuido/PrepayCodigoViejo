<?php
/* 
 * **********************************************************************
 * Project           : Ocetech web services API
 * Program name      : sample.sas
 * Author            : David Vargas
 * Date created      : 20190708
 * Module            : Recharge
 * Purpose           : Provide customer solution to Ocetech terminals 'BUY CARD'
 * Story             : Buy Card collection
 */
setlocale(LC_MONETARY, 'es_MX');
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
    $ok = false;
    if(!isset($input["imei"]) || $input["imei"] == "" || !is_numeric($input["imei"])){
        $reply["status"] = false;
        $reply["message"] = "Imei no válido..- ".$input["imei"];
    }else{
		if(!isset($input["idUnit"]) || $input["idUnit"] == ""){
			$reply["status"] = false;
			$reply["message"] = "Unidad es requerida.";
		}else{
			if(!isset($input["idLine"]) || $input["idLine"] == ""){
				$reply["status"] = false;
				$reply["message"] = "Linea requerida.";
			}else{
				if(!isset($input["idCompany"]) || $input["idCompany"] == ""){
					$reply["status"] = false;
					$reply["message"] = "Compañia no puede ser vacía.";
				}else{
					if(!isset($input["idRoute"]) || $input["idRoute"] == ""){
						$reply["status"] = false;
						$reply["message"] = "Ruta no puede ser vacía.";
					}else{
						if(!isset($input["idIncidence"]) || $input["idIncidence"] == ""){
							$reply["status"] = false;
							$reply["message"] = "Incidencia requerida.";
						}else{
							if(!isset($input["dateTimeIncidence"]) || $input["dateTimeIncidence"] == ""){
								$reply["status"] = false;
								$reply["message"] = "Fecha y hora requerida.";
							}else{
								if(!isset($input["location"]) || empty($input["location"])){
									$reply["status"] = false;
									$reply["message"] = "Coordenadas son requeridas";
								}else{
									if(!validateLatLong(explode(",",$input["location"])[0],explode(",",$input["location"])[1])){
										$reply["status"] = false;
										$reply["message"] = "Coordenadas no son validas";
									}else{
										$reply = insertIncidence($conn, $input);
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
    //header("HTTP/1.1 200 OK");
    header('Content-type: application/json');
    echo json_encode($reply);
    exit();
}else{
    echo "This service is not browseable. Here you can find proper <a href='OcTecRechargeCatalogsDoc.rar' target='_blank'>documentation</a>";
}
