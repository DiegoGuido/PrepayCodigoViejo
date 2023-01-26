<?php
session_start();
if(isset($_SESSION["userinfo"])){
	require "DAOSaldoterminal.php";
	$dao = new DAOSaldoTerminal();
	if(isset($_POST["btnAgregar"]) && !empty($_POST["monto"]) && $_POST["monto"] > 0){	
		$reply = $dao->insert(buildSaldoterminal());
		if($reply > 0)
			$_SESSION["mng_saldoterminal"]["message"] = "Saldo registrado con el folio $reply";
		else
			$_SESSION["mng_saldoterminal"]["message"] = $reply;
	}else{		
		if(isset($_GET["usr"])){
			$_SESSION["mng_saldoterminal"]["saldoterminal"] = serialize($dao->searchById($_GET["usr"],$_GET["imei"]));
		}		
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_saldoterminal.phtml");
function buildSaldoterminal(){
	//echo "iddis:".$_REQUEST["idPuntoVenta"];
	$saldoterminal = new SaldoTerminal();
	$saldoterminal->setIdPuntoVenta 	($_REQUEST["ptovta"]);
	$saldoterminal->setImei 			($_REQUEST["imei"]);
	$saldoterminal->setUsuario			($_REQUEST["recusr"]);	
	$saldoterminal->setMonto			($_REQUEST["monto"]);	
	return $saldoterminal;
}
?>