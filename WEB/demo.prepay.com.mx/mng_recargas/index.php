<?php
session_start();
if(isset($_SESSION["userinfo"])){
	//no hago nada
}else{
	header("Location: ../");
}
header("Location: ../mng_recargas.phtml");
function buildRecargas(){
	$recarga = new recargas();
	$recarga->setIdPuntoVenta 	($_REQUEST["idPuntoVenta"]);
	$recarga->setFecIni			($_REQUEST["fecini"]);	
	$recarga->setFecFin			($_REQUEST["fecfin"]);	
	return $recarga;
}
?>