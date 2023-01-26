<?php
session_start();
require "DAORuta.php";
$dao = new DAORuta();
if(isset($_POST["btnAgregar"])){
	$reply = $dao->insert(buildRuta());
	if($reply > 0)
		$_SESSION["mng_rutas"]["message"] = "Ruta registrada con el folio $reply";
	else
		$_SESSION["mng_rutas"]["message"] = $reply;
}else{
	if(isset($_POST["btnActualizar"])){
		$reply = $dao->update(buildRuta());
		if($reply == 0)
			$_SESSION["mng_rutas"]["message"] = "Ruta guardada sin cambios.";
		else
			if($reply == 1)
				$_SESSION["mng_rutas"]["message"] = "Cambios realizados correctamente.";
			else
				$_SESSION["mng_rutas"]["message"] = $reply;
	}else{
		if(isset($_GET["u"])){
			$_SESSION["mng_rutas"]["ruta"] = serialize($dao->searchById($_GET["u"]));
		}
	}
}
header("Location: ../mng_rutas.phtml");
function buildRuta(){
	$ruta = new Ruta();
	$ruta->setIdRuta 	($_REQUEST["idRuta"]);
	$ruta->setIdLinea	($_REQUEST["idLinea"]);
	$ruta->setorigen	($_REQUEST["origen"]);
	$ruta->setDestino	($_REQUEST["destino"]);
	$ruta->setTiempo	($_REQUEST["hrs"].":".$_REQUEST["min"].":00");
	$ruta->setKms		($_REQUEST["kms"]);
	$ruta->setStatus    ($_REQUEST["status"]);
	$ruta->setRuta	    ($_REQUEST["ruta"]);
	return $ruta;
}
?>