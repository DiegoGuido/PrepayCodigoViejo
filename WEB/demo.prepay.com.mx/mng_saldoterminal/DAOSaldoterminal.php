<?php
	session_start();
	include "../cnx.php";
	require "saldoterminal.php";	
	class DAOSaldoTerminal{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoSaldoTerminal){
			$conn = getConnection();			
			$idPuntoVenta  = $objetoSaldoTerminal->getIdPuntoVenta();
			$imei			= $objetoSaldoTerminal->getImei();
			$monto			= $objetoSaldoTerminal->getMonto();
			$usrec			= $objetoSaldoTerminal->getUsuario();
			$usr = $_SESSION["userinfo"]["username"];
			$fec = $currDate = date("Y-m-d H:i:s");			
			$sql = "insert into sis_rec_ter (cve_ter,imei,mon_rec,usr_rec,usr_cap,fec_cap) values ('$idPuntoVenta','$imei','$monto','$usrec','$usr','$fec')";
			//echo $sql;
			$stmt = $conn->prepare($sql);			
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 				
			}else{
				$ret = $stmt->insert_id;				
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}
		public function update($objetoSaldoTerminal){ 				
			$conn = getConnection();
			$idSaldoTerminal  = $objetoSaldoTerminal->getIdSaldoTerminal();									
			$sql = "update cat_ter set des_ter = '$desSaldoTerminal', cve_lin = '$linea', stt_ter = '$status', tip_ter = '$tipo' where cve_ter = '$idSaldoTerminal'";		
			$stmt = $conn->prepare($sql);			
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
			}else{
			   $ret = $stmt->affected_rows;				
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}		
		public function searchById($usr,$imei){			
			$conn = getConnection();
			/*$sql = "select a.cve_reg,a.mon_rec,a.cve_ter,b.des_ter,a.imei,a.usr_rec
			from sis_rec_ter a, cat_ter b
			where a.cve_ter=b.cve_ter
			and a.usr_rec='$usr'
			and a.imei='$imei'";*/
			$sql="select a.imei,a.bal_ter,a.usr_cap,b.cve_rel,c.des_ter,d.nombre
			from vw_ter_bal a, vw_rel_dis b,cat_ter c, cat_usr d
			where a.imei=b.imei
			and b.cve_rel=c.cve_ter
			and b.tip_rel=2
			and c.tip_ter in('C','H')
			and b.stt_rel='A'
			and c.stt_ter='A'
			and a.usr_cap=d.usr
			and d.stt_usr='A'
			and a.usr_cap='$usr'
			and a.imei='$imei'";
			//echo "sql: ".$sql;
			$result = $conn->query($sql);	
			if($row = $result->fetch_assoc()) {
				$saldoterminal = new SaldoTerminal();				
				$saldoterminal->setIdPuntoVenta 	($row["cve_rel"]);
				$saldoterminal->setDesPuntoVenta 	($row["des_ter"]);
				$saldoterminal->setImei 			($row["imei"]);
				$saldoterminal->setUsuario	    	($row["usr_cap"]);
				$saldoterminal->setSaldo			($row["bal_ter"]);				
				$saldoterminal->setNombre			($row["nombre"]);
			}
			$conn->close();
			return $saldoterminal;
		}
	}
?>