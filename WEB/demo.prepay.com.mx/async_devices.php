<?php
if(isset($_POST["param_idlinea"])){
	$idlinea = $_POST["param_idlinea"];
	if($idlinea > 0){
		include "cnx.php";	
		$conn = getConnection();
		$sql = "select a.* 
		from cat_dis a
		where a.stt_dis='A' 
		and a.cve_cia=(select cve_cia from cat_lin b where b.cve_lin='$idlinea')
		and a.imei not in (select imei from vw_rel_dis c where c.cve_cia=a.cve_cia and tip_rel=2)";
		$result = $conn->query($sql);
		$arrDrv = array();
		if ($result->num_rows > 0) {
			echo "<option value=''>- Dispositivos Disponibles -</option>";
			while($row = $result->fetch_assoc()) {				
				echo "<option value='".$row["imei"]."'>".$row["des_dis"]."</option>";
			}
		} else {
			echo "<option value=''>No hay dispositivos disponibles!</option>";
		}
		$conn->close();
	}else{
		echo "<option value=''>- Dispositivos Disponibles -</option>";
	}
}

if(isset($_POST["param_idpc"])){
	$idpc = $_POST["param_idpc"];
	if($idpc > 0){
		include "cnx.php";	
		$conn = getConnection();
		$sql = "select a.* 
		from cat_dis a
		where a.stt_dis='A' 		
		and a.cve_cia=(select b.cve_cia from cat_lin b, cat_uni c where b.cve_lin=c.cve_lin and c.cve_uni='$idpc')
		and a.imei not in (select imei from vw_rel_dis d where d.cve_cia=a.cve_cia and tip_rel=1)";
		$result = $conn->query($sql);
		$arrDrv = array();
		if ($result->num_rows > 0) {
			echo "<option value=''>- Dispositivos Disponibles -</option>";
			while($row = $result->fetch_assoc()) {				
				echo "<option value='".$row["imei"]."'>".$row["des_dis"]."</option>";
			}
		} else {
			echo "<option value=''>No hay dispositivos disponibles!</option>";
		}
		$conn->close();
	}else{
		echo "<option value=''>- Dispositivos Disponibles -</option>";
	}
}