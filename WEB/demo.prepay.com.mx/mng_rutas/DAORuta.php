<?php
	session_start();
	include "../cnx.php";
	require "ruta.php";
	class DAORuta{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoRuta){
			$conn = getConnection();
			$idLinea	= $objetoRuta->getIdLinea();
			$ruta		= $objetoRuta->getRuta();
			$origen		= $objetoRuta->getOrigen();
			$destino	= $objetoRuta->getDestino();
			$tiempo		= $objetoRuta->getTiempo();
			$kms		= $objetoRuta->getKms();
			$status		= empty($objetoRuta->getStatus())?"A":$objetoRuta->getStatus();
			$currDate = date("Y-m-d H:i:s");
			$sql = "INSERT INTO cat_rut (nombre_ruta, origen, destino, tiempo, kms, cve_lin, stt_rut, usr_cap, fec_cap) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			//echo $sql;
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("sssssisss", $ruta, $origen,$destino, $tiempo, $kms, $idLinea,$status, $_SESSION["userinfo"]["username"], $currDate);
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
			}else{
				$ret = $stmt->insert_id;
				//PROCESO DE INSERCION DE MODOS DE RUTA QUE POR DEFAULT ES ESTATICO
				$sqlmod = "insert into cat_rut_mod (cve_rut,cve_mod,stt_rut_mod,usr_cap,fec_cap) 
				values ('$ret','E','A','".$_SESSION['userinfo']['username']."','$currDate')";				
				$stmt2 = $conn->prepare($sqlmod);	
				$rc2 = $stmt2->execute();
				if(false===$rc){
					$ret2 = $stmt2->error; 
				}else{
					$ret2 = $stmt2->insert_id;		
				}
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}
		public function update($objetoRuta){
			$conn = getConnection();
			$idRuta		= $objetoRuta->getIdRuta();
			$idLinea	= $objetoRuta->getIdLinea();
			$ruta		= $objetoRuta->getRuta();
			$origen		= $objetoRuta->getOrigen();
			$destino	= $objetoRuta->getDestino();
			$tiempo		= $objetoRuta->getTiempo();
			$kms		= $objetoRuta->getKms();
			$status		= empty($objetoRuta->getStatus())?"A":$objetoRuta->getStatus();
			$sql = "update cat_rut set nombre_ruta=? ,origen = ?, destino = ?, tiempo = ?, kms = ?, cve_lin = ?, stt_rut = ? where cve_rut = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("sssssisi", $ruta,$origen,$destino, $tiempo, $kms, $idLinea, $status, $idRuta);
			$rc = $stmt->execute();
			if(false===$rc){
				//echo "ok";
				$ret = $stmt->error; 
			}else{
				//echo "¬¬";
			   $ret = $stmt->affected_rows;
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}
		public function searchById($idRuta){
			$conn = getConnection();
			$sql = "select * from cat_rut where cve_rut = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i",$idRuta);
			$stmt->execute();
			$result = $stmt->get_result();
			if($row = $result->fetch_assoc()) {
				$conductor = new Ruta();
				$conductor->setIdRuta 	($row["cve_rut"]);
				$conductor->setIdLinea	($row["cve_lin"]);
				$conductor->setRuta 	($row["nombre_ruta"]);
				$conductor->setOrigen 	($row["origen"]);
				$conductor->setDestino	($row["destino"]);
				$conductor->setTiempo 	($row["tiempo"]);
				$conductor->setKms		($row["kms"]);
				$conductor->setStatus   ($row["stt_rut"]);
			}
			$stmt->close();
			$conn->close();
			return $conductor;
		}
	}