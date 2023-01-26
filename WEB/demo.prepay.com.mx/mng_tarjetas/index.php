<?php
session_start();
include "../cnx.php";


if(isset($_POST["btnActualizar"])){
		
		$id  = $_POST["trjid"];
		$stt = $_POST["trjstt"];
		$vig = $_POST["trjvig"];
		
		$reply = updateData($id,$stt,$vig);
		if($reply == 0){
			$_SESSION["mng_tarjetas"]["message"] = "GuardadO sin cambios.";
		}else{
			if($reply >= 1){
				$_SESSION["mng_tarjetas"]["message"] = "Cambios realizados correctamente.";
				$_SESSION["mng_tarjetas"]["tarjetas"] = serialize(searchById($id));
			}else{
				$_SESSION["mng_tarjetas"]["message"] = $reply;
			}
		}
}else{
		if(isset($_GET["u"])){
			//echo "get";
			$_SESSION["mng_tarjetas"]["tarjetas"] = serialize(searchById($_GET["u"]));
		}
}


header("Location: ../mng_tarjetas.phtml");


function searchById($id){			
			$conn = getConnection();
			$sql = "select * from sis_reg_trj where cve_trj='$id'";
			//echo "sql: ".$sql."<br>";
			$result = $conn->query($sql);			
			$tmp = array();
			if($row = $result->fetch_assoc()) {					
				$tmp["cve_trj"] = $row["cve_trj"];
				$tmp["vigencia"] = $row["vigencia"];
				$tmp["stt_trj"] = $row["stt_trj"];
				//echo "dat: ".$tmp["cve_trj"]."-".$tmp["vigencia"]."-".$tmp["stt_trj"];
			}		
			$conn->close();
			return $tmp;
}

function updateData($id,$stt,$vig){ 
	$conn = getConnection();
	$usr = $_SESSION["userinfo"]["username"];
	$fec = $currDate = date("Y-m-d H:i:s");
	$sql = "update sis_reg_trj set stt_trj='$stt', vigencia='$vig 23:59:59', usr_cap='$usr', fec_cap='$fec' where cve_trj='$id'";
	//echo "sql: ".$sql."</br>";
	$stmt = $conn->prepare($sql);
	$rc = $stmt->execute();
	if(false===$rc){
		$ret = $stmt->error; 
	}else{	   
		$ret = $stmt->affected_rows;				   
	}
	$stmt->close();	
	return $ret;
}
?>