<?php
session_start();
if(isset($_POST["param_idruta"])){
	$cve_tar = $_POST["param_idtarifa"];
	$cve_rut = $_POST["param_idruta"];
	$trx = $_POST["param_trx"];
	if($cve_rut > 0){
		include "cnx.php";
		$trx	= isset($_POST["param_trx"])?$_POST["param_trx"]:"";
		$conn = getConnection();
		if(!empty($trx)){
			$km_ini = $_POST["param_km_ini"];
			$km_fin = $_POST["param_km_fin"];
			$monto = $_POST["param_monto"];
			$stt = $_POST["param_stt"];
			$pred = $_POST["param_pred"];
			if($trx=="add"){
				$sql = "INSERT INTO cat_tar ( cve_rut, km_ini, km_fin, monto, stt_tar, usr_cap, fec_cap, stt_pred) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("idddssss", $cve_rut, $km_ini, $km_fin, $monto, $stt, $_SESSION["userinfo"]["username"], $currDate = date("Y-m-d H:i:s"), $pred);
				$rc = $stmt->execute();
				if(false===$rc){
					$ret = $stmt->error."  $cve_rut, $km_ini, $km_fin, $monto, $stt, $pred"; 
				}else{
					$cve_tar = $stmt->insert_id;
					$ret = $cve_tar;
				}
			}else{
				if($trx=="update"){
					$sql="update cat_tar set cve_rut = ?, km_ini = ?, km_fin = ?, monto = ?, stt_tar = ?, stt_pred = ?  where cve_tar = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("idddssi", $cve_rut, $km_ini, $km_fin, $monto, $stt, $pred, $cve_tar);
					$rc = $stmt->execute();
					if(false===$rc){
						$ret = $stmt->error; 
					}else{
						if($stmt->affected_rows > 0){
							$ret = "Tarifa actualizada correctamente";
						}else{
							$ret = "Tarifa guardada sin cambios";
						}
					}
				}
			}
			if ($pred == "Y"){
				$sql="update cat_tar set stt_pred='N' where cve_tar != '$cve_tar' and cve_rut='$cve_rut'";
				$stmt = $conn->prepare($sql);				
				$rc = $stmt->execute();
			}
		}
		$sql = "select * from cat_tar where cve_rut='$cve_rut'";
		$result = $conn->query($sql);		
		$table = "<table class='table table-bordered table-hover'>";
		$table .= "<thead class='bill-header cs' style='color: rgb(29,42,159);'>";
		$table .= "<tr style='color: rgb(229,230,242);background-color: rgb(24,165,88);'><th id='trs-hd' class='col-lg-1' >Folio</th><th id='trs-hd' class='col-lg-1' >KM Inicial</th><th id='trs-hd' class='col-lg-1' >Km Final</th><th id='trs-hd' class='col-lg-1' >Monto</th><th id='trs-hd' class='col-lg-1' >Status</th><th id='trs-hd' class='col-lg-1' >Pred</th><th id='trs-hd' class='col-lg-1' >...</th></tr>";
		$table .= "</thead>";
		$table .= "<tbody>";
		if ($result->num_rows > 0) {
		
			while($row = $result->fetch_assoc()) {
				
				$table .= "<tr>";
				$table .= "<td id='trs-hd' class='col-lg-1' data-label='Folio'>	".$row["cve_tar"]."</td>"; 
				$table .= "<td data-label='KM INI'>	".$row["km_ini"]."</td>";
				$table .= "<td data-label='KM FIN'> ".$row["km_fin"]."</td>";
				$table .= "<td data-label='MONTO'> ".$row["monto"]."</td>";
				$table .= "<td data-label='Estatus'><img src='http://cnf.prepay.com.mx/assets/img/".($row["stt_tar"]=="A"?"on":"off").".png' height='40' /></td>";
				$table .= "<td data-label='Pred'><img src='http://cnf.prepay.com.mx/assets/img/".($row["stt_pred"]=="Y"?"on":"off").".png' height='40' /></td>";
				$table .= "<td data-label='Editar'>";
				$table .= "<img src='http://cnf.prepay.com.mx/assets/img/editar.png' height='20' onclick='fillFields(".$row["cve_tar"].")' />";
				$table .= "<input type='hidden' id='datarate".$row["cve_tar"]."' value='".($row["cve_tar"]."-".$row["cve_rut"]."-".$row["km_ini"]."-".$row["km_fin"]."-".$row["monto"]."-".$row["stt_tar"]."-".$row["stt_pred"])."' />";
				$table .= "</td>";
				$table .= "</tr>";
			}
		}
		$table .= "</tbody>";
		$table .= "</table>";
			echo $ret."|".$table;
		$conn->close();
	}else{
		echo "<option value=''>- Conductor -</option>";
	}
}