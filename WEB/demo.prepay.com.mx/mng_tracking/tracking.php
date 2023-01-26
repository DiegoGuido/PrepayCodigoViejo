<?php
class Tracking{
	var $unidad;
	var $fecini;
	var $fecfin;		

	
	public function __construct(){
		// this class is not used by now...
	}
	public function getUnidad(){
		return $this->unidad;
	}
	public function getFecIni(){
		return $this->fecini;
	}
	public function getFecFin(){
		return $this->fecfin;
	}	
	
	
	public function setUnidad($valor){
		$this->unidad = $valor;
	}
	public function setFecIni($valor){
		$this->fecini = $valor;
	}	
	public function setFecFin($valor){
		$this->fecfin = $valor;
	}
	
}
?>