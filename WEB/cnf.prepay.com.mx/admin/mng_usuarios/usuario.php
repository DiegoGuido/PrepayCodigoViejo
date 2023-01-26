<?php
class Usuario{
	var $idUsuario;
	var $nombre;
	var $usuario;
	var $contrasenia;
	var $fechaIngreso;
	var $correo;
	var $status;
	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdUsuario(){
		return $this->idUsuario;
	}
	public function getNombre(){
		return $this->nombre;
	}
	public function getUsuario(){
		return $this->usuario;
	}
	public function getContrasenia(){
		return $this->contrasenia;
	}
	public function getFechaIngreso(){
		return $this->fechaIngreso;
	}
	public function getCorreo(){
		return $this->correo;
	}
	public function getStatus(){
		return $this->status;
	}
	
	public function setIdUsuario($valor){
		$this->idUsuario = $valor;
	}
	public function setNombre($valor){
		$this->nombre = $valor;
	}
	public function setUsuario($valor){
		$this->usuario = $valor;
	}
	public function setContrasenia($valor){
		$this->contrasenia = $valor;
	}
	public function setFechaingreso($valor){
		$this->fechaIngreso = $valor;
	}
	public function setCorreo($valor){
		$this->correo = $valor;
	}
	public function setStatus($valor){
		$this->status = $valor;
	}
}

