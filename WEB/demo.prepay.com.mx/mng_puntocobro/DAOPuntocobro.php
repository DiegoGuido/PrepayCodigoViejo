<?php
	session_start();
	include "../cnx.php";
	include "../util_list.php";
	require "puntocobro.php";	
	class DAOPuntoCobro{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoPuntoCobro){
			$conn = getConnection();			
			$desPuntoCobro	= $objetoPuntoCobro->getDesPuntoCobro();						
			//obtengo linea
			//$linea			= $objetoPuntoCobro->getLinea();
			$sqllin = "select cve_lin from cat_uni where cve_uni='$desPuntoCobro'";
			$arrlin = getDataFromTable($conn,$sqllin);
			$linea  = array_unique($arrlin);
			//termino linea
			$imei			= $objetoPuntoCobro->getImei();
			$status			= empty($objetoPuntoCobro->getStatus())?"A":$objetoPuntoCobro->getStatus();
			$modo			= $objetoPuntoCobro->getModo();
			//$usr = 'admin';
			$usr = $_SESSION["userinfo"]["username"];
			$fec = $currDate = date("Y-m-d H:i:s");

				$sql2 = "INSERT INTO sis_rel_dis (imei,cve_rel,tip_rel,stt_rel,fec_efe,usr_cap,fec_cap) VALUES ('$imei','$desPuntoCobro','1','$status','$fec','$usr','$fec')";
				//echo $sql;			
				$stmt2 = $conn->prepare($sql2);				
				$rc2 = $stmt2->execute();
				if(false===$rc2){
					$ret = $stmt2->error; 
					//echo "error";
				}else{
					 $ret = $stmt2->insert_id;
					 //$ret = $ret1;
					 $mod = updateModoDis($conn,$imei,$modo);
					}											
			$stmt2->close();
			$conn->close();
			return $ret;
		}
		public function update($objetoPuntoCobro){ 
		//echo "estoy en upd2";
		
			$conn = getConnection();
			$idPuntoCobro  = $objetoPuntoCobro->getIdPuntoCobro();			
			$imei			= $objetoPuntoCobro->getImei();
			$desPuntoCobro	= $objetoPuntoCobro->getDesPuntoCobro();
			//$linea		= $objetoPuntoCobro->getLinea();
			$status			= $objetoPuntoCobro->getStatus();
			$modo			= $objetoPuntoCobro->getModo();
			//echo "id:".$idCompania."nom: ".$nomCompania." mon: ".$moneda." status ".$status;		
				//$sql2 = "update sis_rel_dis set imei = ?, cve_rel = ?, stt_rel = ? where cve_rel = ?";
				$sql2 = "update sis_rel_dis set imei = '$imei', stt_rel = '$status' where cve_rel = '$idPuntoCobro' and tip_rel='1'";
				//echo $sql2."<br>";
				$stmt2 = $conn->prepare($sql2);
				//$stmt2->bind_param("sisi", $imei, $idPuntoCobro, $status, $idPuntoCobro);
				$rc2 = $stmt2->execute();
				if(false===$rc2){
				$ret = $stmt2->error; 
				} else {
				$ret = $stmt2->affected_rows;
				$mod = updateModoDis($conn,$imei,$modo);
				$ret = $rer + $mod;
				}
				
			$stmt2->close();
			$conn->close();
			return $ret;
		}		
		public function searchById($idPuntoCobro){			
			$conn = getConnection();
			$sql = "select a.cve_uni, a.num_eco, a.cve_lin, b.imei, c.des_dis,a.stt_uni,c.cve_mod_dis,d.des_mod_dis
			from cat_uni a, sis_rel_dis b, cat_dis c, cat_mod_dis d
			where a.cve_uni=b.cve_rel
			and b.imei=c.imei
			and a.num_eco='$idPuntoCobro'
			and c.cve_mod_dis=d.cve_mod_dis
			and b.tip_rel='1'";
			//echo "sql: ".$sql."</br>";
			$stmt = $conn->prepare($sql);
			//$stmt->bind_param("i",$idPuntoCobro);
			$stmt->execute();
			$result = $stmt->get_result();
			if($row = $result->fetch_assoc()) {
				$puntocobro = new PuntoCobro();
				$puntocobro->setIdPuntoCobro 	($row["cve_uni"]);				
				$puntocobro->setDesPuntoCobro 	($row["num_eco"]);
				$puntocobro->setLinea 			($row["cve_lin"]);
				$puntocobro->setImei 		    ($row["imei"]);
				$puntocobro->setStatus	    	($row["stt_uni"]);
				$puntocobro->setDesDispositivo	($row["des_dis"]);
				$puntocobro->setModo			($row["cve_mod_dis"]);
				$puntocobro->setDesModo			($row["des_mod_dis"]);
			}
			$stmt->close();
			$conn->close();
			return $puntocobro;
		}
	}
	function updateModoDis($conn,$imei,$modo){
		$sql = "update cat_dis set cve_mod_dis='$modo' where imei='$imei'";
		//echo $sql;
		$stmt = $conn->prepare($sql);		
		$rc = $stmt->execute();
		if(false===$rc){
			$ret = $stmt->error; 
		} else {
			$ret = $stmt->affected_rows;	
		}
		$stmt->close();
	return $ret;
	}
?>