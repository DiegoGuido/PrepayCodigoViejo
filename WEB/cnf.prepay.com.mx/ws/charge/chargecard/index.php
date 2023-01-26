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
    $ok = false;
    if(!isset($input["imei"]) || $input["imei"] == "" || !is_numeric($input["imei"])){
        $reply["status"] = false;
        $reply["message"] = "Imei no válido..- ".$input["imei"];
    }else{
		if(!isset($input["transactionId"]) || $input["transactionId"] == ""){
			$reply["status"] = false;
			$reply["message"] = "Transacción es requerida.";
		}else{
			if(!isset($input["idCompany"]) || $input["idCompany"] == ""){
				$reply["status"] = false;
				$reply["message"] = "Compañia no puede ser vacía.";
			}else{
				if(isset($input["chargeType"]) && !in_array($input["chargeType"], array("A","E"))){
					$reply["status"] = false;
					$reply["message"] = "Tipo de movimiento no válido";
				}else{
					if($input["chargeType"] == "A"){
						if(!isset($input["idCard"]) || $input["idCard"] == ""){
							$reply["status"] = false;
							$reply["message"] = "ID Tarjeta es requerido.";
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
										if(!isset($input["type"]) || empty($input["type"])){
											$reply["status"] = false;
											$reply["message"] = "Tipo de tarjeta es requerido";
										}else{
											if(!isset($input["balance"])){
												$reply["status"] = false;
												$reply["message"] = "Saldo de tarjeta requerido.";
											}
										}
									}
								}
							}
						}
					}
					
					if(!isset($reply["status"])){
						
							
							if(!isset($input["netCharge"]) || empty($input["netCharge"])){
								$reply["status"] = false;
								$reply["message"] = "Monto de recarga es requerido y debe ser mayor a 0";
							}else{
								
									if(isset($input["netCharge"]) && !is_numeric($input["netCharge"])){
										$reply["status"] = false;
										$reply["message"] = "Cobro debe ser numérico.";
									}else{
										if(!isset($input["currency"]) || empty($input["currency"])){
											$reply["status"] = false;
											$reply["message"] = "Moneda es requerido.";
										}else{
											if(!isset($input["idUnit"]) || empty($input["idUnit"])){
												$reply["status"] = false;
												$reply["message"] = "Unidad es requerido.";
											}else{
												if(!isset($input["idRoute"]) || empty($input["idRoute"])){
													$reply["status"] = false;
													$reply["message"] = "Ruta es requerido.";
												}else{
													if(!isset($input["dateTimeCharge"]) || empty($input["dateTimeCharge"])){
														$reply["status"] = false;
														$reply["message"] = "Fecha y hora de la transacción es requerida.";
													}else{
														if(!isset($input["idLine"]) || empty($input["idLine"])){
															$reply["status"] = false;
															$reply["message"] = "Línea es requerida.";
														}else{
															$reply =  insertCharge($conn, $input);
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
function grabJson($txtJson){
	//$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_payment.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}