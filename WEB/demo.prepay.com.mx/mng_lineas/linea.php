<?php
class Linea{
	var $idLinea;
	var $nombreLinea;
	var $descripcionLinea;
	var $compania;
	var $status;
	var $rutas;
	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdLinea(){
		return $this->idLinea;
	}
	public function getNombreLinea(){
		return $this->nombreLinea;
	}
	public function getDescripcionLinea(){
		return $this->descripcionLinea;
	}
	public function getCompania(){
		return $this->compania;
	}
	public function getStatus(){
		return $this->status;
	}
	public function getRutas(){
		return $this->rutas;
	}
	
	
	public function setIdLinea($valor){
		$this->idLinea = $valor;
	}
	public function setNombreLinea($valor){
		$this->nombreLinea = $valor;
	}
	public function setDescripcionLinea($valor){
		$this->descripcionLinea = $valor;
	}
	public function setCompania($valor){
		$this->compania = $valor;
	}
	public function setStatus($valor){
		$this->status = $valor;
	}
	public function setRutas($valor){
		$this->rutas = $valor;
	}
}

