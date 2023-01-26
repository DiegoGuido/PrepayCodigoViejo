<?php
class PuntoCobro{
	var $idPuntoCobro;
	var $desPuntoCobro;
	var $linea;
	var $imei;	
	var $status;
	var $DesDispositivo;
	var $modo;

	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdPuntoCobro(){		
		return $this->idPuntoCobro;
	}	
	public function getDesPuntoCobro(){
		return $this->desPuntoCobro;
	}
	public function getLinea(){
		return $this->linea;
	}
	public function getImei(){
		return $this->imei;
	}	
	public function getStatus(){
		return $this->status;
	}
	public function getDesDispositivo(){
		return $this->DesDispositivo;
	}
	public function getModo(){
		return $this->modo;
	}
	
	public function setIdPuntoCobro($valor){		
		$this->idPuntoCobro = $valor;
	}	
	public function setDesPuntoCobro($valor){
		$this->desPuntoCobro = $valor;
	}	
	public function setLinea($valor){
		$this->linea = $valor;
	}
	public function setImei($valor){	
		$this->imei = $valor;
	}
	public function setStatus($valor){
		$this->status = $valor;
	}
	public function setDesDispositivo($valor){
		$this->DesDispositivo = $valor;
	}
	public function setModo($valor){
		$this->modo = $valor;
	}
	public function setDesModo($valor){
		$this->desmodo = $valor;
	}
}
?>