<?php
session_start();
if(isset($_SESSION["userinfo"])){
	//no hago nada
}else{
	header("Location: ../");
}
header("Location: ../mng_tracking.phtml");
function buildTracking(){
	$track = new tracking();
	$track->setIdPuntoVenta 	($_REQUEST["unidad"]);
	$track->setFecIni			($_REQUEST["fecini"]);	
	$track->setFecFin			($_REQUEST["fecfin"]);	
	return $track;
}
?>