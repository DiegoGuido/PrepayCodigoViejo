<?
session_start();
if(isset($_SESSION["userinfo"])){
	include "../cnx.php";
	$conn = getConnection();
	$mon = $_POST["monto"];
	$fef = $_POST["fecefe"];
	$stt = empty($_POST["status"])?"A":$_POST["status"];
	$cve = $_POST["idCosto"];
	$cia = $_SESSION["compania"]["idCompania"];
	$usr = $_SESSION["userinfo"]["username"];
	$fec = date("Y-m-d H:i:s");
	$_SESSION["mng_compania"]["costos"]="";
	$_SESSION["costos"]["message"]="";
	//echo "stt: ".$stt."<br>";
	
	if(isset($_POST["btnAgregar"])){
		//echo "aqui estoy 1";
		$reply = InsertData($mon,$fef,$stt,$cia,$usr,$fec);
		if($reply > 0)
			$_SESSION["costos"]["message"] = "Compania registrada con el folio $reply";
		else
			$_SESSION["costos"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			$reply = UpdateData($mon,$fef,$stt,$cve,$usr,$fec);
			if($reply == 0)
				$_SESSION["costos"]["message"] = "Compania guardada sin cambios.";
			else
				if($reply == 1)
					$_SESSION["costos"]["message"] = "Cambios realizados correctamente.";
				else
					$_SESSION["costos"]["message"] = $reply;
		}else{
			if(isset($_GET["u"])){
				$_SESSION["mng_compania"]["costos"] = serialize(searchData($_GET["u"]));
			}
		}
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_compania_costos.phtml");
function InsertData($mon,$fef,$stt,$cia,$usr,$fec){
	$conn = getConnection();
	$sql = "insert into cat_costo (mon_costo, cve_cia, fec_efe, stt_costo, usr_cap, fec_cap) 
			values ('$mon','$cia','$fef','$stt','$usr','$fec')";
	//echo "ins: ".$sql."<br>";
	$stmt = $conn->prepare($sql);
	//$stmt->bind_param("idissss", $cve_cos, $monto, $cve_cia, $fecefe, $stt, $_SESSION["userinfo"]["username"], $currDate = date("Y-m-d H:i:s"));
	$rc = $stmt->execute();
	if(false===$rc){
		$ret = $stmt->error."  $mon, $fef, $stt"; 
	}else{
		$ret = $stmt->insert_id;
		$dis = DisablePrevReg($ret,$cia,$usr,$fec);
	}
	return $ret;
}
function UpdateData($mon,$fef,$stt,$cve,$usr,$fec){
	$conn = getConnection();
	$sql="update cat_costo set mon_costo = '$mon', fec_efe = '$fef', stt_costo = '$stt', usr_cap='$usr', fec_cap='$fec' where cve_costo = '$cve'";
	//echo "upd: ".$sql."<br>";
	$stmt = $conn->prepare($sql);
	//$stmt->bind_param("idssi", $cve_cia, $monto, $fecefe, $stt, $cve_costo);
	$rc = $stmt->execute();
	if(false===$rc){
		$ret = $stmt->error; 
	}else{
		if($stmt->affected_rows > 0){
			$ret = "1";
		}else{
			$ret = "0";
		}
	}
	return $ret;
}
function searchData($dat){
	$conn = getConnection();
	$sql = "select * from cat_costo where cve_costo='$dat'";
	$result = $conn->query($sql);
	$tmp = array();
	if($row = $result->fetch_assoc()) {		
		$tmp["cve_costo"] = $row["cve_costo"];
		$tmp["monto"] = $row["mon_costo"];
		$tmp["fec_efe"] = $row["fec_efe"];
		$tmp["stt_costo"] = $row["stt_costo"];		
		
	}
	$conn->close();
	return $tmp;
}

function DisablePrevReg($id,$cia,$usr,$fec){
	$conn = getConnection();
	$sql="update cat_costo set stt_costo = 'I', usr_cap='$usr', fec_cap='$fec' where cve_cia='$cia' and stt_costo='A' and cve_costo <> '$id'";
	//echo "dis: ".$sql."<br>";
	$stmt = $conn->prepare($sql);
	//$stmt->bind_param("idssi", $cve_cia, $monto, $fecefe, $stt, $cve_costo);
	$rc = $stmt->execute();
	if(false===$rc){
		$ret = $stmt->error; 
	}else{
		if($stmt->affected_rows > 0){
			$ret = "1";
		}else{
			$ret = "0";
		}
	}
	return $ret;
}


?>