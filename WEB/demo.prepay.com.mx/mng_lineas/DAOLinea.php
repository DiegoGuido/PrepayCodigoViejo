<?php
	session_start();
	include "../cnx.php";
	require "linea.php";
	class DAOLinea{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoLinea){
			$conn = getConnection();
			$nombreLinea		= $objetoLinea->getNombrelinea();
			$descripcionLinea	= $objetoLinea->getDescripcionLinea();
			$compania			= $objetoLinea->getCompania();
			$rutas				= $objetoLinea->getRutas();
			$status			= empty($objetoLinea->getStatus())?"A":$objetoLinea->getStatus();
			$sql = "INSERT INTO cat_lin (des_lin, des_lar, cve_cia, stt_lin, usr_cap, fec_cap) VALUES (?, ?, ?, ?, ?, ?)";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssisss", $nombreLinea,$descripcionLinea, $compania, $status, $_SESSION["userinfo"]["username"], $currDate = date("Y-m-d H:i:s"));
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
			}else{
				$ret = $stmt->insert_id;
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}
		public function update($objetoLinea){
			$conn = getConnection();
			$idLinea			= $objetoLinea->getIdLinea();
			$nombreLinea		= $objetoLinea->getNombrelinea();
			$descripcionLinea	= $objetoLinea->getDescripcionLinea();
			$compania			= $objetoLinea->getCompania();
			$rutas				= $objetoLinea->getRutas();
			$status				= empty($objetoLinea->getStatus())?"A":$objetoLinea->getStatus();
			$sql = "update cat_lin set des_lin = ?, des_lar = ?, cve_cia = ?, stt_lin = ? where cve_lin = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssisi", $nombreLinea, $descripcionLinea, $compania, $status, $idLinea);
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
		public function searchById($idlinea){
			$conn = getConnection();
			$sql = "select * from cat_lin where cve_lin = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i",$idlinea);
			$stmt->execute();
			$result = $stmt->get_result();
			if($row = $result->fetch_assoc()) {
				$linea = new Linea();
				$linea->setIdLinea 			($row["cve_lin"]);
				$linea->setNombreLinea 		($row["des_lin"]);
				$linea->setDescripcionLinea	($row["des_lar"]);
				$linea->setCompania 		($row["cve_cia"]);
				$linea->setStatus      		($row["stt_lin"]);
			}
			$conn->close();
			return $linea;
		}
	}