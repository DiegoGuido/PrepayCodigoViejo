<?php
class Recaudacion{
	var $idPuntoVenta;
	var $imei;
	var $usrec;
	var $monto;
	var $nombre;
	var $adeudo;
	var $fecrec;
	

	
	public function __construct(){
		// this class is not used by now...
	}
	public function getIdSaldoTerminal(){		
		return $this->idSaldoTerminal;
	}
	public function getIdPuntoVenta(){		
		return $this->idPuntoVenta;
	}
	public function getDesPuntoVenta(){		
		return $this->desPuntoVenta;
	}
	public function getImei(){
		return $this->imei;
	}
	public function getUsuario(){
		return $this->usuario;
	}
	public function getMonto(){
		return $this->monto;
	}
	public function getAdeudo(){
		return $this->adeudo;
	}
	public function getFecRec(){
		return $this->fecrec;
	}
		
	
	public function setIdSaldoTerminal($valor){		
		$this->idSaldoTerminal = $valor;
	}
	public function setIdPuntoVenta($valor){		
		$this->idPuntoVenta = $valor;
	}
	public function setDesPuntoVenta($valor){		
		$this->desPuntoVenta = $valor;
	}
	public function setImei($valor){	
		$this->imei = $valor;
	}
	public function setUsuario($valor){
		$this->usuario = $valor;
	}	
	public function setMonto($valor){
		$this->monto = $valor;
	}	
	public function setAdeudo($valor){
		$this->adeudo = $valor;
	}	
	public function setNombre($valor){
		$this->nombre = $valor;
	}
	public function setFecRec($valor){
		$this->fecrec = $valor;
	}
	
}
?>