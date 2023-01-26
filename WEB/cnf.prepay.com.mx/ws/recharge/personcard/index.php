<?php
/* 
 * **********************************************************************
 * Project           : Ocetech web services API
 * Program name      : sample.sas
 * Author            : Joel Becerril
 * Date created      : 2020/12/01
 * Module            : Personalize
 * Purpose           : Provide customer solution to Ocetech terminals 'Personalize CARD'
 * Story             : Person Card collection
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
                     if(isPersonalizedCard($conn, $input["idCard"])){
                            $reply["status"] = false;
                            $reply["message"] = "Esta tarjeta ya ha sido registrada previamente.";
                        }else{
                            if(!isset($input["name"]) || empty($input["name"])){
                                $reply["status"] = false;
                                $reply["message"] = "Falta nombre de usuario.";
                            }else{
                                if(!isset($input["surname"]) || empty($input["surname"])){
                                    $reply["status"] = false;
                                    $reply["message"] = "Falta apellidos de usuario.";
                                }else{
                                    if(!isset($input["birthday"]) || empty($input["birthday"])){
                                        $reply["status"] = false;
                                        $reply["message"] = "Falta fecha de nacimiento.";
                                    }else{
                                        if(!isset($input["email"]) || empty($input["email"])){
                                            $reply["status"] = false;
                                            $reply["message"] = "Indica un correo de usuario.";   
                                        }else{
                                            if(!isset($input["phoneNumber"]) || empty($input["phoneNumber"])){
                                                $reply["status"] = false;
                                                $reply["message"] = "Indica un teléfono.";   
                                            }else{											
												$input["userId"]=$input["userId"];
												$newCust = insertCustomerDetail1($conn, $input);
												if($newCust > 0){
													$reply["status"] = true;
													$reply["insertid"] = $newCust;
												}else{
													$reply["status"] = false;
													$reply["insertid"] = "";
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
    //$reply["response"] = "";
    echo json_encode($reply);
    exit();
}else{
    echo "This service is not browseable. Here you can find proper <a href='OcTecRechargeCatalogsDoc.rar' target='_blank'>documentation</a>";
}
function grabJson($txtJson){
	//$txtJson = str_replace(" ","",$txtJson);
	$txtJson = str_replace("\r\n","",$txtJson);
	$fp = fopen('log_person.txt', 'a');//opens file in append mode.
	fwrite($fp, date("Y-m-d H:i:s")." ".$txtJson."\n");
	fclose($fp);
}