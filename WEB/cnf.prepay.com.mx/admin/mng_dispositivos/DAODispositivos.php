<?php
	session_start();
	include "../cnx.php";
	require "dispositivos.php";	
	class DAODispositivos{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoDispositivo){
			$conn = getConnection();
			$imei			= $objetoDispositivo->getImei();
			$desDispositivo	= $objetoDispositivo->getDesDispositivo();
			$compania		= $objetoDispositivo->getCompania();						
			$status			= empty($objetoDispositivo->getStatus())?"A":$objetoDispositivo->getStatus();
			//$usr = 'admin';
			$usr = $_SESSION["userinfo"]["username"];
			$fec = $currDate = date("Y-m-d H:i:s");
			//echo "usr: ".$usr." fec: ".$fec;
			//echo "nom: ".$nomCompania." mon: ".$moneda." status ".$status;
			//$sql = "INSERT INTO cat_cia (des_cia, moneda, stt_cia, usr_cap, fec_cap) VALUES (?, ?, ?, ?, ?)";
			$sql = "INSERT INTO cat_dis (imei, des_dis, cve_cia, stt_dis, usr_cap, fec_cap) VALUES ('$imei', '$desDispositivo','$compania', '$status', '$usr', '$fec')";
			//echo $sql;			
			$stmt = $conn->prepare($sql);
			//$stmt->bind_param("sssss", $nomCompania, $moneda, $status, $usr, $fec);
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
				//echo "error";				
			}else{
				$ret = $stmt->insert_id;
				//echo "ok";
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}
		public function update($objetoDispositivo){ 
		//echo "estoy en upd2";
		
			$conn = getConnection();
			$idDispositivo  = $objetoDispositivo->getIdDispositivo();
			//echo "disp:".$idDispositivo;
			$imei			= $objetoDispositivo->getImei();
			$desDispositivo	= $objetoDispositivo->getDesDispositivo();
			$compania		= $objetoDispositivo->getCompania();
			$status			= $objetoDispositivo->getStatus();			
			//echo "id:".$idCompania."nom: ".$nomCompania." mon: ".$moneda." status ".$status;		
			$sql = "update cat_dis set imei = ?, des_dis = ?, cve_cia = ?, stt_dis = ? where cve_dis = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssisi", $imei, $desDispositivo, $compania, $status, $idDispositivo);
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
			}else{
			   $ret = $stmt->affected_rows;
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}		
		public function searchById($idDispositivo){			
			$conn = getConnection();
			$sql = "select * from cat_dis where cve_dis = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i",$idDispositivo);
			$stmt->execute();
			$result = $stmt->get_result();
			if($row = $result->fetch_assoc()) {
				$dispositivos = new Dispositivos();
				$dispositivos->setIdDispositivo 	($row["cve_dis"]);
				$dispositivos->setImei 				($row["imei"]);
				$dispositivos->setDesDispositivo 	($row["des_dis"]);
				$dispositivos->setCompania 			($row["cve_cia"]);								
				$dispositivos->setStatus	    	($row["stt_dis"]);
			}
			$conn->close();
			return $dispositivos;
		}
	}
?>