<?php
session_start();
require "DAOConductor.php";
$dao = new DAOConductor();
if(isset($_POST["btnAgregar"])){
	$reply = $dao->insert(buildConductor());
	if($reply > 0)
		$_SESSION["mng_conductores"]["message"] = "Conductor registrada con el folio $reply";
	else
		$_SESSION["mng_conductores"]["message"] = $reply;
}else{
	if(isset($_POST["btnActualizar"])){
		$reply = $dao->update(buildConductor());
		if($reply == 0)
			$_SESSION["mng_conductores"]["message"] = "Conductor guardada sin cambios.";
		else
			if($reply == 1)
				$_SESSION["mng_conductores"]["message"] = "Cambios realizados correctamente.";
			else
				$_SESSION["mng_conductores"]["message"] = $reply;
	}else{
		if(isset($_GET["u"])){
			$_SESSION["mng_conductores"]["conductor"] = serialize($dao->searchById($_GET["u"]));
		}
	}
}
header("Location: ../mng_conductores.phtml");
function buildConductor(){
	$conductor = new Conductor();
	$conductor->setIdConductor ($_REQUEST["idConductor"]);
	$conductor->setNombre		($_REQUEST["nombre"]);
	$conductor->setFechaEntrada($_REQUEST["fechaEntrada"]);
	$conductor->setFechaSalida	($_REQUEST["fechaSalida"]);
	$conductor->setIdLinea		($_REQUEST["idLinea"]);
	$conductor->setStatus      ($_REQUEST["status"]);
	return $conductor;
}
?>