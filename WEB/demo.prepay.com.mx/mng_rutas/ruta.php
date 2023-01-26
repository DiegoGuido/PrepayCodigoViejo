<?php
class Ruta{
	var $idRuta;
	var $idLinea;
	var $ruta;
	var $origen;
	var $destino;
	var $tiempo;
	var $kms;
	var $status;
	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdRuta(){
		return $this->idRuta;
	}
	public function getIdLinea(){
		return $this->idLinea;
	}
	public function getOrigen(){
		return $this->origen;
	}
	public function getDestino(){
		return $this->destino;
	}
	public function getTiempo(){
		return $this->tiempo;
	}
	public function getKms(){
		return $this->kms;
	}
	public function getStatus(){
		return $this->status;
	}
	public function getRuta(){
		return $this->ruta;
	}
	
	public function setIdRuta($valor){
		$this->idRuta = $valor;
	}
	public function setIdLinea($valor){
		$this->idLinea = $valor;
	}
	public function setOrigen($valor){
		$this->origen = $valor;
	}
	public function setDestino($valor){
		$this->destino = $valor;
	}
	public function setTiempo($valor){
		$this->tiempo = $valor;
	}
	public function setKms($valor){
		$this->kms = $valor;
	}
	public function setStatus($valor){
		$this->status = $valor;
	}
	public function setRuta($valor){
		$this->ruta = $valor;
	}
}

