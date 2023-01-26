<?php
	session_start();
	include "../cnx.php";
	require "unidad.php";
	class DAOUnidad{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoUnidad){
			$conn = getConnection();
			$idLinea	= $objetoUnidad->getIdLinea();
			$economico	= $objetoUnidad->getEconomico();
			$marca		= $objetoUnidad->getMarca();
			$modelo		= $objetoUnidad->getModelo();
			$pax		= $objetoUnidad->getPax();
			$motor		= $objetoUnidad->getMotor();
			$placa		= $objetoUnidad->getPlaca();
			$idConductor= $objetoUnidad->getIdConductor();
			$idRuta		= $objetoUnidad->getIdRuta();
			$status		= empty($objetoUnidad->getStatus())?"A":$objetoUnidad->getStatus();
			$sql = "INSERT INTO cat_uni ( num_eco, marca, modelo, pax, num_motor, placa, cve_cnd, stt_uni, cve_lin, usr_cap, fec_cap, cve_rut) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("sssissisissi", $economico, $marca, $modelo, $pax, $motor, $placa, $idConductor, $status, $idLinea, $_SESSION["userinfo"]["username"], $currDate = date("Y-m-d H:i:s"), $idRuta);
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
		public function update($objetoUnidad){
			$conn = getConnection();
			$idUnidad	= $objetoUnidad->getIdUnidad();
			$idLinea	= $objetoUnidad->getIdLinea();
			$economico	= $objetoUnidad->getEconomico();
			$marca		= $objetoUnidad->getMarca();
			$modelo		= $objetoUnidad->getModelo();
			$pax		= $objetoUnidad->getPax();
			$motor		= $objetoUnidad->getMotor();
			$placa		= $objetoUnidad->getPlaca();
			$idConductor= $objetoUnidad->getIdConductor();
			$status		= $objetoUnidad->getStatus();
			$idRuta		= $objetoUnidad->getIdRuta();
			//echo "unidad: ".$idUnidad."ruta: ".$idRuta;
			$sql = "update cat_uni set num_eco=?, marca=?, modelo=?, pax=?, num_motor=?, placa=?, cve_cnd=?, stt_uni=?, cve_lin=?, cve_rut=? where cve_uni = ?";
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("sssissisiii", $economico, $marca, $modelo, $pax, $motor, $placa, $idConductor, $status, $idLinea, $idRuta, $idUnidad);
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
		public function searchById($idunidad){
			$conn = getConnection();
			$sql = "select a.*,b.nombre from cat_uni a, cat_cnd b where b.cve_cnd=a.cve_cnd and a.cve_uni = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i",$idunidad);
			$stmt->execute();
			$result = $stmt->get_result();
			if($row = $result->fetch_assoc()) {
				$unidad = new Unidad();
				$unidad->setIdUnidad	($row["cve_uni"]);
				$unidad->setIdLinea		($row["cve_lin"]);
				$unidad->setEconomico	($row["num_eco"]);
				$unidad->setMarca		($row["marca"]);
				$unidad->setModelo		($row["modelo"]);
				$unidad->setPax         ($row["pax"]);
				$unidad->setMotor       ($row["num_motor"]);
				$unidad->setPlaca       ($row["placa"]);
				$unidad->setIdConductor ($row["cve_cnd"]);
				$unidad->setNomConductor ($row["nombre"]);
				$unidad->setStatus      ($row["stt_uni"]);
				$unidad->setidRuta      ($row["cve_rut"]);
			}
			$conn->close();
			return $unidad;
		}
	}