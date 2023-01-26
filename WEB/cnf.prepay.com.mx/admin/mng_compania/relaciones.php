<?
session_start();
if(isset($_SESSION["userinfo"])){
	include "../cnx.php";
	$conn = getConnection();
	$rel = $_POST["relacion"];
	$stt = empty($_POST["status"])?"A":$_POST["status"];
	$cve = $_POST["idRelacion"];
	$cia = $_SESSION["compania"]["idCompania"];
	$usr = $_SESSION["userinfo"]["username"];
	$fec = date("Y-m-d H:i:s");
	$_SESSION["mng_compania"]["relaciones"]="";
	$_SESSION["relaciones"]["message"]="";
	//echo "pct: ".$pct."<br>";
	
	if(isset($_POST["btnAgregar"])){
		//echo "aqui estoy 1";
		$reply1 = InsertData($cia,$rel,$stt,$usr,$fec);
		$reply2 = InsertData($rel,$cia,$stt,$usr,$fec);	
		$reply = "1: ".$reply1." 2: ".$reply2;
		if($reply1 > 0 && $reply2 > 0)
			$_SESSION["relaciones"]["message"] = "Relacion registrada con el folio $reply";
		else			
			$_SESSION["relaciones"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			$reply1 = UpdateData($cia,$rel,$stt,$usr,$fec,$cve);
			$reply2 = UpdateData($rel,$cia,$stt,$usr,$fec,$cve);
			$reply = "1: ".$reply1." 2: ".$reply2;
			if($reply1 == 0 && $reply2 == 0)
				$_SESSION["relaciones"]["message"] = "Relacion guardada sin cambios.";
			else
				if($reply1 == 1 && $reply2 == 1)
					$_SESSION["relaciones"]["message"] = "Cambios realizados correctamente.";
				else					
					$_SESSION["relaciones"]["message"] = $reply;
		}else{
			if(isset($_GET["u"])){
				$_SESSION["mng_compania"]["relaciones"] = serialize(searchData($_GET["u"]));
			}
		}
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_compania_relaciones.phtml");
function InsertData($cia,$rel,$stt,$usr,$fec){
	$conn = getConnection();	
	$sql = "insert into cat_cia_rel (cve_cia, cve_cia_rel, stt_rel, usr_cap, fec_cap) 
			values ('$cia','$rel','$stt','$usr','$fec')";
	//echo "ins: ".$sql."<br>";
	$stmt = $conn->prepare($sql);	
	$rc = $stmt->execute();
	if(false===$rc){
		$ret = $stmt->error; 
	}else{
		$ret = $stmt->insert_id;
		$dis1 = DisablePrevReg($ret,$cia,$rel,$usr,$fec);
		//$dis2 = DisablePrevReg($ret,$rel,$cia,$usr,$fec);
	}
	return $ret;
}
function UpdateData($cia,$rel,$stt,$usr,$fec,$cve){
	$conn = getConnection();
	$sql="update cat_cia_rel set stt_rel = '$stt', usr_cap='$usr', fec_cap='$fec' where cve_cia='$cia' and cve_cia_rel='$rel'";
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
	$sql = "select a.cve_reg,a.cve_cia_rel,b.des_cia,a.stt_rel 
			from cat_cia_rel a, cat_cia b 
			where a.cve_cia_rel=b.cve_cia 
			and cve_reg='$dat'";
	//echo $sql;
	$result = $conn->query($sql);
	$tmp = array();
	if($row = $result->fetch_assoc()) {		
		$tmp["cve_reg"] = $row["cve_reg"];
		$tmp["cve_cia_rel"] = $row["cve_cia_rel"];
		$tmp["des_cia"] = $row["des_cia"];		
		$tmp["stt_rel"] = $row["stt_rel"];
		
	}
	$conn->close();
	return $tmp;
}
function DisablePrevReg($id,$cia,$rel,$usr,$fec){
	$conn = getConnection();
	$sql="update cat_cia_rel set stt_rel = 'I', usr_cap='$usr', fec_cap='$fec' 
		 where cve_cia='$cia' and cve_cia_rel='$rel' and stt_rel='A' and cve_reg <> '$id'";
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