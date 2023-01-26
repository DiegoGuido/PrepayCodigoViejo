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
	grabJson($json);
    $input = json_decode($json, true);
    $reply = array();
    $conn=getConnection();
   
    //if(!isset($input["imei"]) || $input["imei"] == "" || !is_numeric($input["imei"])){
	if(!isset($input["imei"]) || $input["imei"] == ""){
        $reply["status"] = false;
        $reply["message"] = "Imei no válido..- ".$input["imei"];
    }else{
        if( !isset($input["userId"]) || !validateUserId($conn, $input["userId"])){
            $reply["status"] = false;
            $reply["message"] = "Usuario no válido.";
        }else{
            if(!isset($input["pointOfSaleId"]) || $input["pointOfSaleId"] == "" || !is_numeric($input["pointOfSaleId"])){
                $reply["status"] = false;
                $reply["message"] = "Punto de venta no válido.";
            }else{
                if(!isset($input["idCard"]) || $input["idCard"] == ""){
                    $reply["status"] = false;
                    $reply["message"] = "ID Tarjeta es requerido.";
                }else{
                    if(!isset($input["idCompany"]) || $input["idCompany"] == ""){
                        $reply["status"] = false;
                        $reply["message"] = "Compañia no puede ser vacía.";
                    }else{
                        if(!isRegisteredCard($conn, $input["idCard"])){
                            $reply["status"] = false;
                            $reply["message"] = "Tarjeta no encontrada.";
                        }else{
                            if(isBannedCard($conn, $input["idCard"])){
                                $reply["status"] = false;
                                $reply["message"] = "Tarjeta en lista negra.";   
                            }else{
                                if(!validateThruDate($conn, $input["idCard"])){
                                    $reply["status"] = false;
                                    $reply["message"] = "Tarjeta expirada."; 
                                }else{
                                    if(!isset($input["netCharge"]) || empty($input["netCharge"])){
                                        $reply["status"] = false;
                                        $reply["message"] = "Monto de recarga es requerido y debe ser mayor a 0";
                                    }else{
                                        if(!isset($input["balance"])){
                                            $reply["status"] = false;
                                            $reply["message"] = "Saldo de tarjeta requerido.";
                                        }else{
                                            if(isset($input["netCharge"]) && !is_numeric($input["netCharge"])){
                                                $reply["status"] = false;
                                                $reply["message"] = "Monto de la recarga debe ser numérico.";
                                            }else{
                                                $top_amount = getMaxAmount($conn, $input["imei"]);
                                                if(($input["balance"] + $input["netCharge"]) > $top_amount){
                                                    $reply["status"] = false;
                                                    $reply["message"] = "La recarga no puede ser mayor a ".($top_amount-$input["balance"]);
                                                }else{
                                                    $reply =  insertRecharge($conn, $input);
                                                    if($reply["folio"] > 1){ // $ret["folio"]
                                                        $folio = $reply["folio"];
                                                        $url = "http://test.auditaenlinea.com";
                                                        $input["folio"] = $folio;
														$arrData = buildRecord($input);
                                                        submitData($url, $arrData);
                                                    }
                                                }
                                            }
                                        } 
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
function submitData($url, $arrData){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrData, true));
	file_put_contents('trx_log.txt', date("Y-m-d H:i:s")." P ".$arrData["cve_reg"].": ".json_encode($arrData).PHP_EOL , FILE_APPEND | LOCK_EX);
	$output = curl_exec($ch);
	file_put_contents('trx_log.txt', date("Y-m-d H:i:s")." R ".$arrData["cve_reg"].": $output".PHP_EOL , FILE_APPEND | LOCK_EX);
	$jsonReply = json_decode($output, true);
	return $jsonReply;
}
function buildRecord($input){
	$arrData = array();
	$arrData["cve_reg"] = $input["folio"];
	$arrData["monto"] = $input["netCharge"];
	$arrData["moneda"] = $input["currency"];
	$arrData["cve_mov"] = 'R';
	$arrData["cve_trj"] = $input["idCard"];
	$arrData["cve_tip"] = $input["type"];
	$arrData["fec_rec"] = date("Y-m-d H:i:s", strtotime($input["dateTimeRecharge"]));
	$arrData["fec_cap"] = date("Y-m-d H:i:s");
	/*
        $arrReturn = array();
	$arrReturn["data"] = $arrData;
         * 
         */
	return $arrData;
}
function grabJson($txtJson){
	//$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_recharge.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}
