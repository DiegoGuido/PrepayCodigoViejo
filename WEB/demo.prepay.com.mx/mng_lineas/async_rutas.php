<?php
session_start();
if(isset($_POST["param_linea"])){
	include "../util_list.php";
	$idruta	=$_POST["param_ruta"];
	$origen	=$_POST["param_origen"];
	$destino=$_POST["param_destino"];
	$kms	=$_POST["param_kms"];
	$tiempo	=$_POST["param_tiempo"];
	$stt	=$_POST["param_stt"];
	$idLinea	=$_POST["param_linea"];
	
	$trx	=$_POST["param_trx"];
	if($idLinea > 0){
		include "../cnx.php";	
		$conn = getConnection();
		
		if($trx=="add"){
			$sql = "INSERT INTO cat_rut ( origen, destino, tiempo, kms, cve_lin, stt_rut, usr_cap, fec_cap) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssssisss", $origen, $destino, $tiempo, $kms, $idLinea, $stt, $_SESSION["userinfo"]["username"], $currDate = date("Y-m-d H:i:s"));
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
			}else{
				$ret = $stmt->insert_id;
			}
		}else{
			if($trx=="update"){
				$sql="update cat_rut set origen=?, destino=?, tiempo=?, kms=?, cve_lin=?, stt_rut=? where cve_rut = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("ssssisi", $origen, $destino, $tiempo, $kms,  $idLinea, $stt, $idruta);
				$rc = $stmt->execute();
				if(false===$rc){
					$ret = $stmt->error; 
				}else{
					if($stmt->affected_rows > 0){
						$ret = "Ruta actualizada correctamente";
					}else{
						$ret = "Ruta guardada sin cambios";
					}
				}
			}
		}
		$stmt->close();
		$conn->close();
		$arrRut = listAllRoutes($idLinea);
						$arrRut = listAllRoutes($idLinea);
						$table = "<table class='table table-bordered table-hover'>";
						$table .= "<thead class='bill-header cs' style='color: rgb(29,42,159);'>";
						$table .= "<tr style='color: rgb(229,230,242);background-color: rgb(24,165,88);'><th id='trs-hd' class='col-lg-1' >Folio</th><th id='trs-hd' class='col-lg-1' >Origen</th><th id='trs-hd' class='col-lg-1' >Destino</th><th id='trs-hd' class='col-lg-1' >Status</th><th id='trs-hd' class='col-lg-1' >Editar</th></tr>";
						$table .= "</thead>";
						$table .= "<tbody>";
						$h = false;
						foreach($arrRut as $rut){
							$table .= "<tr>";
							$table .= "<td data-label='Folio'>	".$rut["cve_rut"]."</td>"; 
							$table .= "<td data-label='Origen'>	".$rut["origen"]."</td>";
							$table .= "<td data-label='Destino'>".$rut["destino"]."</td>";
							$table .= "<td data-label='Estatus'><img src='assets/img/".($rut["stt_rut"]=="A"?"on":"off").".png' height='40' /></td>";
							$table .= "<td data-label='Editar'>";
							$table .= "<a onclick='document.documentElement.scrollTop = 0;'><img src='assets/img/editar.png' height='20' onclick='fillFields(".$rut["cve_rut"].")' /></a>";
							$table .= "<input type='hidden' id='dataroute".$rut["cve_rut"]."' value='".($rut["cve_rut"]."-".$rut["origen"]."-".$rut["destino"]."-".$rut["kms"]."-".$rut["tiempo"]."-".$idLinea."-".$rut["stt_rut"])."' />";
							$table .= "</td>";
							$table .= "</tr>";
						}
						$table .= "</tbody>";
						$table .= "</table>";
						echo $ret."|".$table;
	}else{
		echo "<option value=''>- Conductor -</option>";
	}
}
