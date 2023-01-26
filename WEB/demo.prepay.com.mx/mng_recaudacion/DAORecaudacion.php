<?php
	session_start();
	include "../cnx.php";
	require "recaudacion.php";	
	class DAORecaudacion{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoRecaudacion){
			$conn = getConnection();			
			$idPuntoVenta  = $objetoRecaudacion->getIdPuntoVenta();
			$imei			= $objetoRecaudacion->getImei();
			$monto			= $objetoRecaudacion->getMonto();
			$usrec			= $objetoRecaudacion->getUsuario();
			$fecrec			= $objetoRecaudacion->getFecRec();
			$usr = $_SESSION["userinfo"]["username"];
			$fec = $currDate = date("Y-m-d H:i:s");			
			$sql = "insert into sis_rec_taq (cve_ter,imei,mon_rec,usr_rec,usr_cap,fec_cap,fec_trx) values ('$idPuntoVenta','$imei','$monto','$usrec','$usr','$fec','$fecrec')";
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
		public function searchById($usr,$imei,$fecrec){			
			$conn = getConnection();			
			$sql="select a.imei,a.bal_taq,a.usr_cap,b.cve_rel,c.des_ter,d.nombre,a.fecha
			from vw_adeudo_taquilla a, vw_rel_dis b,cat_ter c, cat_usr d
			where a.imei=b.imei
			and b.cve_rel=c.cve_ter
			and b.tip_rel=2			
			and b.stt_rel='A'
			and c.stt_ter='A'
			and a.usr_cap=d.usr
			and d.stt_usr='A'
			and a.usr_cap='$usr'
			and a.imei='$imei'
			and a.fecha='$fecrec'";
			//echo "sql: ".$sql;
			$result = $conn->query($sql);	
			if($row = $result->fetch_assoc()) {
				$recaudacion = new Recaudacion();				
				$recaudacion->setIdPuntoVenta 	($row["cve_rel"]);
				$recaudacion->setDesPuntoVenta 	($row["des_ter"]);
				$recaudacion->setImei 			($row["imei"]);
				$recaudacion->setUsuario	    ($row["usr_cap"]);
				$recaudacion->setAdeudo			($row["bal_taq"]);				
				$recaudacion->setNombre			($row["nombre"]);
				$recaudacion->setFecRec			($row["fecha"]);
			}
			$conn->close();
			return $recaudacion;
		}
	}
?>