<?php
if(isset($_POST["param_idlinea"])){
	$idlinea = $_POST["param_idlinea"];
	if($idlinea > 0){
		include "cnx.php";	
		$conn = getConnection();
		$sql = "select * from cat_cnd 
		where stt_cnd='A' 
		and cve_lin='$idlinea' 
		and cve_cnd not in (select cve_cnd from cat_uni where cve_lin='$idlinea')";
		$result = $conn->query($sql);
		$arrDrv = array();
		if ($result->num_rows > 0) {
			echo "<option value=''>- Selecciona conductor -</option>";
			while($row = $result->fetch_assoc()) {				
				echo "<option value='".$row["cve_cnd"]."'>".$row["nombre"]."</option>";
			}
		} else {
			echo "<option value=''>No hay conductores disponibles!</option>";
		}
		$conn->close();
	}else{
		echo "<option value=''>- Conductor -</option>";
	}
}