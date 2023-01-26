<?
session_start();
if(isset($_SESSION["userinfo"])){
	include "../cnx.php";
	$conn = getConnection();
	$mon = $_POST["monto"];
	$fef = $_POST["fecefe"];
	$stt = empty($_POST["status"])?"A":$_POST["status"];
	$cve = $_POST["idTope"];
	$cia = $_SESSION["compania"]["idCompania"];
	$usr = $_SESSION["userinfo"]["username"];
	$fec = date("Y-m-d H:i:s");
	$_SESSION["mng_compania"]["topes"]="";
	$_SESSION["topes"]["message"]="";
	//echo "stt: ".$stt."<br>";
	
	if(isset($_POST["btnAgregar"])){
		//echo "aqui estoy 1";
		$reply = InsertData($mon,$fef,$stt,$cia,$usr,$fec);
		if($reply > 0)
			$_SESSION["topes"]["message"] = "Tope registrado con el folio $reply";
		else
			$_SESSION["topes"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			$reply = UpdateData($mon,$fef,$stt,$cve,$usr,$fec);
			if($reply == 0)
				$_SESSION["topes"]["message"] = "Tope guardado sin cambios.";
			else
				if($reply == 1)
					$_SESSION["topes"]["message"] = "Cambios realizados correctamente.";
				else
					$_SESSION["topes"]["message"] = $reply;
		}else{
			if(isset($_GET["u"])){
				$_SESSION["mng_compania"]["topes"] = serialize(searchData($_GET["u"]));
			}
		}
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_compania_topes.phtml");

function InsertData($mon,$fef,$stt,$cia,$usr,$fec){
	$conn = getConnection();
	$sql = "insert into cat_top (mon_top, cve_cia, fec_efe, stt_top, usr_cap, fec_cap) 
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
	return $tmp;
}
function UpdateData($mon,$fef,$stt,$cve,$usr,$fec){
	$conn = getConnection();
	$sql="update cat_top set mon_top = '$mon', fec_efe = '$fef', stt_top = '$stt', usr_cap='$usr', fec_cap='$fec' where cve_top = '$cve'";
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
	$sql = "select * from cat_top where cve_top='$dat'";
	$result = $conn->query($sql);
	$tmp = array();
	if($row = $result->fetch_assoc()) {		
		$tmp["cve_top"] = $row["cve_top"];
		$tmp["mon_top"] = $row["mon_top"];
		$tmp["fec_efe"] = $row["fec_efe"];
		$tmp["stt_top"] = $row["stt_top"];		
		
	}
	$conn->close();
	return $tmp;
}

function DisablePrevReg($id,$cia,$usr,$fec){
	$conn = getConnection();
	$sql="update cat_top set stt_top = 'I', usr_cap='$usr', fec_cap='$fec' where cve_cia='$cia' and stt_top='A' and cve_top <> '$id'";
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