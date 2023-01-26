<?php
/* 
 * **********************************************************************
 * Project           : Prepay web services API
 * Program name      : sample.sas
 * Author            : Joel Becerril
 * Date created      : 20210117
 * Module            : Multidata
 * Purpose           : Insertar n elementos de los movimientos de cobro y recarga
 * Story             : Multidata Collection
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
	//echo "input: ".$input["data"][1]["imei"]; //acceso a elementos del json
	//echo "tamaño: ".count($input["data"]); //acceso a elementos del json
	$cntinp = count($input["data"]);
	$datetime = getSysDate(true);
	//$i=0;
	for ($i=0; $i < $cntinp; $i++){ //for count elementos
		//echo "input: ".$input["data"][$i]["idCard"];
		$input["data"][$i]["fecap"] = $datetime;
		//if(!isset($input["data"][$i]["imei"]) || $input["data"][$i]["imei"] == "" || !is_numeric($input["data"][$i]["imei"])){
		if(!isset($input["data"][$i]["imei"]) || $input["data"][$i]["imei"] == ""){
			$reply["status"] = false;
			$reply["message"] = "Imei no válido..- ".$input["data"][$i]["imei"];
		}else{
			if( !isset($input["data"][$i]["userId"]) || !validateUserId($conn, $input["data"][$i]["userId"])){
				$reply["status"] = false;
				$reply["message"] = "Usuario no válido.";
			}else{
				if(!isset($input["data"][$i]["pointOfSaleId"]) || $input["data"][$i]["pointOfSaleId"] == "" || !is_numeric($input["data"][$i]["pointOfSaleId"])){
					$reply["status"] = false;
					$reply["message"] = "Punto de venta no válido.";
				}else{
					if(!isset($input["data"][$i]["idCard"]) || $input["data"][$i]["idCard"] == ""){
						$reply["status"] = false;
						$reply["message"] = "ID Tarjeta es requerido.";
					}else{
						if(!isset($input["data"][$i]["idCompany"]) || $input["data"][$i]["idCompany"] == ""){
							$reply["status"] = false;
							$reply["message"] = "Compañia no puede ser vacía.";
						}else{						
								if(isBannedCard($conn, $input["data"][$i]["idCard"])){
									$reply["status"] = false;
									$reply["message"] = "Tarjeta en lista negra.";   
								}else{									
										////////////////VENTA///////////////////////
										//echo "cp: ".$input["data"][$i]["cardPrice"];
										if(isset($input["data"][$i]["cardPrice"])){
											if(empty($input["data"][$i]["cardPrice"])){
												$reply["status"] = false;
												$reply["message"] = "Precio de la tarjeta es requerido";
											}else{
												if(!is_numeric($input["data"][$i]["cardPrice"])){
													$reply["status"] = false;
													$reply["message"] = "Precio de la tarjeta debe ser numérico.";
												}else{
													if(isset($input["data"][$i]["netCharge"]) && !is_numeric($input["data"][$i]["netCharge"])){
														$reply["status"] = false;
														$reply["message"] = "Monto de la recarga debe ser numérico.";
													}else{
														$top_amount = getMaxAmount($conn, $input["data"][$i]["imei"]);
														$prc_card = getCardPrice($conn, $input["data"][$i]["imei"]);
														$charge = isset($input["data"][$i]["netCharge"])?$input["data"][$i]["netCharge"]:0;
														if(($input["data"][$i]["cardPrice"] + $charge) < $prc_card){
															$reply["status"] = false;
															$reply["message"] = "Monto no puede ser menor que el costo de la tarjeta ".money_format('%.2n', $prc_card).".";
														}else{
															if(($input["data"][$i]["cardPrice"] + $charge) > $top_amount){
																$reply["status"] = false;
																$reply["message"] = "Monto no puede superar el monto máximo ".money_format('%.2n', $top_amount).".";   
															}else{
																//echo "hola";
																$newCard = insertNewCard($conn, $input["data"][$i], 0);
																if($newCard > 0){
																	if($input["data"][$i]["netCharge"]>0){
																		$insdat =  insertRecharge($conn, $input["data"][$i]);
																	//}else{
																		//$reply["status"] = true;
																		//$reply["message"] = "Tarjeta registrada correctamente."; 
																	}
																}else{
																	$reply["status"] = false;
																	$reply["message"] = $newCard;  
																}
															}
														}
													}
												}
											}
										}else{
										/////////////////RECARGA//////////////////////
											if(!isRegisteredCard($conn, $input["data"][$i]["idCard"])){
												$reply["status"] = false;
												$reply["message"] = "Tarjeta no encontrada.";
											}else{
												if(!validateThruDate($conn, $input["data"][$i]["idCard"])){
													$reply["status"] = false;
													$reply["message"] = "Tarjeta expirada."; 
												}else{
												if(!isset($input["data"][$i]["netCharge"]) || empty($input["data"][$i]["netCharge"])){
													$reply["status"] = false;
													$reply["message"] = "Monto de recarga es requerido y debe ser mayor a 0";
												}else{
													if(!isset($input["data"][$i]["balance"])){
														$reply["status"] = false;
														$reply["message"] = "Saldo de tarjeta requerido.";
													}else{
														if(isset($input["data"][$i]["netCharge"]) && !is_numeric($input["data"][$i]["netCharge"])){
															$reply["status"] = false;
															$reply["message"] = "Monto de la recarga debe ser numérico.";
														}else{
															$top_amount = getMaxAmount($conn, $input["data"][$i]["imei"]);
															if(($input["data"][$i]["balance"] + $input["data"][$i]["netCharge"]) > $top_amount){
																$reply["status"] = false;
																$reply["message"] = "La recarga no puede ser mayor a ".($top_amount-$input["data"][$i]["balance"]);
															}else{
																//echo "contador: ".$i;
																$insdat =  insertRecharge($conn, $input["data"][$i]);
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
	} //for count elementos
	$reply =  returnMultidata($conn, $input["data"][0]["imei"], $datetime);
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
	$fp = fopen('log_multidata.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}
