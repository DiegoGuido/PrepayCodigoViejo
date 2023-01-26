<?php
class Recargas{
	var $idPuntoVenta;
	var $fecini;
	var $fecfin;		

	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdPuntoVenta(){
		return $this->idPuntoVenta;
	}
	public function getFecIni(){
		return $this->fecini;
	}
	public function getFecFin(){
		return $this->fecfin;
	}	
	
	
	public function setIdPuntoVenta($valor){
		$this->idPuntoVenta = $valor;
	}
	public function setFecIni($valor){
		$this->fecini = $valor;
	}	
	public function setFecFin($valor){
		$this->fecfin = $valor;
	}
	
}
?>