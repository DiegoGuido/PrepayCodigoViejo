<?php
class PuntoVenta{
	var $idPuntoVenta;
	var $desPuntoVenta;
	var $linea;
	var $imei;	
	var $status;
	var $DesDispositivo;
	var $tipo;

	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdPuntoVenta(){		
		return $this->idPuntoVenta;
	}	
	public function getDesPuntoVenta(){
		return $this->desPuntoVenta;
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
	public function getTipo(){
		return $this->tipo;
	}
	public function getDesTipo(){
		return $this->destipo;
	}
	
	
	public function setIdPuntoVenta($valor){		
		$this->idPuntoVenta = $valor;
	}	
	public function setDesPuntoVenta($valor){
		$this->desPuntoVenta = $valor;
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
	public function setTipo($valor){
		$this->tipo = $valor;
	}
	public function setDesTipo($valor){
		$this->destipo = $valor;
	}
}
?>