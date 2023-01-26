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
        $reply["message"] = "Imei no válido.";
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
                        if(isRegisteredCard($conn, $input["idCard"])){
                            $reply["status"] = false;
                            $reply["message"] = "Esta tarjeta ya ha sido registrada previamente.";
                        }else{
                            if(!isset($input["cardPrice"]) || empty($input["cardPrice"])){
                                $reply["status"] = false;
                                $reply["message"] = "Precio de la tarjeta es requerido";
                            }else{
                                if(isset($input["cardPrice"]) && !is_numeric($input["cardPrice"])){
                                    $reply["status"] = false;
                                    $reply["message"] = "Precio de la tarjeta debe ser numérico.";
                                }else{
                                    if(isset($input["netCharge"]) && !is_numeric($input["netCharge"])){
                                        $reply["status"] = false;
                                        $reply["message"] = "Monto de la recarga debe ser numérico.";
                                    }else{
                                        $top_amount = getMaxAmount($conn, $input["imei"]);
                                        $prc_card = getCardPrice($conn, $input["imei"]);
                                        $charge = isset($input["netCharge"])?$input["netCharge"]:0;
                                        if(($input["cardPrice"] + $charge) < $prc_card){
                                            $reply["status"] = false;
                                            $reply["message"] = "Monto no puede ser menor que el costo de la tarjeta ".money_format('%.2n', $prc_card).".";
                                        }else{
                                            if(($input["cardPrice"] + $charge) > $top_amount){
                                                $reply["status"] = false;
                                                $reply["message"] = "Monto no puede superar el monto máximo ".money_format('%.2n', $top_amount).".";   
                                            }else{
                                                /*if(isset($input["customize"]) && $input["customize"]){
                                                    $custDetail = $input["customerDetail"];
                                                    if(!isset($custDetail["name"]) || empty($custDetail["name"])){
                                                        $reply["status"] = false;
                                                        $reply["message"] = "Falta nombre de usuario.";
                                                    }else{
                                                        if(!isset($custDetail["surname"]) || empty($custDetail["surname"])){
                                                            $reply["status"] = false;
                                                            $reply["message"] = "Falta apellidos de usuario.";
                                                        }else{
                                                            if(!isset($custDetail["birthday"]) || empty($custDetail["birthday"])){
                                                                $reply["status"] = false;
                                                                $reply["message"] = "Falta fecha de nacimiento.";
                                                            }else{
                                                                if(!isset($custDetail["email"]) || empty($custDetail["email"])){
                                                                    $reply["status"] = false;
                                                                    $reply["message"] = "Indica un correo de usuario.";   
                                                                }else{
                                                                    if(!isset($custDetail["phoneNumber"]) || empty($custDetail["phoneNumber"])){
                                                                        $reply["status"] = false;
                                                                        $reply["message"] = "Indica un teléfono.";   
                                                                    }else{
                                                                        $custDetail["userId"]=$input["userId"];
                                                                        $newCust = insertCustomerDetail($conn, $custDetail);
                                                                        if($newCust > 0){
                                                                            $newCard = insertNewCard($conn, $input, $newCust);
                                                                            if($newCard > 0){
                                                                                 if($input["netCharge"]>0){
                                                                                    $reply =  insertRecharge($conn, $input);
                                                                                }else{
                                                                                    $reply["status"] = true;
                                                                                    $reply["message"] = "Tarjeta registrada correctamente."; 
                                                                                }
                                                                            }else{
                                                                                $reply["status"] = false;
                                                                                $reply["message"] = $newCard;  
                                                                            }
                                                                        }else{
                                                                            $reply["status"] = false;
                                                                            $reply["message"] = $newCust;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }else{*/
                                                    $newCard = insertNewCard($conn, $input, 0);
                                                    if($newCard > 0){
                                                        if($input["netCharge"]>0){
                                                            $reply =  insertRecharge($conn, $input);
                                                        }else{
                                                            $reply["status"] = true;
                                                            $reply["message"] = "Tarjeta registrada correctamente."; 
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
                            }
                        }
                    }
                }
            }
        }
    //}
    //header("HTTP/1.1 200 OK");
    header('Content-type: application/json');
    //$reply["response"] = "";
    echo json_encode($reply);
    exit();
}else{
    echo "This service is not browseable. Here you can find proper <a href='OcTecRechargeCatalogsDoc.rar' target='_blank'>documentation</a>";
}
function grabJson($txtJson){
	//$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_sale.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}