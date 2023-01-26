<?php
/* 
 * **********************************************************************
 * Project           : Prepay web services API
 * Program name      : sample.sas
 * Author            : Joel Becerril
 * Date created      : 20210120
 * Module            : Charge
 * Purpose           : Multicharge
 * Story             : Multidata collection
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
	$repok = array();
	$insdat = array();
	$inserr = array();
    $conn=getConnection();
    $ok = false;
	$cntinp = count($input["data"]);
	$datetime = getSysDate(true);
	//$i=0;
	for ($i=0; $i < $cntinp; $i++){ //for count elementos
	$input["data"][$i]["fecap"] = $datetime;
	$reply["status"] = true;
		if(!isset($input["data"][$i]["imei"]) || $input["data"][$i]["imei"] == "" || !is_numeric($input["data"][$i]["imei"])){
			$reply["status"] = false;
			$reply["message"] = "Imei no válido..- ".$input["data"][$i]["imei"];
		}else{
			if(!isset($input["data"][$i]["transactionId"]) || $input["data"][$i]["transactionId"] == ""){
				$reply["status"] = false;
				$reply["message"] = "Transacción es requerida.";
			}else{
				if(!isset($input["data"][$i]["idCompany"]) || $input["data"][$i]["idCompany"] == ""){
					$reply["status"] = false;
					$reply["message"] = "Compañia no puede ser vacía.";
				}else{
					if(isset($input["data"][$i]["chargeType"]) && !in_array($input["data"][$i]["chargeType"], array("A","E"))){
						$reply["status"] = false;
						$reply["message"] = "Tipo de movimiento no válido";
					}else{
						if($input["data"][$i]["chargeType"] == "A"){
							
							if(!isset($input["data"][$i]["idCard"]) || $input["data"][$i]["idCard"] == ""){
								$reply["status"] = false;
								$reply["message"] = "ID Tarjeta es requerido.";
							}else{
								if(!isRegisteredCard($conn, $input["data"][$i]["idCard"])){
									$reply["status"] = false;
									$reply["message"] = "Tarjeta no encontrada.";									
								}else{
									if(isBannedCard($conn, $input["data"][$i]["idCard"])){
										$reply["status"] = false;
										$reply["message"] = "Tarjeta en lista negra.";   
									}else{
										if(!validateThruDate($conn, $input["data"][$i]["idCard"])){
											$reply["status"] = false;
											$reply["message"] = "Tarjeta expirada."; 
										}else{
											if(!isset($input["data"][$i]["type"]) || empty($input["data"][$i]["type"])){
												$reply["status"] = false;
												$reply["message"] = "Tipo de tarjeta es requerido";
											}else{
												if(!isset($input["data"][$i]["balance"])){
													$reply["status"] = false;
													$reply["message"] = "Saldo de tarjeta requerido.";
												}
											}
										}
									}
								}
							}
						}				
						
						if($reply["status"]==true){
							
							if(!isset($input["data"][$i]["netCharge"]) || empty($input["data"][$i]["netCharge"])){
									$reply["status"] = false;
									$reply["message"] = "Monto de Cobro es requerido y debe ser mayor a 0";
								}else{
									
										if(isset($input["data"][$i]["netCharge"]) && !is_numeric($input["data"][$i]["netCharge"])){
											$reply["status"] = false;
											$reply["message"] = "Cobro debe ser numérico.";
										}else{
											if(!isset($input["data"][$i]["currency"]) || empty($input["data"][$i]["currency"])){
												$reply["status"] = false;
												$reply["message"] = "Moneda es requerido.";
											}else{
												if(!isset($input["data"][$i]["idUnit"]) || empty($input["data"][$i]["idUnit"])){
													$reply["status"] = false;
													$reply["message"] = "Unidad es requerido.";
												}else{
													if(!isset($input["data"][$i]["idRoute"]) || empty($input["data"][$i]["idRoute"])){
														$reply["status"] = false;
														$reply["message"] = "Ruta es requerido.";
													}else{
														if(!isset($input["data"][$i]["dateTimeCharge"]) || empty($input["data"][$i]["dateTimeCharge"])){
															$reply["status"] = false;
															$reply["message"] = "Fecha y hora de la transacción es requerida.";
														}else{
															if(!isset($input["data"][$i]["idLine"]) || empty($input["data"][$i]["idLine"])){
																
																$reply["message"] = "Línea es requerida.";
															}else{
																$insdat =  insertCharge($conn, $input["data"][$i]);																
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
		if ($reply["status"] == false){			
			$inserr =  insertChargeErr($conn, $input["data"][$i],$reply["message"]);
			$repok["response"]=$inserr;			
		}
	} //Termina FOR
	$repok["response"] =  returnMultiCharge($conn, $input["data"][0]["imei"], $datetime);
    //header("HTTP/1.1 200 OK");
    header('Content-type: application/json');
    echo json_encode($repok);
    exit();
}else{
    echo "This service is not browseable. Here you can find proper <a href='OcTecRechargeCatalogsDoc.rar' target='_blank'>documentation</a>";
}
function grabJson($txtJson){
	//$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_multicharge.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}