<?php
session_start();
if(isset($_SESSION["userinfo"])){
	require "DAOPuntoventa.php";
	$dao = new DAOPuntoVenta();
	if(isset($_POST["btnAgregar"])){
		//echo "aqui estoy 1";
		$reply = $dao->insert(buildPuntoVenta());
		if($reply > 0)
			$_SESSION["mng_puntoventa"]["message"] = "Punto de Venta registrado con el folio $reply";
		else
			$_SESSION["mng_puntoventa"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			//echo "estoy en upd1";
			$reply = $dao->update(buildPuntoVenta());
			if($reply == 0)
				$_SESSION["mng_puntoventa"]["message"] = "Punto de Venta guardado sin cambios.";
			else
				if($reply == 1)
					$_SESSION["mng_puntoventa"]["message"] = "Cambios realizados correctamente.";
				else
					$_SESSION["mng_puntoventa"]["message"] = $reply;
		}else{
			if(isset($_GET["u"])){
				$_SESSION["mng_puntoventa"]["puntoventa"] = serialize($dao->searchById($_GET["u"]));
			}
		}
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_puntoventa.phtml");
function buildPuntoVenta(){
	//echo "iddis:".$_REQUEST["idPuntoVenta"];
	$puntoventa = new PuntoVenta();
	$puntoventa->setIdPuntoVenta 	($_REQUEST["idPuntoVenta"]);
	$puntoventa->setImei 			($_REQUEST["imei"]);
	$puntoventa->setDesPuntoVenta	($_REQUEST["desPuntoVenta"]);	
	$puntoventa->setLinea			($_REQUEST["linea"]);
	$puntoventa->setStatus	    	($_REQUEST["status"]);
	$puntoventa->setTipo	    	($_REQUEST["tipo"]);
	return $puntoventa;
}
?>