<?php
if(isset($_POST["param_imei"]) && isset($_POST["param_usr"])){
	$imei = $_POST["param_imei"];
	$usr  = $_POST["param_usr"];
	//echo $imei.$usr;
	if(!empty($imei) && !empty($usr)){
		include "cnx.php";	
		$conn = getConnection();
		$sql= "select a.bal_ter
		from vw_ter_bal a
		where a.imei='$imei'
		and a.usr_cap='$usr'";
		//echo $sql;
		$result = $conn->query($sql);		
		if ($result->num_rows > 0) {			
			while($row = $result->fetch_assoc()) {				
				echo $row["bal_ter"];
			}
		} else {
			echo "0";
		}
		$conn->close();
	}else{
		echo "Datos Invalidos";
	}
}

if(isset($_POST["param_ptovta"])){
	$ptovta = $_POST["param_ptovta"];	
	if($ptovta > 0){
		include "cnx.php";	
		$conn = getConnection();
		$sql= "select cve_rel,imei
		from sis_rel_dis 
		where cve_rel='$ptovta'
		and tip_rel='2'
		and stt_rel='A'";
		//echo $sql;
		$result = $conn->query($sql);		
		if ($result->num_rows > 0) {			
			while($row = $result->fetch_assoc()) {				
				echo $row["imei"];
			}
		} else {
			echo "IMEI INVALIDO";
		}
		$conn->close();
	}else{
		echo "";
	}
}
