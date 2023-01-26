<?php
class Compania{
	var $idCompania;
	var $nomCompania;
	var $moneda;	
	var $status;
	var $urlCompania;

	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdCompania(){
		return $this->idCompania;
	}
	public function getNomCompania(){
		return $this->nomCompania;
	}
	public function getMoneda(){
		return $this->moneda;
	}	
	public function getStatus(){
		return $this->status;
	}
	public function getUrlCompania(){
		return $this->urlCompania;
	}
	
	public function setIdCompania($valor){
		$this->idCompania = $valor;
	}
	public function setNomCompania($valor){
		$this->nomCompania = $valor;
	}	
	public function setMoneda($valor){
		$this->moneda = $valor;
	}
	public function setStatus($valor){
		$this->status = $valor;
	}
	public function setUrlCompania($valor){
		$this->urlCompania = $valor;
	}
}
?>