<?php
class Unidad{
	var $idUnidad;
	var $idLinea;
	var $economico;
	var $marca;
	var $modelo;
	var $pax;
	var $motor;
	var $placa;
	var $idConductor;
	var $NomConductor;
	var $status;
	var $idRuta;
	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdUnidad(){
		return $this->idUnidad;
	}
	public function getIdLinea(){
		return $this->idLinea;
	}
	public function getEconomico(){
		return $this->economico;
	}
	public function getMarca(){
		return $this->marca;
	}
	public function getModelo(){
		return $this->modelo;
	}
	public function getPax(){
		return $this->pax;
	}
	public function getMotor(){
		return $this->motor;
	}
	public function getPlaca(){
		return $this->placa;
	}
	public function getIdConductor(){
		return $this->idConductor;
	}
	public function getNomConductor(){
		return $this->NomConductor;
	}
	public function getStatus(){
		return $this->status;
	}
	public function getIdRuta(){
		return $this->idRuta;
	}
	
	
	public function setIdUnidad($valor){
		$this->idUnidad = $valor;
	}
	public function setIdLinea($valor){
		$this->idLinea = $valor;
	}
	public function setEconomico($valor){
		$this->economico = $valor;
	}
	public function setMarca($valor){
		$this->marca = $valor;
	}
	public function setModelo($valor){
		$this->modelo = $valor;
	}
	public function setPax($valor){
		$this->pax = $valor;
	}
	public function setMotor($valor){
		$this->motor = $valor;
	}
	public function setPlaca($valor){
		$this->placa = $valor;
	}
	public function setIdConductor($valor){
		$this->idConductor = $valor;
	}
	public function setNomConductor($valor){
		$this->NomConductor = $valor;
	}
	public function setStatus($valor){
		$this->status = $valor;
	}
	public function setIdRuta($valor){
		$this->idRuta = $valor;
	}
	
}

