<?php
	session_start();
	include "../cnx.php";
	require "usuario.php";
	class DAOUsuario{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoUsuario){
			$conn = getConnection();
			$nombre			= $objetoUsuario->getNombre();
			$usuario		= $objetoUsuario->getUsuario();
			$contrasenia	= $objetoUsuario->getContrasenia();
			$fechaIngreso	= $objetoUsuario->getFechaIngreso();
			$correo			= $objetoUsuario->getCorreo();
			$status			= empty($objetoUsuario->getStatus())?"A":$objetoUsuario->getStatus();
				
						
						
						
			
			$sql = "INSERT INTO cat_usr ( usr, pwd, nombre, fec_ing, stt_usr, mail, usr_cap, fec_cap) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)";
			//echo $sql;
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssssssss", $usuario, $contrasenia, $nombre, $fechaIngreso, $status, $correo, $_SESSION["userinfo"]["username"], $currDate = date("Y-m-d H:i:s"));
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
		public function update($objetoUsuario){
			$conn = getConnection();
			$idUsuario		= $objetoUsuario->getIdUsuario();
			$nombre			= $objetoUsuario->getNombre();
			$usuario		= $objetoUsuario->getUsuario();
			$contrasenia	= md5($objetoUsuario->getContrasenia());
			$fechaIngreso	= $objetoUsuario->getFechaIngreso();
			$correo			= $objetoUsuario->getCorreo();
			$status			= $objetoUsuario->getStatus();
			if($contrasenia == ""){
			
				$sql = "update cat_usr set usr=?, nombre=?, fec_ing=?, stt_usr=?, mail=? where cve_usr = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("ssssss", $usuario, $nombre, $fechaIngreso, $status, $correo,$idUsuario);
				$rc = $stmt->execute();
			}else{
				
				//$sql = "update cat_usr set usr=?, pwd=?, nombre=?, fec_ing=?, stt_usr=?, mail=? where cve_usr = ?";
				//$stmt = $conn->prepare($sql);
				$sql = "update cat_usr set usr=?, nombre=?, fec_ing=?, stt_usr=?, mail=? where cve_usr = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("ssssss", $usuario, $contrasenia, $nombre, $fechaIngreso, $status, $correo,$idUsuario);
				$rc = $stmt->execute();
			}
			
			if(false===$rc){
				$ret = $stmt->error; 
				
			}else{
			   $ret = $stmt->affected_rows;
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}
		public function searchById($idusuario){
			$conn = getConnection();
			if($idusuario > 0){
				$field = "cve_usr";
				$bind = "i";
			}else{
				$field = "usr";
				$bind = "s";
			}
			$sql = "select * from cat_usr where $field = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param($bind ,$idusuario);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if($row = $result->fetch_assoc()) {
				
				$usuario = new Usuario();
				$usuario->setIdUsuario 		($row["cve_usr"]);
				$usuario->setNombre 		($row["nombre"]);
				$usuario->setUsuario		($row["usr"]);
				$usuario->setContrasenia 	($row["pwd"]);
				$usuario->setFechaIngreso	(explode(" ",$row["fec_ing"])[0]);
				$usuario->setCorreo			($row["mail"]);
				$usuario->setStatus      	($row["stt_usr"]);
			}else{
				$usuario= "";
			}
			$stmt->close();
			$conn->close();
			return $usuario;
		}
	}