<?php
session_start();
require "DAOLinea.php";
$dao = new DAOLinea();
if(isset($_POST["btnAgregar"])){
	$reply = $dao->insert(buildLinea());
	if($reply > 0)
		$_SESSION["mng_lineas"]["message"] = "Línea registrada con el folio $reply";
	else
		$_SESSION["mng_lineas"]["message"] = $reply;
}else{
	if(isset($_POST["btnActualizar"])){
		$reply = $dao->update(buildLinea());
		if($reply == 0)
			$_SESSION["mng_lineas"]["message"] = "Línea guardada sin cambios.";
		else
			if($reply == 1)
				$_SESSION["mng_lineas"]["message"] = "Cambios realizados correctamente.";
			else
				$_SESSION["mng_lineas"]["message"] = $reply;
	}else{
		if(isset($_GET["u"])){
			$_SESSION["mng_lineas"]["linea"] = serialize($dao->searchById($_GET["u"]));
			//echo $_SESSION["mng_lineas"]["linea"];
		}
	}
}
header("Location: ../mng_lineas.phtml");
function buildLinea(){
	$linea = new Linea();
	$linea->setIdLinea 			($_REQUEST["idLinea"]);
	$linea->setNombreLinea		($_REQUEST["nombreLinea"]);
	$linea->setDescripcionLinea	($_REQUEST["descripcionLinea"]);
	$linea->setCompania			($_REQUEST["idCompania"]);
	$linea->setStatus      		($_REQUEST["status"]);
	return $linea;
}
?>