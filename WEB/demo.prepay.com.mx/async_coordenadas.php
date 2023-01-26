<?php
session_start();
if(isset($_POST["param_coordenada"])){
	include "util_list.php";
	$coordValue = json_decode(base64_decode($_POST["param_coordenada"]), true);
	$cve_crd 	= $coordValue["idcoordenada"];
	$cve_rut 	= $coordValue["idruta"];
	$trx = $_POST["param_trx"];
	if($cve_rut > 0){
		include "cnx.php";
		$trx	= isset($_POST["param_trx"])?$_POST["param_trx"]:"";
		$conn = getConnection();
		if(!empty($trx)){
			
			
			
			$des_crd 	= $coordValue["desccoordinates"];
			$lat 		= $coordValue["latitude"];
			$lon 		= $coordValue["longitude"];
			$geocerca 	= $coordValue["geofence"];
			$tip_crd 	= $coordValue["coordinatetype"];
			$tar1	 	= $coordValue["tar1"];
			$tar2	 	= $coordValue["tar2"];
			$stt_crd 	= $coordValue["statuscoordenada"];
			$arrTipo = array("O"=>"Origen", "D"=>"Destino");
			if($trx=="add"){
				if(in_array($tip_crd, array("O","D")) && $stt_crd=='A'){
					$sqlExists="select count(*) as existe from sis_crd_rut where tip_crd = '$tip_crd' and stt_crd = 'A' and cve_rut='$cve_rut'";
					$existe=getDataFromTable($conn, $sqlExists)[0]["existe"];
				}
				if($existe > 0){
					$ret = "Ya existe una coordenada de tipo ".$arrTipo[$tip_crd]." activa."; 
				}else{
					$sql = "INSERT INTO sis_crd_rut ( cve_rut, des_crd, lat, lon, geocerca, tip_crd, stt_crd, usr_cap, fec_cap, tar1, tar2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("isssdssssdd", $cve_rut, $des_crd, $lat, $lon, $geocerca, $tip_crd, $stt_crd , $_SESSION["userinfo"]["username"], $currDate = date("Y-m-d H:i:s"),$tar1,$tar2);
					$rc = $stmt->execute();
					if(false===$rc){
						$ret = $stmt->error."  -- $cve_rut, $des_crd, $lat, $lon, $geocerca, $tip_crd, $stt_crd, $tar1, $tar2"; 
					}else{
						$ret = $stmt->insert_id;
					}
				}
			}else{
				if($trx=="update"){
					if(in_array($tip_crd, array("O","D")) && $stt_crd=='A'){
						$sqlExists="select count(*) as existe, group_concat(cve_crd) as cve_crd from sis_crd_rut where tip_crd = '$tip_crd' and stt_crd = 'A' and cve_rut='$cve_rut'";
						$data_exist = getDataFromTable($conn, $sqlExists)[0];
						if($data_exist["cve_crd"]==$cve_crd){
							$existe=0;
						}else{
							$existe=$data_exist["existe"];
						}
						
					}
					if($existe > 0){
						$ret = "Ya existe una coordenada de tipo ".$arrTipo[$tip_crd]." activa."; 
					}else{
						$sql="update sis_crd_rut set des_crd= ?, lat= ?, lon= ?, geocerca= ?, tip_crd= ?, stt_crd= ?, tar1= ?, tar2= ? where cve_crd = ?";
						$stmt = $conn->prepare($sql);
						$stmt->bind_param("isssdssidd", $cve_rut, $des_crd, $lat, $lon, $geocerca, $tip_crd, $stt_crd , $cve_crd, $tar1, $tar2);
						$rc = $stmt->execute();
						if(false===$rc){
							$ret = $stmt->error; 
						}else{
							
								$ret = $stmt->affected_rows > 0;
							
						}
					}
				}
			}
		}
		$conn->close();
		
			
		$table = "<table class='table table-bordered table-hover'>";
		$table .= "<thead class='bill-header cs' style='color: rgb(29,42,159);'>";
		$table .= "<tr style='color: rgb(229,230,242);background-color: rgb(24,165,88);'><th id='trs-hd' class='col-lg-1' >Descripcion</th>";		
		$table .= "<th id='trs-hd' class='col-lg-1' >Latitud</th>";
		$table .= "<th id='trs-hd' class='col-lg-1' >Longitud</th>";
		$table .= "<th id='trs-hd' class='col-lg-1' >geocerca</th>";
		$table .= "<th id='trs-hd' class='col-lg-1' >Tipo</th>";
		$table .= "<th id='trs-hd' class='col-lg-1' >Usuario</th>";
		$table .= "<th id='trs-hd' class='col-lg-1' >Fecha	</th>";
		$table .= "<th id='trs-hd' class='col-lg-1' >Tarifa</th>";
		$table .= "<th id='trs-hd' class='col-lg-1' >Estatus</th>";		
		$table .= "<th id='trs-hd' class='col-lg-1' >...</th>";
		
		$table .= "</thead>";
		$table .= "<tbody>";
		$arrCoord = listCoordinatesByRoute($cve_rut);
		if (count($arrCoord) > 0) {
		
			foreach($arrCoord as $coord) {
				
				$table .= "<tr>";				
				$table .= "<td data-label='Descripcion'>".$coord["des_crd"]	."</td>";
				$table .= "<td data-label='Latitud' style='word-wrap:break-word;font-size:12px;'>"		.$coord["lat"]		."</td>";
				$table .= "<td data-label='Longitud' style='word-wrap:break-word;font-size:12px;'>"		.$coord["lon"]		."</td>";
				$table .= "<td data-label='geocerca'>"		.$coord["geocerca"]	."</td>";
				$table .= "<td data-label='Tipo'>"			.$coord["tip_crd"]	."</td>";
				$table .= "<td data-label='Usuario'>"		.$coord["usr_cap"]	."</td>";
				$table .= "<td data-label='Fecha' style='word-wrap:break-word;font-size:12px;'>"			.$coord["fec_cap"]	."</td>";
				$table .= "<td data-label='Tarifa'>"		.$coord["tar1"]	."</td>";
				$table .= "<td data-label='Estatus'><img src='http://cnf.prepay.com.mx/assets/img/".($coord["stt_crd"]=="A"?"on":"off").".png' height='40' /></td>";
				$table .= "<td data-label='Editar'>";
				$table .= "<img src='http://cnf.prepay.com.mx/assets/img/editar.png' height='20' onclick='fillCoordinates(".$coord["cve_crd"].")' />";
				// cve_rut, des_crd, lat, lon, geocerca, tip_crd, stt_crd, usr_cap, fec_cap
				$table .= "<input type='hidden' id='datacoordinate".$coord["cve_crd"]."' value='".($coord["cve_crd"]."-".$coord["cve_rut"]."-".$coord["des_crd"]."-".base64_encode($coord["lat"])."-".base64_encode($coord["lon"])."-".$coord["geocerca"]."-".$coord["tip_crd"]."-".$coord["stt_crd"]."-".$coord["tar1"])."' />";
				$table .= "</td>";
				$table .= "</tr>";
			}
		}else{
			$table .= "<td id='trs-hd' class='col-lg-1' data-label='Folio'>No hay coordenadas para esta ruta</td>"; 
		}
		$table .= "</tbody>";
		$table .= "</table>";
		echo $ret."|$table";
		
	}else{
		echo "Problemas leyendo ruta";
	}
}
function validateCoord($coord){
	
}