<?php
session_start();
if(isset($_SESSION["userinfo"])){
	require "DAORecaudacion.php";
	$dao = new DAORecaudacion();
	if(isset($_POST["btnAgregar"])){
		if (!empty($_POST["monto"]) && $_POST["monto"] > 0){
			if ($_POST["monto"] <= $_POST["adeudo"]){
				$reply = $dao->insert(buildRecaudacion());
				if($reply > 0)
					$_SESSION["mng_recaudacion"]["message"] = "Abono registrado con el folio $reply";
				else
					$_SESSION["mng_recaudacion"]["message"] = $reply;
			} else {
				$_SESSION["mng_recaudacion"]["message"] = "El monto no puede mayor al Adeudo";
			}
		} else {
			$_SESSION["mng_recaudacion"]["message"] = "El monto no puede ser vacio o 0";
		}
	}else{		
		if(isset($_GET["usr"])){
			$_SESSION["mng_recaudacion"]["recaudacion"] = serialize($dao->searchById($_GET["usr"],$_GET["imei"],$_GET["fecrec"]));
		}		
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_recaudacion.phtml");
function buildRecaudacion(){
	//echo "iddis:".$_REQUEST["idPuntoVenta"];
	$recaudacion = new Recaudacion();
	$recaudacion->setIdPuntoVenta 	($_REQUEST["ptovta"]);
	$recaudacion->setImei 			($_REQUEST["imei"]);
	$recaudacion->setUsuario		($_REQUEST["recusr"]);	
	$recaudacion->setMonto			($_REQUEST["monto"]);
	$recaudacion->setFecRec			($_REQUEST["fecrec"]);
	
	return $recaudacion;
}
?>