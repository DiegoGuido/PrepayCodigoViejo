<?php
	session_start();
	include "../cnx.php";
	require "puntoventa.php";	
	class DAOPuntoVenta{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoPuntoVenta){
			$conn = getConnection();			
			$desPuntoVenta	= $objetoPuntoVenta->getDesPuntoVenta();
			$linea			= $objetoPuntoVenta->getLinea();
			$imei			= $objetoPuntoVenta->getImei();
			$status			= empty($objetoPuntoVenta->getStatus())?"A":$objetoPuntoVenta->getStatus();
			$tipo			= $objetoPuntoVenta->getTipo();
			//echo "tipo: ".$tipo."</br>";
			//$usr = 'admin';
			$usr = $_SESSION["userinfo"]["username"];
			$fec = $currDate = date("Y-m-d H:i:s");
			//echo "usr: ".$usr." fec: ".$fec;
			//echo "nom: ".$nomCompania." mon: ".$moneda." status ".$status;
			//$sql = "INSERT INTO cat_cia (des_cia, moneda, stt_cia, usr_cap, fec_cap) VALUES (?, ?, ?, ?, ?)";
			$sql = "INSERT INTO cat_ter (des_ter, cve_lin, stt_ter, usr_cap, fec_cap, tip_ter) VALUES ('$desPuntoVenta', '$linea', '$status', '$usr', '$fec', '$tipo')";
			//echo $sql;			
			$stmt = $conn->prepare($sql);
			//$stmt->bind_param("sssss", $nomCompania, $moneda, $status, $usr, $fec);
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
				//echo "error";
			}else{
				$ret = $stmt->insert_id;
				$ret1 = $ret;
				$sql2 = "INSERT INTO sis_rel_dis (imei,cve_rel,tip_rel,stt_rel,fec_efe,usr_cap,fec_cap) VALUES ('$imei','$ret1','2','$status','$fec','$usr','$fec')";
				//echo $sql;			
				$stmt2 = $conn->prepare($sql2);				
				$rc2 = $stmt2->execute();
				if(false===$rc2){
					$ret = $stmt2->error; 
					//echo "error";
				}else{
					 //$ret = $stmt2->insert_id;
					 $ret = $ret1;
					}									
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}
		public function update($objetoPuntoVenta){ 
		//echo "estoy en upd2";
		
			$conn = getConnection();
			$idPuntoVenta  = $objetoPuntoVenta->getIdPuntoVenta();			
			$imei			= $objetoPuntoVenta->getImei();
			$desPuntoVenta	= $objetoPuntoVenta->getDesPuntoVenta();
			$linea		= $objetoPuntoVenta->getLinea();
			$status			= $objetoPuntoVenta->getStatus();
			$tipo			= $objetoPuntoVenta->getTipo();
			//echo "id:".$idCompania."nom: ".$nomCompania." mon: ".$moneda." status ".$status;		
			$sql = "update cat_ter set des_ter = '$desPuntoVenta', cve_lin = '$linea', stt_ter = '$status', tip_ter = '$tipo' where cve_ter = '$idPuntoVenta'";
			//echo $sql;
			$stmt = $conn->prepare($sql);
			//$stmt->bind_param("sisi", $desPuntoVenta, $linea, $status, $idPuntoVenta);
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
			}else{
			   $ret = $stmt->affected_rows;
				$sql2 = "update sis_rel_dis set imei = '$imei', cve_rel = '$idPuntoVenta', stt_rel = '$status' where cve_rel = '$idPuntoVenta' and tip_rel='2'";
				$stmt2 = $conn->prepare($sql2);
				//$stmt2->bind_param("sisi", $imei, $idPuntoVenta, $status, $idPuntoVenta);
				$rc2 = $stmt2->execute();
				if(false===$rc2){
				$ret = $stmt2->error; 
				} else {
				$ret = $stmt->affected_rows;	
				}
			   
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}		
		public function searchById($idPuntoVenta){			
			$conn = getConnection();
			$sql = "select a.cve_ter, a.des_ter, a.cve_lin, b.imei, a.stt_ter, c.des_dis, a.tip_ter, d.des_tip_ter
					from cat_ter a, sis_rel_dis b, cat_dis c, cat_tip_ter d
					where a.cve_ter=b.cve_rel
					and b.imei=c.imei
					and a.cve_ter='$idPuntoVenta'
					and b.tip_rel=2
					and a.tip_ter=d.cve_tip_ter";
			//echo "sql: ".$sql."</br>";
			$stmt = $conn->prepare($sql);
			//$stmt->bind_param("i",$idPuntoVenta);
			$stmt->execute();
			$result = $stmt->get_result();
			if($row = $result->fetch_assoc()) {
				$puntoventa = new PuntoVenta();
				$puntoventa->setIdPuntoVenta 	($row["cve_ter"]);				
				$puntoventa->setDesPuntoVenta 	($row["des_ter"]);
				$puntoventa->setLinea 			($row["cve_lin"]);
				$puntoventa->setImei 			($row["imei"]);
				$puntoventa->setStatus	    	($row["stt_ter"]);
				$puntoventa->setDesDispositivo	($row["des_dis"]);
				$puntoventa->setTipo			($row["tip_ter"]);
				$puntoventa->setDesTipo			($row["des_tip_ter"]);
			}
			$conn->close();
			return $puntoventa;
		}
	}
?>