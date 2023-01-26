<?php
session_start();
if(isset($_SESSION["userinfo"])){
	require "DAODispositivos.php";
	$dao = new DAODispositivos();
	if(isset($_POST["btnAgregar"])){
		//echo "aqui estoy 1";
		$reply = $dao->insert(buildDispositivo());
		if($reply > 0)
			$_SESSION["mng_dispositivos"]["message"] = "Dispositivo registrado con el folio $reply";
		else
			$_SESSION["mng_dispositivos"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			//echo "estoy en upd1";
			$reply = $dao->update(buildDispositivo());
			if($reply == 0)
				$_SESSION["mng_dispositivos"]["message"] = "Dispositivo guardado sin cambios.";
			else
				if($reply == 1)
					$_SESSION["mng_dispositivos"]["message"] = "Cambios realizados correctamente.";
				else
					$_SESSION["mng_dispositivos"]["message"] = $reply;
		}else{
			if(isset($_GET["u"])){
				$_SESSION["mng_dispositivos"]["dispositivos"] = serialize($dao->searchById($_GET["u"]));
			}
		}
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_dispositivos.phtml");
function buildDispositivo(){
	//echo "iddis:".$_REQUEST["idDispositivo"];
	$dispositivo = new Dispositivos();
	$dispositivo->setIdDispositivo 	($_REQUEST["idDispositivo"]);
	$dispositivo->setImei 			($_REQUEST["imei"]);
	$dispositivo->setDesDispositivo	($_REQUEST["desdispositivo"]);	
	$dispositivo->setCompania		($_REQUEST["compania"]);
	$dispositivo->setStatus	    	($_REQUEST["status"]);
	return $dispositivo;
}
?>