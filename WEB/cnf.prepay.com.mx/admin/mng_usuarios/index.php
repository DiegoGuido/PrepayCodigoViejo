<?php
session_start();
require "DAOUsuario.php";
$dao = new DAOUsuario();
if(isset($_POST["param_idusuario"])){
	$usr = $_POST["param_idusuario"];
	$exist = $dao->searchById($usr);
	if($exist->idUsuario != ""){
		echo "Este usuario ya existe.";
	}else{
		echo "&#x2714;";
	}
}else{
	if(isset($_POST["btnAgregar"])){
		$reply = $dao->insert(buildUsuario());
		if($reply > 0)
			$_SESSION["mng_usuarios"]["message"] = "Usuario registrada con el folio $reply";
		else
			$_SESSION["mng_usuarios"]["message"] = $reply;
	}else{
		if(isset($_POST["btnActualizar"])){
			$usuarioObj = buildUsuario();
			$reply = $dao->update(buildUsuario());
			if($reply == 0)
				$_SESSION["mng_usuarios"]["message"] = "Usuario guardado sin cambios.";
			else
				if($reply == 1)
					$_SESSION["mng_usuarios"]["message"] = "Cambios realizados correctamente.";
				else
					$_SESSION["mng_usuarios"]["message"] = $reply;
		}else{
			if(isset($_GET["u"])){
				$_SESSION["mng_usuarios"]["usuario"] = serialize($dao->searchById($_GET["u"]));
			}
		}
	}
	header("Location: ../mng_usuarios.phtml");
}
function buildUsuario(){
	$usuario = new Usuario();
	$usuario->setIdUsuario 		($_REQUEST["idUsuario"]);
	$usuario->setNombre			($_REQUEST["nombre"]);
	$usuario->setUsuario		($_REQUEST["usuario"]);
	$usuario->setContrasenia	(md5($_REQUEST["contrasenia"]));
	$usuario->setFechaIngreso	($_REQUEST["fechaingreso"]);
	$usuario->setCorreo			($_REQUEST["correo"]);
	$usuario->setStatus      	($_REQUEST["status"]);
	return $usuario;
}
?>