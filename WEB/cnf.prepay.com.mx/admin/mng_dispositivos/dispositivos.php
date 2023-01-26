<?php
class Dispositivos{
	var $idDispositivo;
	var $imei;
	var $desDispositivo;
	var $compania;	
	var $status;

	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdDispositivo(){		
		return $this->idDispositivo;
	}
	public function getImei(){
		return $this->imei;
	}
	public function getDesDispositivo(){
		return $this->desDispositivo;
	}
	public function getCompania(){
		return $this->compania;
	}	
	public function getStatus(){
		return $this->status;
	}
	
	public function setIdDispositivo($valor){
		//echo "val:".$valor;
		$this->idDispositivo = $valor;
	}
	public function setImei($valor){
		//echo "val:".$valor;
		$this->imei = $valor;
	}
	public function setDesDispositivo($valor){
		$this->desDispositivo = $valor;
	}	
	public function setCompania($valor){
		$this->compania = $valor;
	}
	public function setStatus($valor){
		$this->status = $valor;
	}
}
?>