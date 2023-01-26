<?
session_start();
if(isset($_SESSION["userinfo"])){
	include "../cnx.php";
	$conn = getConnection();
	//$rut = $_POST["idRuta"];
	$rut = $_SESSION["mngrutas"]["idRuta"];
	$mod = $_POST["modo"];	
	$stt = empty($_POST["status"])?"A":$_POST["status"];		
	$usr = $_SESSION["userinfo"]["username"];
	$fec = date("Y-m-d H:i:s");
	$_SESSION["mng_rutas"]["modos"]="";
	$_SESSION["modos"]["message"]="";
	//echo "rut: ".$rut."<br>";
	
	
	if(isset($_POST["btnAgregar"])){
		//echo "aqui estoy 1";
		$reply = InsertData($rut,$mod,$stt,$usr,$fec);
		if($reply > 0)
			$_SESSION["modos"]["message"] = "Modo registrado con el folio $reply";
		else
			$_SESSION["modos"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			$reply = UpdateData($rut,$mod,$stt,$usr,$fec);
			if($reply == 0)
				$_SESSION["modos"]["message"] = "Tipo guardado sin cambios.";
			else
				if($reply == 1)
					$_SESSION["modos"]["message"] = "Cambios realizados correctamente.";
				else
					$_SESSION["modos"]["message"] = $reply;
		}else{
			if(isset($_GET["d1"])){
				$_SESSION["mng_rutas"]["modos"] = serialize(searchData($_GET["d1"],$_GET["d2"]));
			}
		}
	}
}else{
	header("Location: ../");
}
header("Location: ../mng_rutas_modos.phtml");
function InsertData($rut,$mod,$stt,$usr,$fec){
	$conn = getConnection();	
	$sql = "insert into cat_rut_mod (cve_rut,cve_mod,stt_rut_mod,usr_cap,fec_cap) 
			values ('$rut','$mod','$stt','$usr','$fec')";
	//echo "ins: ".$sql."<br>";
	$stmt = $conn->prepare($sql);	
	$rc = $stmt->execute();
	if(false===$rc){
		$ret = $stmt->error; 
	}else{
		$ret = $stmt->insert_id;		
	}
	return $ret;
}
function UpdateData($rut,$mod,$stt,$usr,$fec){
	$conn = getConnection();
	$sql="update cat_rut_mod set cve_mod='$mod', stt_rut_mod='$stt', fec_cap='$fec' where cve_rut = '$rut' and cve_mod='$mod'";
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
function searchData($dat1,$dat2){
	//echo "dat1: ".$dat1." dat2: ".$dat2;
	$conn = getConnection();
	$sql = "select a.cve_rut,a.cve_mod,c.des_mod,a.fec_cap,a.stt_rut_mod
			from cat_rut_mod a, cat_mod c
			where a.cve_mod=c.cve_mod			
			and a.cve_rut='$dat1'
			and a.cve_mod='$dat2'";
	//echo "sql".$sql;
	$result = $conn->query($sql);
	$tmp = array();
	if($row = $result->fetch_assoc()) {		
		$tmp["cve_mod"] = $row["cve_mod"];
		$tmp["des_mod"] = $row["des_mod"];		
		$tmp["stt_rut_mod"] = $row["stt_rut_mod"];				
		$tmp["cve_rut"] = $row["cve_rut"];
	}
	$conn->close();
	return $tmp;
}


?>