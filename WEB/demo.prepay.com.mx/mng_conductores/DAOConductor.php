<?php
	session_start();
	include "../cnx.php";
	require "conductor.php";
	class DAOConductor{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoConductor){
			$conn = getConnection();
			$nombre			= $objetoConductor->getNombre();
			$fechaEntrada	= $objetoConductor->getFechaEntrada();
			$fechaSalida	= $objetoConductor->getFechaSalida();
			$idLinea		= $objetoConductor->getIdLinea();
			$status			= empty($objetoConductor->getStatus())?"A":$objetoConductor->getStatus();
			$sql = "INSERT INTO cat_cnd ( nombre, fec_ing, fec_sal, stt_cnd, cve_lin, usr_cap, fec_cap) VALUES (?, ?, ?, ?, ?, ?, ?)";
			//echo $sql;
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssssiss", $nombre,$fechaEntrada, $fechaSalida, $status, $idLinea, $_SESSION["userinfo"]["username"], $currDate = date("Y-m-d H:i:s"));
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
		public function update($objetoConductor){
			$conn = getConnection();
			$idConductor	= $objetoConductor->getIdConductor();
			$nombre			= $objetoConductor->getNombre();
			$fechaEntrada	= $objetoConductor->getFechaEntrada();
			$fechaSalida	= $objetoConductor->getFechaSalida();
			$idLinea		= $objetoConductor->getIdLinea();
			$status			= $objetoConductor->getStatus();
			$sql = "update cat_cnd set nombre = ?, fec_ing = ?, fec_sal = ?, stt_cnd = ?, cve_lin = ? where cve_cnd = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssssii", $nombre,$fechaEntrada, $fechaSalida, $status, $idLinea, $idConductor);
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
		public function searchById($idconductor){
			$conn = getConnection();
			$sql = "select * from cat_cnd where cve_cnd = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i",$idconductor);
			$stmt->execute();
			$result = $stmt->get_result();
			if($row = $result->fetch_assoc()) {
				$conductor = new Conductor();
				$conductor->setIdConductor ($row["cve_cnd"]);
				$conductor->setNombre 		($row["nombre"]);
				$conductor->setFechaEntrada(explode(" ",$row["fec_ing"])[0]);
				$conductor->setFechaSalida (explode(" ",$row["fec_sal"])[0]);
				$conductor->setIdLinea		($row["cve_lin"]);
				$conductor->setStatus      ($row["stt_cnd"]);
			}
			$stmt->close();
			$conn->close();
			return $conductor;
		}
	}