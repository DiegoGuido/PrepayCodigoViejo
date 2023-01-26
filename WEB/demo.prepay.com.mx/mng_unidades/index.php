<?php
session_start();
require "DAOUnidad.php";
$dao = new DAOUnidad();
if(isset($_POST["btnAgregar"])){
	$reply = $dao->insert(buildUnidad());
	if($reply > 0)
		$_SESSION["mng_unidades"]["message"] = "Unidad registrada con el folio $reply";
	else
		$_SESSION["mng_unidades"]["message"] = $reply;
}else{
	if(isset($_POST["btnActualizar"])){
		$reply = $dao->update(buildUnidad());
		if($reply == 0)
			$_SESSION["mng_unidades"]["message"] = "Unidad guardada sin cambios.";
		else
			if($reply == 1)
				$_SESSION["mng_unidades"]["message"] = "Cambios realizados correctamente.";
			else
				$_SESSION["mng_unidades"]["message"] = $reply;
	}else{
		if(isset($_GET["u"])){
			$_SESSION["mng_unidades"]["unidad"] = serialize($dao->searchById($_GET["u"]));
		}
	}
}
header("Location: ../mng_unidades.phtml");
function buildUnidad(){
	$unidad = new Unidad();
	$unidad->setIdUnidad	($_REQUEST["idUnidad"]);
	$unidad->setIdLinea		($_REQUEST["idLinea"]);
	$unidad->setEconomico	($_REQUEST["economico"]);
	$unidad->setMarca		($_REQUEST["marca"]);
	$unidad->setModelo		($_REQUEST["modelo"]);
	$unidad->setPax         ($_REQUEST["pax"]);
	$unidad->setMotor       ($_REQUEST["motor"]);
	$unidad->setPlaca       ($_REQUEST["placa"]);
	$unidad->setIdConductor ($_REQUEST["idConductor"]);
	$unidad->setStatus      ($_REQUEST["status"]);
	$unidad->setIdRuta      ($_REQUEST["idRuta"]);
	return $unidad;
}
?>