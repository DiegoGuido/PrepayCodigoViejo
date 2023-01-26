<?php
session_start();
if(isset($_SESSION["userinfo"])){
	require "DAOCompania.php";
	$dao = new DAOCompania();
	if(isset($_POST["btnAgregar"])){
		//echo "aqui estoy 1";
		$reply = $dao->insert(buildCompania());
		if($reply > 0)
			$_SESSION["mng_compania"]["message"] = "Compania registrada con el folio $reply";
		else
			$_SESSION["mng_compania"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			$reply = $dao->update(buildCompania());
			if($reply == 0)
				$_SESSION["mng_compania"]["message"] = "Compania guardada sin cambios.";
			else
				if($reply == 1)
					$_SESSION["mng_compania"]["message"] = "Cambios realizados correctamente.";
				else
					$_SESSION["mng_compania"]["message"] = $reply;
		}else{
			if(isset($_GET["u"])){
				$_SESSION["mng_compania"]["compania"] = serialize($dao->searchById($_GET["u"]));
			}
		}
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_compania.phtml");
function buildCompania(){
	$compania = new compania();
	$compania->setIdCompania 	($_REQUEST["idCompania"]);
	$compania->setNomCompania	($_REQUEST["nomCompania"]);	
	$compania->setMoneda		($_REQUEST["moneda"]);
	$compania->setStatus	    ($_REQUEST["status"]);
	$compania->setUrlCompania	($_REQUEST["urlCompania"]);	
	return $compania;
}
?>