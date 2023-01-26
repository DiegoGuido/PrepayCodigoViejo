<?
session_start();
if(isset($_SESSION["userinfo"])){
	include "../cnx.php";
	$conn = getConnection();
	$tip = $_POST["tipo"];
	$pct = $_POST["porcentaje"];
	$fef = $_POST["fecefe"];
	$max = $_POST["maxmov"];
	$tim = $_POST["timeout"];
	$stt = empty($_POST["status"])?"A":$_POST["status"];
	$cve = $_POST["idTipo"];
	$cia = $_SESSION["compania"]["idCompania"];
	$usr = $_SESSION["userinfo"]["username"];
	$fec = date("Y-m-d H:i:s");
	$_SESSION["mng_compania"]["tipos"]="";
	$_SESSION["tipos"]["message"]="";
	//echo "pct: ".$pct."<br>";
	
	if(isset($_POST["btnAgregar"])){
		//echo "aqui estoy 1";
		$reply = InsertData($tip,$pct,$fef,$stt,$cia,$usr,$fec,$max,$tim);
		if($reply > 0)
			$_SESSION["tipos"]["message"] = "Tipo registrado con el folio $reply";
		else
			$_SESSION["tipos"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			$reply = UpdateData($tip,$pct,$fef,$stt,$usr,$fec,$cve,$max,$tim);
			if($reply == 0)
				$_SESSION["tipos"]["message"] = "Tipo guardado sin cambios.";
			else
				if($reply == 1)
					$_SESSION["tipos"]["message"] = "Cambios realizados correctamente.";
				else
					$_SESSION["tipos"]["message"] = $reply;
		}else{
			if(isset($_GET["u"])){
				$_SESSION["mng_compania"]["tipos"] = serialize(searchData($_GET["u"]));
			}
		}
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_compania_tipos.phtml");
function InsertData($tip,$pct,$fef,$stt,$cia,$usr,$fec,$max,$tim){
	$conn = getConnection();	
	$sql = "insert into sis_pct_tip_cia (cve_tip, cve_cia, pct_desc, stt_pct, fec_efe, usr_cap, fec_cap, maxmov, timeout) 
			values ('$tip','$cia','$pct','$stt','$fef','$usr','$fec','$max','$tim')";
	//echo "ins: ".$sql."<br>";
	$stmt = $conn->prepare($sql);	
	$rc = $stmt->execute();
	if(false===$rc){
		$ret = $stmt->error."  $mon, $fef, $stt"; 
	}else{
		$ret = $stmt->insert_id;
		//$dis = DisablePrevReg($ret,$cia,$tip,$usr,$fec);
	}
	return $ret;
}
function UpdateData($tip,$pct,$fef,$stt,$usr,$fec,$cve,$max,$tim){
	$conn = getConnection();
	$sql="update sis_pct_tip_cia set cve_tip = '$tip', pct_desc='$pct',fec_efe = '$fef', stt_pct = '$stt', usr_cap='$usr', fec_cap='$fec', maxmov='$max', timeout='$tim' where cve_reg = '$cve'";
	//echo "upd: ".$sql."<br>";
	$stmt = $conn->prepare($sql);	
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
	$sql = "select a.cve_reg,a.cve_tip,b.des_tip,a.pct_desc,a.fec_efe,a.stt_pct,a.maxmov,a.timeout
			from sis_pct_tip_cia a, cat_tip_trj b
			where a.cve_tip=b.cve_tip
			and cve_reg='$dat'";
	$result = $conn->query($sql);
	$tmp = array();
	if($row = $result->fetch_assoc()) {		
		$tmp["cve_reg"] = $row["cve_reg"];
		$tmp["cve_tip"] = $row["cve_tip"];
		$tmp["des_tip"] = $row["des_tip"];
		$tmp["pct_desc"] = $row["pct_desc"];
		$tmp["fec_efe"] = $row["fec_efe"];		
		$tmp["stt_pct"] = $row["stt_pct"];
		$tmp["maxmov"] = $row["maxmov"];
		$tmp["timeout"] = $row["timeout"];
		
	}
	$conn->close();
	return $tmp;
}
function DisablePrevReg($id,$cia,$tip,$usr,$fec){
	$conn = getConnection();
	$sql="update sis_pct_tip_cia set stt_pct = 'I', usr_cap='$usr', fec_cap='$fec' where cve_cia='$cia' and cve_tip='$tip' and 'stt_pct='A' and cve_reg <> '$id'";
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