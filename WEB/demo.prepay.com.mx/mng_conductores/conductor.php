<?php
class Conductor{
	var $idConductor;
	var $nombre;
	var $fechaEntrada;
	var $fechaSalida;
	var $idLinea;
	var $status;
	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdConductor(){
		return $this->idConductor;
	}
	public function getNombre(){
		return $this->nombre;
	}
	public function getFechaEntrada(){
		return $this->fechaEntrada;
	}
	public function getFechaSalida(){
		return $this->fechaSalida;
	}
	public function getIdLinea(){
		return $this->idLinea;
	}
	public function getStatus(){
		return $this->status;
	}
	
	public function setIdConductor($valor){
		$this->idConductor = $valor;
	}
	public function setNombre($valor){
		$this->nombre = $valor;
	}
	public function setFechaEntrada($valor){
		$this->fechaEntrada = $valor;
	}
	public function setFechaSalida($valor){
		$this->fechaSalida = $valor;
	}
	public function setIdLinea($valor){
		$this->idLinea = $valor;
	}
	public function setStatus($valor){
		$this->status = $valor;
	}
}

