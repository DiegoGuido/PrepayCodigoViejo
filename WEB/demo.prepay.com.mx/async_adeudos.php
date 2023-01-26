<?php
if (isset($_POST["param_tipo"]) && $_POST["param_tipo"]=="bal"){
	if(isset($_POST["param_imei"]) && isset($_POST["param_usr"]) && isset($_POST["param_fec"])){
		$imei 	= $_POST["param_imei"];
		$usr 	= $_POST["param_usr"];
		$fec 	= $_POST["param_fec"];
		//echo $imei.$usr.$fec;
		if(!empty($imei) && !empty($usr) && !empty($fec)){
			include "cnx.php";	
			$conn = getConnection();
			$sql= "select a.bal_taq
			from vw_adeudo_taquilla a
			where a.imei='$imei'
			and a.usr_cap='$usr'
			and a.fecha='$fec'";
			//echo $sql;
			$result = $conn->query($sql);		
			if ($result->num_rows > 0) {			
				while($row = $result->fetch_assoc()) {				
					echo $row["bal_taq"];
				}
			} else {
				echo "0";
			}
			$conn->close();
		}else{
			echo "Datos Invalidos";
		}
	}
}
if (isset($_POST["param_tipo"]) && $_POST["param_tipo"]=="dev"){
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
}
if (isset($_POST["param_tipo"]) && $_POST["param_tipo"]=="fec"){
	//echo "imei: ".$_POST["param_imei"];
	if(isset($_POST["param_imei"])){
		$imei = $_POST["param_imei"];
		if(!empty($imei)){
			include "cnx.php";	
			$conn = getConnection();
			$sql = "select distinct(fecha),imei from vw_adeudo_taquilla where bal_taq > 0 and imei ='$imei'";
			$result = $conn->query($sql);
			$arrDrv = array();
			if ($result->num_rows > 0) {
				echo "<option value=''>- Seleccione Fecha -</option>";
				while($row = $result->fetch_assoc()) {		
					echo "<option value='".$row["fecha"]."'>".$row["fecha"]."</option>";
				}
			} else {
				echo "<option value=''>No hay fechas disponibles!1</option>";
			}
			$conn->close();
		}else{
			echo "<option value=''>No hay fechas disponibles!2</option>";
		}
	}
}

if (isset($_POST["param_tipo"]) && $_POST["param_tipo"]=="usr"){
	//echo "imei: ".$_POST["param_imei"];
	if(isset($_POST["param_imei"]) && isset($_POST["param_fec"])){
		$imei = $_POST["param_imei"];
		$fec = $_POST["param_fec"];
		if(!empty($imei) && !empty($fec)){
			include "cnx.php";	
			$conn = getConnection();
			$sql = "select a.usr_cap,b.nombre from vw_adeudo_taquilla a, cat_usr b where a.usr_cap=b.usr and a.imei ='$imei' and a.fecha='$fec'";
			$result = $conn->query($sql);
			$arrDrv = array();
			if ($result->num_rows > 0) {
				echo "<option value=''>- Seleccione Usuario -</option>";
				while($row = $result->fetch_assoc()) {		
					echo "<option value='".$row["usr_cap"]."'>".$row["nombre"]."</option>";
				}
			} else {
				echo "<option value=''>No hay Usuarios disponibles!1</option>";
			}
			$conn->close();
		}else{
			echo "<option value=''>No hay Usuarios disponibles!2</option>";
		}
	}
}