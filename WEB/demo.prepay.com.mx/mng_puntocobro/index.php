<?php
session_start();
if(isset($_SESSION["userinfo"])){
	require "DAOPuntocobro.php";
	$dao = new DAOPuntoCobro();
	if(isset($_POST["btnAgregar"])){
		//echo "aqui estoy 1";
		$reply = $dao->insert(buildPuntoCobro());
		if($reply > 0)
			$_SESSION["mng_puntocobro"]["message"] = "Punto de Cobro registrado con el folio $reply";
		else
			$_SESSION["mng_puntocobro"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			//echo "estoy en upd1";
			$reply = $dao->update(buildPuntoCobro());
			if($reply == 0)
				$_SESSION["mng_puntocobro"]["message"] = "Punto de Cobro guardado sin cambios.";
			else
				if($reply == 1)
					$_SESSION["mng_puntocobro"]["message"] = "Cambios realizados correctamente.";
				else
					$_SESSION["mng_puntocobro"]["message"] = $reply;
		}else{
			if(isset($_GET["u"])){
				$_SESSION["mng_puntocobro"]["puntocobro"] = serialize($dao->searchById($_GET["u"]));
			}
		}
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_puntocobro.phtml");
function buildPuntoCobro(){
	//echo "iddis:".$_REQUEST["idPuntoCobro"];
	$puntocobro = new PuntoCobro();
	$puntocobro->setIdPuntoCobro 	($_REQUEST["idPuntoCobro"]);
	$puntocobro->setImei 			($_REQUEST["imei"]);
	$puntocobro->setDesPuntoCobro	($_REQUEST["desPuntoCobro"]);	
	$puntocobro->setLinea			($_REQUEST["linea"]);
	$puntocobro->setStatus	    	($_REQUEST["status"]);
	$puntocobro->setModo	    	($_REQUEST["modo"]);
	
	return $puntocobro;
}
?>