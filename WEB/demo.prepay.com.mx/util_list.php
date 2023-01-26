<?php
function listActiveLines($usr,$cia){
	$conn = getConnection();
	/*$sql = "select * from 
	cat_lin where stt_lin='A' 
	and cve_cia in (select cve_cia from vw_usr_cia where usr='$usr')";*/
	$sql = "select * from 
	cat_lin where stt_lin='A' 
	and cve_cia = '$cia'";
	$result = $conn->query($sql);
	$arrLin = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmpLin=array();
			$tmpLin["cve_lin"] = $row["cve_lin"];
			$tmpLin["des_lin"] = $row["des_lin"];
			$tmpLin["des_lar"] = $row["des_lar"];
			array_push($arrLin, $tmpLin);
		}
	} else {
		echo "0 results";
	}
	$conn->close();
	return $arrLin;
}
function listActiveDrivers($cve_lin){
	$conn = getConnection();
	$sql = "select * from cat_cnd where cve_lin = '$cve_lin' and stt_cnd='A'";
	$result = $conn->query($sql);
	$arrDrv = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmpDrv=array();
			$tmpDrv["cve_cnd"] = $row["cve_cnd"];
			$tmpDrv["nombre"]  = $row["nombre"];
			array_push($arrDrv, $tmpDrv);
		}
	} else {
		echo "Sin Resultados";
	}
	$conn->close();
	return $arrDrv;
}
function listAllDrivers($usr,$cia){
	$conn = getConnection();
	$sql = "select cve_cnd, nombre,fec_ing,fec_sal,C.cve_lin,stt_cnd, des_lin   
			from cat_cnd C inner join cat_lin L on L.cve_lin=C.cve_lin 
			where C.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')";
	//echo $sql;
	$result = $conn->query($sql);
	$arrDrv = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmpDrv=array();
			$tmpDrv["cve_cnd"] = $row["cve_cnd"];
			$tmpDrv["nombre"]  = $row["nombre"];
			$tmpDrv["fec_ing"]  = $row["fec_ing"];
			$tmpDrv["fec_sal"]  = $row["fec_sal"];
			$tmpDrv["cve_lin"]  = $row["cve_lin"];
			$tmpDrv["stt_cnd"]  = $row["stt_cnd"];
			$tmpDrv["des_lin"]  = $row["des_lin"];
			array_push($arrDrv, $tmpDrv);
		}
	} else {
		echo "Sin Resultados";
	}
	$conn->close();
	return $arrDrv;
}
function listAllUsers($conn){
	$sql = "select * from cat_usr";
	//echo $sql;
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($arr, $row);
		}
	} else {
		echo "Sin Resultados";
	}
	$conn->close();
	return $arr;
}
function listAllLines($usr,$cia){
	//echo "usr".$usr;
	$conn = getConnection();
	/*$sql = "select cve_lin ,des_lin, des_lar, L.cve_cia, des_cia, stt_lin 
			from cat_lin L inner join cat_cia C on L.cve_cia=C.cve_cia 
			where L.cve_cia in (select cve_cia from vw_usr_cia where usr='$usr')";*/
	$sql = "select cve_lin ,des_lin, des_lar, L.cve_cia, des_cia, stt_lin 
			from cat_lin L inner join cat_cia C on L.cve_cia=C.cve_cia 
			where L.cve_cia = '$cia'";
	//echo $sql;
	$result = $conn->query($sql);
	$arrLin = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmpLin=array();
			$tmpLin["cve_lin"] = $row["cve_lin"];
			$tmpLin["des_lin"] = $row["des_lin"];
			$tmpLin["des_lar"] = $row["des_lar"];
			$tmpLin["cve_cia"] = $row["cve_cia"];
			$tmpLin["des_cia"] = $row["des_cia"];
			$tmpLin["stt_lin"] = $row["stt_lin"];
			array_push($arrLin, $tmpLin);
		}
	} else {
		echo "Sin Resultados";
	}
	$conn->close();
	return $arrLin;
}

function listAllBuses($usr,$cia){
	$conn = getConnection();
	$sql = "select * from cat_uni U 
	inner join cat_lin L on U.cve_lin=L.cve_lin
	inner join cat_cnd C on C.cve_cnd=U.cve_cnd
	inner join cat_rut R on R.cve_rut=U.cve_rut
	where L.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')";
	$result = $conn->query($sql);
	$arrUni = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmpUni=array();
			$tmpUni["cve_uni"] 	= $row["cve_uni"];
			$tmpUni["des_lin"]  = $row["des_lin"];
			$tmpUni["num_eco"]  = $row["num_eco"];
			$tmpUni["placa"] 	= $row["placa"];
			$tmpUni["nombre"] 	= $row["nombre"];
			$tmpUni["stt_uni"] 	= $row["stt_uni"];
			$tmpUni["nombre_ruta"] 	= $row["nombre_ruta"];
			array_push($arrUni, $tmpUni);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arrUni;
}

function listActiveBuses($usr,$cia){
	$conn = getConnection();
	/*$sql = "select * from cat_uni U 
	inner join cat_lin L on U.cve_lin=L.cve_lin
	inner join cat_cnd C on C.cve_cnd=U.cve_cnd
	where U.stt_uni='A'
	and L.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr')";*/
	$sql = "select a.*,b.des_lin,c.nombre
	from cat_uni a, cat_lin b, cat_cnd c
	where a.cve_lin=b.cve_lin
	and a.cve_cnd=c.cve_cnd
	and a.stt_uni='A'
	and b.cve_lin in (select d.cve_lin from vw_usr_cia d where d.usr='$usr' and cve_cia='$cia')
	and a.cve_uni not in (select e.cve_rel from vw_rel_dis e where e.cve_lin=b.cve_lin and e.tip_rel=1)";
	echo "sql: ".$sql."<br>";
	$result = $conn->query($sql);
	$arrUni = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmpUni=array();
			$tmpUni["cve_uni"] 	= $row["cve_uni"];
			$tmpUni["des_lin"]  = $row["des_lin"];
			$tmpUni["num_eco"]  = $row["num_eco"];
			$tmpUni["placa"] 	= $row["placa"];
			$tmpUni["nombre"] 	= $row["nombre"];
			$tmpUni["stt_uni"] 	= $row["stt_uni"];
			array_push($arrUni, $tmpUni);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arrUni;
}
function listActiveRoutes($usr,$cia){
	$conn = getConnection();	
	$sql = "select cve_rut, nombre_ruta from cat_rut 
			where cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')";
	//echo $sql;
	$result = $conn->query($sql);
	$arrRut = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmpRut=array();
			$tmpRut["cve_rut"] 	= $row["cve_rut"] ;			
			$tmpRut["nombre_ruta"] 	= $row["nombre_ruta"];
			array_push($arrRut, $tmpRut);
		}
	} else {
		echo "";
	}
	$conn->close();
	return $arrRut;
}

function listAllRoutes($usr,$cia){
	$conn = getConnection();
	$sql = "select cve_rut,origen, destino, tiempo, kms, stt_rut, R.cve_lin, des_lin, des_lar, nombre_ruta
			from cat_rut R inner join cat_lin L on L.cve_lin = R.cve_lin
			where R.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')";
	//echo $sql;
	$result = $conn->query($sql);
	$arrRut = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmpRut=array();
			$tmpRut["cve_rut"] 	= $row["cve_rut"] ;
			$tmpRut["origen"] 	= $row["origen"] ;
			$tmpRut["destino"]  = $row["destino"];
			$tmpRut["tiempo"]  	= $row["tiempo"] ;
			$tmpRut["kms"]		= $row["kms"]	 ;	
			$tmpRut["stt_rut"] 	= $row["stt_rut"];
			$tmpRut["cve_lin"] 	= $row["cve_lin"];
			$tmpRut["des_lin"] 	= $row["des_lin"];
			$tmpRut["des_lar"] 	= $row["des_lar"];
			$tmpRut["nombre_ruta"] 	= $row["nombre_ruta"];
			array_push($arrRut, $tmpRut);
		}
	} else {
		echo "";
	}
	$conn->close();
	return $arrRut;
}
function listCoordinatesByRoute($idRoute){
	$conn = getConnection();
	$sql = "SELECT * from sis_crd_rut where cve_rut = '$idRoute' order by stt_crd, tip_crd desc";
	//echo $sql;
	$result = $conn->query($sql);
	$arrCoord = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($arrCoord, $row);
		}
	} else {
		echo "";
	}
	$conn->close();
	return $arrCoord;
}
function listAllCoordTypes(){
	$conn = getConnection();
	$sql = "SELECT * FROM cat_tip_crd";
	//echo $sql;
	$result = $conn->query($sql);
	$arrTypes = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($arrTypes, $row);
		}
	} else {
		echo "";
	}
	$conn->close();
	return $arrTypes;
}

function listActiveCompanies($usr){
	$conn = getConnection();
	$sql = "select * from cat_cia where cve_cia in (select cve_cia from sis_usr_cia where usr='$usr')";
	$result = $conn->query($sql);
	$arrCia = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			echo "";
			$tmpCia=array();
			$tmpCia["cve_cia"] 	= $row["cve_cia"];
			$tmpCia["des_cia"]  = $row["des_cia"];
			array_push($arrCia, $tmpCia);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arrCia;
}
function listRatesByRoute($idRuta){
	$conn = getConnection();
	$sql = "select * from cat_tar where cve_rut = '$idRuta'";
	$result = $conn->query($sql);
	$arrRates = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmpRate=array();
			$tmpRate["cve_tar"] = $row["cve_tar"];
			$tmpRate["km_ini"]  = $row["km_ini"];
			$tmpRate["km_fin"]  = $row["km_fin"];
			$tmpRate["monto"]  	= $row["monto"];
			$tmpRate["stt_tar"] = $row["stt_tar"];
			array_push($arrRates, $tmpRate);
		}
	} else {
		echo "Sin Resultados";
	}
	$conn->close();
	return $arrRates;
}

function listAllDevices($usr){
	$conn = getConnection();
	$sql = "select a.cve_dis, a.imei, a.des_dis, a.cve_cia,	b.des_cia 
			from cat_dis a, cat_cia b
			where a.cve_cia=b.cve_cia
			and a.cve_cia in (select cve_cia from vw_usr_cia where usr='$usr')";
	//echo $sql;
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["cve_dis"] = $row["cve_dis"];
			$tmp["imei"] 	= $row["imei"];
			$tmp["des_dis"] = $row["des_dis"];
			$tmp["cve_cia"] = $row["cve_cia"];			
			$tmp["des_cia"] = $row["des_cia"];			
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listActiveDevices($usr){
	$conn = getConnection();
	$sql = "select * from cat_dis where stt_dis='A' and cve_cia in (select cve_cia from vw_usr_cia where usr='$usr')";
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["cve_dis"] = $row["cve_dis"];
			$tmp["imei"] 	= $row["imei"];
			$tmp["des_dis"] = $row["des_dis"];
			$tmp["cve_cia"] = $row["cve_cia"];			
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listAllSalePoints($usr,$cia,$tip){
	$conn = getConnection();
	if ($tip == ""){
	$sql = "select a.cve_rel, a.dato, a.cve_lin, b.des_lin, a.imei, c.des_dis, a.stt_rel, e.des_tip_ter
			from vw_rel_dis a, cat_lin b, cat_dis c, cat_ter d, cat_tip_ter e
			where a.cve_lin=b.cve_lin
			and a.imei=c.imei
			and a.cve_rel=d.cve_ter
			and d.tip_ter=e.cve_tip_ter
			and a.tip_rel=2
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr')
			and a.cve_cia = '$cia'
			order by a.cve_rel";
	} else {
		$sql = "select a.cve_rel, a.dato, a.cve_lin, b.des_lin, a.imei, c.des_dis, a.stt_rel, e.des_tip_ter
			from vw_rel_dis a, cat_lin b, cat_dis c, cat_ter d, cat_tip_ter e
			where a.cve_lin=b.cve_lin
			and a.imei=c.imei
			and a.cve_rel=d.cve_ter
			and d.tip_ter=e.cve_tip_ter
			and a.tip_rel=2
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr')
			and a.cve_cia = '$cia'
			and e.cve_tip_ter in ('C','H')
			order by a.cve_rel";
	}
	//echo "sql: ".$sql;
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["cve_rel"] = $row["cve_rel"];			
			$tmp["dato"] = $row["dato"];		
			$tmp["des_lin"] = $row["des_lin"];
			$tmp["imei"] = $row["imei"];
			$tmp["des_dis"] = $row["des_dis"];
			$tmp["stt_rel"] = $row["stt_rel"];
			$tmp["des_tip_ter"] = $row["des_tip_ter"];
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listActiveSalePoints($usr){
	$conn = getConnection();
	/*$sql = "select a.cve_ter, a.des_ter, a.cve_lin, c.des_lin, b.imei, a.stt_ter
			from cat_ter a, sis_rel_dis b, cat_lin c
			where a.cve_ter=b.cve_rel
			and a.cve_lin=c.cve_lin
			and b.tip_rel=2
			and b.stt_rel='A'
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr')
			order by a.cve_ter";*/
	$sql = "select a.cve_rel, a.dato, a.cve_lin, b.des_lin,a.imei, a.stt_rel
			from vw_rel_dis a, cat_lin b
			where a.cve_lin=b.cve_lin
			and a.tip_rel=2
			and a.stt_rel='A'
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr')
			order by a.cve_rel";
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["cve_rel"] = $row["cve_rel"];			
			$tmp["dato"] = $row["dato"];		
			$tmp["des_lin"] = $row["des_lin"];
			$tmp["imei"] = $row["imei"];
			$tmp["stt_rel"] = $row["stt_rel"];
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listAllPayPoints($usr,$cia){
	$conn = getConnection();
	/*$sql = "select a.cve_uni, a.num_eco, a.cve_lin, c.des_lin, b.imei, a.stt_uni, b.cve_reg
			from cat_uni a, sis_rel_dis b, cat_lin c
			where a.cve_uni=b.cve_rel
			and a.cve_lin = c.cve_lin
			and b.tip_rel=1
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr')
			order by a.cve_uni";*/
	$sql = "select a.cve_rel, a.dato, a.cve_lin, b.des_lin, a.imei, a.stt_rel, c.des_dis,c.cve_mod_dis,d.des_mod_dis
			from vw_rel_dis a, cat_lin b, cat_dis c, cat_mod_dis d
			where a.cve_lin=b.cve_lin
			and a.imei=c.imei
			and a.tip_rel=1
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr')
			and a.cve_cia = '$cia'
			and c.cve_mod_dis=d.cve_mod_dis
			order by a.cve_rel";
	//echo $sql;
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			//$tmp["cve_reg"] = $row["cve_reg"];			
			$tmp["cve_rel"] = $row["cve_rel"];			
			$tmp["dato"] = $row["dato"];		
			$tmp["des_lin"] = $row["des_lin"];
			$tmp["imei"] = $row["imei"];
			$tmp["stt_rel"] = $row["stt_rel"];
			$tmp["des_dis"] = $row["des_dis"];
			$tmp["cve_mod_dis"] = $row["cve_mod_dis"];
			$tmp["des_mod_dis"] = $row["des_mod_dis"];
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listActivePayPoints($usr){
	$conn = getConnection();
	/*$sql = "select a.cve_uni, a.num_eco, a.cve_lin, c.des_lin, b.imei, a.stt_uni, b.cve_reg
			from cat_uni a, sis_rel_dis b, cat_lin c
			where a.cve_uni=b.cve_rel
			and a.cve_lin = c.cve_lin
			and b.tip_rel=1
			and b.stt_rel='A'
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr')
			order by a.cve_uni";*/
	$sql = "select a.cve_rel, a.dato, a.cve_lin, b.des_lin, a.imei, a.stt_rel
			from vw_rel_dis a, cat_lin b
			where a.cve_lin=b.cve_lin
			and a.tip_rel=1
			and a.stt_rel='A'
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr')
			order by a.cve_rel";
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			//$tmp["cve_reg"] = $row["cve_reg"];			
			$tmp["cve_rel"] = $row["cve_rel"];			
			$tmp["dato"] = $row["dato"];		
			$tmp["des_lin"] = $row["des_lin"];
			$tmp["imei"] = $row["imei"];
			$tmp["stt_uni"] = $row["stt_uni"];
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listAllRecharge($usr,$cia,$puntoventa,$fecini,$fecfin,$min,$lim){
	$conn = getConnection();
	if ($puntoventa <> ""){
		$sql="select * from vw_recarga 
			where fec_rec >= '$fecini 00:00:00'
			and fec_rec <= '$fecfin 23:59:59'
			and cve_ter = $puntoventa
			and cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')
			and cve_cia='$cia'";
			
	} else {
		$sql="select * from vw_recarga 
			where fec_rec >= '$fecini 00:00:00'
			and fec_rec <= '$fecfin 23:59:59'
			and cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')
			and cve_cia='$cia'";
	}
	$_SESSION['userinfo']['sqlRec'] = $sql;
	if ($lim <> ""){
		//$str = " LIMIT $min,$lim";
		$str = " ORDER BY fec_cap DESC LIMIT $min,$lim";
		$sql = $sql.$str;
	}
	//echo $sql."</br>";	
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["cve_reg"] = $row["cve_reg"];			
			$tmp["des_ter"] = $row["des_ter"];			
			$tmp["des_lin"] = $row["des_lin"];
			$tmp["des_cia"] = $row["des_cia"];
			$tmp["monto"] = $row["monto"];
			$tmp["moneda"] = $row["moneda"];
			$tmp["des_mov"] = $row["des_mov"];
			$tmp["cve_trj"] = $row["cve_trj"];
			$tmp["des_tip"] = $row["des_tip"];
			$tmp["saldo"] = $row["saldo"];
			$tmp["fec_rec"] = $row["fec_rec"];
			$tmp["usr_cap"] = $row["usr_cap"];
			$tmp["imei"] = $row["imei"];
			$tmp["stt_uni"] = $row["stt_uni"];
			$tmp["latitud"] = $row["latitud"];
			$tmp["longitud"] = $row["longitud"];
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listAllPayment($usr,$cia,$unidad,$fecini,$fecfin,$min,$lim){
	$conn = getConnection();
	if ($unidad <> ""){
		$sql="select * from vw_cobro
			where cve_cia='$cia'
			and fec_cob >= '$fecini 00:00:00'
			and fec_cob <= '$fecfin 23:59:59'
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')
			and cve_uni = $unidad";
	} else {
		$sql="select * from vw_cobro
			where cve_cia='$cia'
			and fec_cob >= '$fecini 00:00:00'
			and fec_cob <= '$fecfin 23:59:59'
			and cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')";
	}
	//echo $sql;
	$_SESSION['userinfo']['sql'] = $sql;
	if ($lim <> ""){
		$str = " ORDER BY fec_cob DESC LIMIT $min,$lim";
		$sql = $sql.$str;
	}
	//echo $sql;	
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["cve_reg"] = $row["cve_reg"];			
			$tmp["num_eco"] = $row["num_eco"];			
			$tmp["des_lin"] = $row["des_lin"];
			$tmp["ruta"] = $row["ruta"];
			$tmp["monto"] = $row["monto"];
			$tmp["moneda"] = $row["moneda"];
			$tmp["des_mov"] = $row["des_mov"];
			$tmp["cve_trj"] = $row["cve_trj"];
			$tmp["des_tip"] = $row["des_tip"];
			$tmp["saldo"] = $row["saldo"];
			$tmp["fec_cob"] = $row["fec_cob"];			
			$tmp["imei"] = $row["imei"];			
			$tmp["latitud"] = $row["latitud"];
			$tmp["longitud"] = $row["longitud"];
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listOrgDes($usr,$cia,$unidad,$fecini,$fecfin){
	$conn = getConnection();
	if ($unidad <> ""){
		$sql="select a.* from vw_sis_trk_pts a
		where a.cve_uni=a.cve_uni 
		and a.fec_trk >= '$fecini 00:00:00'
		and a.fec_trk <= '$fecfin 23:59:59'
		and a.cve_uni='$unidad'
		and a.cve_cia='$cia'";		
	} else {
		$sql="select a.* from vw_sis_trk_pts a
		where a.fec_trk = (select max(a1.fec_trk)
        from vw_sis_trk_pts a1                 
        where a1.cve_uni=a.cve_uni
        and a1.fec_trk >= '$fecini 00:00:00'
        and a1.fec_trk <= '$fecfin 23:59:59')
		and a.cve_cia='$cia'";		
	}	
	//echo $sql;	
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["num_eco"] = $row["num_eco"];			
			$tmp["fec_trk"] = $row["fec_trk"];			
			$tmp["des_crd"] = $row["des_crd"];
			$tmp["des_tip_crd"] = $row["des_tip_crd"];			
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listOrgDes_bak($usr,$cia,$unidad,$fecini,$fecfin){
	$conn = getConnection();
	if ($unidad <> ""){
		$sql="select x.* from
			(select a.cve_reg,a.cve_uni,a.cve_rut,a.cve_lin,a.cve_cia,a.fec_trk, a.latitud, a.longitud,b.tip_crd, b.des_crd,b.geocerca,
			(6371 * acos(cos(radians(b.lat)) * cos(radians(a.latitud)) * cos(radians(a.longitud) - radians(b.lon))
			+ sin(radians(b.lat)) * sin(radians(a.latitud))))AS distance,
			if((lag(b.des_crd,1) over (PARTITION BY a.cve_uni order by a.fec_trk)) = des_crd,0,1) as comp,
			c.num_eco,
			d.des_tip_crd
			FROM sis_trk_uni a, sis_crd_rut b, cat_uni c, cat_tip_crd d
			WHERE a.cve_rut=b.cve_rut 
			and a.cve_uni=c.cve_uni
			and b.tip_crd=d.cve_tip_crd			
			and a.fec_trk >= '$fecini 00:00:00' and a.fec_trk <= '$fecfin 23:59:59'
			and a.cve_cia='$cia'
			and a.cve_uni='$unidad'
			HAVING distance < b.geocerca
			ORDER BY a.cve_uni,a.fec_trk) x
			where x.comp=1";
	} else {
		$sql="select x.* from
			(select a.cve_reg,a.cve_uni,a.cve_rut,a.cve_lin,a.cve_cia,a.fec_trk, a.latitud, a.longitud,b.tip_crd, b.des_crd,b.geocerca,
			(6371 * acos(cos(radians(b.lat)) * cos(radians(a.latitud)) * cos(radians(a.longitud) - radians(b.lon))
			+ sin(radians(b.lat)) * sin(radians(a.latitud))))AS distance,
			if((lag(b.des_crd,1) over (PARTITION BY a.cve_uni order by a.fec_trk)) = des_crd,0,1) as comp,
			c.num_eco,
			d.des_tip_crd
			FROM sis_trk_uni a, sis_crd_rut b, cat_uni c, cat_tip_crd d
			WHERE a.cve_rut=b.cve_rut 
			and a.cve_uni=c.cve_uni
			and b.tip_crd=d.cve_tip_crd
			and b.stt_crd='A'
			and b.tip_crd in ('O','D')
			and a.fec_trk = (select max(x.fec_trk) from sis_trk_uni x where x.cve_uni=a.cve_uni and x.fec_trk >= '$fecini 00:00:00' and x.fec_trk <= '$fecfin 23:59:59')
			and a.cve_cia='$cia'			
			HAVING distance < b.geocerca
			ORDER BY a.cve_uni,a.fec_trk) x
			where x.comp=1";		
	}	
	echo $sql;	
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["num_eco"] = $row["num_eco"];			
			$tmp["fec_trk"] = $row["fec_trk"];			
			$tmp["des_crd"] = $row["des_crd"];
			$tmp["des_tip_crd"] = $row["des_tip_crd"];			
			array_push($arr, $tmp);
		}
	} else {
		echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listTracking($usr,$cia,$unidad,$fecini,$fecfin){
	$conn = getConnection();
	if ($unidad <> ""){
		$sql="select a.cve_uni, a.num_eco, a.latitud, a.longitud, a.fec_trk
			from vw_tracking a
			where a.cve_uni='$unidad'
			and a.fec_trk >= '$fecini 00:00:00'
			and a.fec_trk <= '$fecfin 23:59:59'
			order by a.cve_reg";			
	} else {
			$sql="select a.cve_uni,a.num_eco,a.latitud,a.longitud,a.fec_trk
			from vw_tracking a
			where a.fec_trk >= '$fecini 00:00:00'
			and a.fec_trk <= '$fecfin 23:59:59'
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')
			and a.fec_trk = (select max(x.fec_trk) from sis_trk_uni x where x.cve_uni=a.cve_uni and x.fec_trk >= '$fecini 00:00:00' and x.fec_trk <= '$fecfin 23:59:59')
			group by cve_uni
			order by cve_uni";			
	}
	//echo $sql;	
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["num_eco"] = $row["num_eco"];			
			$tmp["fec_trk"] = $row["fec_trk"];			
			$tmp["latitud"] = $row["latitud"];
			$tmp["longitud"] = $row["longitud"];
			array_push($arr, $tmp);
		}
	} else {
		//echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}


function listTrackingRes($usr,$cia,$unidad,$fecini,$fecfin){
	$conn = getConnection();
	if ($unidad <> ""){
		$sql="select x.*, if(x.veltiempo != 0,3.6*(x.veldistancia/x.veltiempo),0) as velocidad from
			(select a.cve_reg,a.cve_uni,c.num_eco,a.cve_rut,concat(e.origen,' - ',e.destino) as ruta,a.cve_lin,d.des_lin,a.cve_cia,f.des_cia,a.fec_trk, a.latitud, a.longitud,b.tip_crd, b.des_crd,b.geocerca,
			(6371 * acos(cos(radians(b.lat)) * cos(radians(a.latitud)) * cos(radians(a.longitud) - radians(b.lon))
			+ sin(radians(b.lat)) * sin(radians(a.latitud))))AS distance,
			(lag(b.des_crd,1) over (PARTITION BY a.cve_uni order by a.fec_trk)) as prevdes,
			1000 *((6371 * acos(cos(radians((lag(a.latitud,1) over (PARTITION BY a.cve_uni order by a.fec_trk)))) * cos(radians(a.latitud)) * cos(radians(a.longitud) - radians((lag(a.longitud,1)over (PARTITION BY a.cve_uni order by a.fec_trk))))
			+ sin(radians((lag(a.latitud,1) over (PARTITION BY a.cve_uni order by a.fec_trk)))) * sin(radians(a.latitud))))) as veldistancia,
			TIMESTAMPDIFF(SECOND, (lag(a.fec_trk,1) over (PARTITION BY a.cve_uni order by a.fec_trk)), a.fec_trk) as veltiempo
			FROM sis_trk_uni a, sis_crd_rut b,cat_uni c, cat_lin d, cat_rut e, cat_cia f
			WHERE a.cve_rut=b.cve_rut
			and a.cve_uni=c.cve_uni
			and a.cve_lin=d.cve_lin
			and a.cve_rut=e.cve_rut
			and a.cve_cia=f.cve_cia
			and b.stt_crd='A'
			and a.cve_uni='$unidad'
			and a.fec_trk >= '$fecini 00:00:00' and a.fec_trk <= '$fecfin 23:59:59'
			and a.cve_cia='$cia'
			HAVING distance < b.geocerca
			ORDER BY a.cve_uni,a.fec_trk) x";			
	} else {
			$sql="select x.*, if(x.veltiempo != 0,3.6*(x.veldistancia/x.veltiempo),0) as velocidad from
			(select a.cve_reg,a.cve_uni,c.num_eco,a.cve_rut,concat(e.origen,' - ',e.destino) as ruta,a.cve_lin,d.des_lin,a.cve_cia,f.des_cia,a.fec_trk, a.latitud, a.longitud,b.tip_crd, b.des_crd,b.geocerca,
			(6371 * acos(cos(radians(b.lat)) * cos(radians(a.latitud)) * cos(radians(a.longitud) - radians(b.lon))
			+ sin(radians(b.lat)) * sin(radians(a.latitud))))AS distance,
			(lag(b.des_crd,1) over (PARTITION BY a.cve_uni order by a.fec_trk)) as prevdes,
			1000 *((6371 * acos(cos(radians((lag(a.latitud,1) over (PARTITION BY a.cve_uni order by a.fec_trk)))) * cos(radians(a.latitud)) * cos(radians(a.longitud) - radians((lag(a.longitud,1)over (PARTITION BY a.cve_uni order by a.fec_trk))))
			+ sin(radians((lag(a.latitud,1) over (PARTITION BY a.cve_uni order by a.fec_trk)))) * sin(radians(a.latitud))))) as veldistancia,
			TIMESTAMPDIFF(SECOND, (lag(a.fec_trk,1) over (PARTITION BY a.cve_uni order by a.fec_trk)), a.fec_trk) as veltiempo
			FROM sis_trk_uni a, sis_crd_rut b,cat_uni c, cat_lin d, cat_rut e, cat_cia f
			WHERE a.cve_rut=b.cve_rut
			and a.cve_uni=c.cve_uni
			and a.cve_lin=d.cve_lin
			and a.cve_rut=e.cve_rut
			and a.cve_cia=f.cve_cia
			and b.stt_crd='A'			
			and a.fec_trk = (select max(x.fec_trk) from sis_trk_uni x where x.cve_uni=a.cve_uni and x.fec_trk >= '$fecini 00:00:00' and x.fec_trk <= '$fecfin 23:59:59')
			and a.cve_cia='$cia'
			HAVING distance < b.geocerca
			ORDER BY a.cve_uni,a.fec_trk) x";		
	}
	//echo $sql;	
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["num_eco"] = $row["num_eco"];			
			$tmp["fec_trk"] = $row["fec_trk"];			
			$tmp["latitud"] = $row["latitud"];
			$tmp["longitud"] = $row["longitud"];
			$tmp["velocidad"] = $row["velocidad"];
			$tmp["des_crd"] = $row["des_crd"];
			array_push($arr, $tmp);
		}
	} else {
		//echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listTrackingDet($usr,$cia,$uni,$fi,$ff){
	$conn = getConnection();
	if ($uni <> ""){
		$sql="select x.*, if(x.veltiempo != 0,3.6*(x.veldistancia/x.veltiempo),0) as velocidad from
		(select a.cve_reg,a.cve_uni,c.num_eco,a.cve_rut,e.nombre_ruta,a.cve_lin,d.des_lin,a.cve_cia,f.des_cia,a.fec_trk, a.latitud, a.longitud,
		1000 *((6371 * acos(cos(radians((lag(a.latitud,1) over (PARTITION BY a.cve_uni order by a.fec_trk)))) * cos(radians(a.latitud)) * cos(radians(a.longitud) - radians((lag(a.longitud,1)over (PARTITION BY a.cve_uni order by a.fec_trk))))+ sin(radians((lag(a.latitud,1) over (PARTITION BY a.cve_uni order by a.fec_trk)))) * sin(radians(a.latitud))))) as veldistancia,
		TIMESTAMPDIFF(SECOND, (lag(a.fec_trk,1) over (PARTITION BY a.cve_uni order by a.fec_trk)), a.fec_trk) as veltiempo
		FROM sis_trk_uni a, cat_uni c, cat_lin d, cat_rut e, cat_cia f
		WHERE a.cve_uni=c.cve_uni
		and a.cve_lin=d.cve_lin
		and a.cve_rut=e.cve_rut
		and a.cve_cia=f.cve_cia			
		and a.cve_uni='$uni'
		and a.fec_trk >= '$fi 00:00:00' and a.fec_trk <= '$ff 23:59:59'
		and a.cve_cia='$cia'			
		ORDER BY a.cve_uni,a.fec_trk) x";		
	} else {
		$sql="select x.*, if(x.veltiempo != 0,3.6*(x.veldistancia/x.veltiempo),0) as velocidad from
		(select a.cve_reg,a.cve_uni,c.num_eco,a.cve_rut,e.nombre_ruta,a.cve_lin,d.des_lin,a.cve_cia,f.des_cia,a.fec_trk, a.latitud, a.longitud,
		1000 *((6371 * acos(cos(radians((lag(a.latitud,1) over (PARTITION BY a.cve_uni order by a.fec_trk)))) * cos(radians(a.latitud)) * cos(radians(a.longitud) - radians((lag(a.longitud,1)over (PARTITION BY a.cve_uni order by a.fec_trk))))+ sin(radians((lag(a.latitud,1) over (PARTITION BY a.cve_uni order by a.fec_trk)))) * sin(radians(a.latitud))))) as veldistancia,
		TIMESTAMPDIFF(SECOND, (lag(a.fec_trk,1) over (PARTITION BY a.cve_uni order by a.fec_trk)), a.fec_trk) as veltiempo
		FROM sis_trk_uni a, cat_uni c, cat_lin d, cat_rut e, cat_cia f
		WHERE a.cve_uni=c.cve_uni
		and a.cve_lin=d.cve_lin
		and a.cve_rut=e.cve_rut
		and a.cve_cia=f.cve_cia		
		and a.cve_cia='$cia'
		and a.fec_trk = (select max(a1.fec_trk) from sis_trk_uni a1
 							where a1.cve_uni=a.cve_uni
 							and a1.cve_cia=a.cve_cia
 							and a1.fec_trk >= '$fi 00:00:00' 
 							and a1.fec_trk <= '$ff 23:59:59')
		ORDER BY a.cve_uni,a.fec_trk) x";		
	}
	//echo $sql;	
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["num_eco"] = $row["num_eco"];			
			$tmp["fec_trk"] = $row["fec_trk"];			
			$tmp["latitud"] = $row["latitud"];
			$tmp["longitud"] = $row["longitud"];
			$tmp["velocidad"] = $row["velocidad"];
			$tmp["des_crd"] = $row["latitud"].",".$row["longitud"];
			array_push($arr, $tmp);
		}
	} else {
		//echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listBusTrack($usr,$cia){
	$conn = getConnection();
	$sql = "select distinct(a.cve_uni),b.num_eco
			from sis_trk_uni a, cat_uni b
			where a.cve_uni=b.cve_uni
			and a.cve_lin in (select cve_lin from vw_usr_cia where usr='$usr' and cve_cia='$cia')";
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();				
			$tmp["cve_uni"] = $row["cve_uni"];			
			$tmp["num_eco"] = $row["num_eco"];		
			array_push($arr, $tmp);
		}
	} else {
		echo "Sin Resultados";
	}
	$conn->close();
	return $arr;
}



function getDataFromTable1($sql){
	$conn = getConnection();
	//echo $sql;	
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {			
			array_push($arr, $row);
		}
	} 
	$conn->close();
	return $arr;
}

function listSttTrj(){
	$conn = getConnection();
	$sql = "select * from cat_stt";
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["cve_stt"] = $row["cve_stt"];
			$tmp["des_stt"] = $row["des_stt"];
			array_push($arr, $tmp);
		}
	} else {
		echo "Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listTrjReg($usr,$cia,$stt,$id,$min,$lim){
	$conn = getConnection();
	if ($id <> ""){
		$sql="select a.cve_reg,a.cve_trj,b.des_tip,cve_prs,if(cve_prs != 0,'Personalizada','Sin Personalizar') as des_prs,c.des_ter, a.mon_costo,a.moneda,d.des_cia,a.stt_trj,a.usr_cap,a.fec_cap, a.cmt_trj, a.vigencia
			from sis_reg_trj a, cat_tip_trj b, cat_ter c, cat_cia d
			where a.cve_tip=b.cve_tip
			and a.cve_ter=c.cve_ter
			and a.cve_cia=d.cve_cia
			and a.cve_cia in (select cve_cia from vw_usr_cia where usr='$usr')
			and a.cve_trj='$id'
			and a.cve_cia='$cia'";
	} else {
		$sql="select a.cve_reg,a.cve_trj,b.des_tip,cve_prs,if(cve_prs != 0,'Personalizada','Sin Personalizar') as des_prs,c.des_ter, a.mon_costo,a.moneda,d.des_cia,a.stt_trj,a.usr_cap,a.fec_cap, a.cmt_trj, a.vigencia
			from sis_reg_trj a, cat_tip_trj b, cat_ter c, cat_cia d
			where a.cve_tip=b.cve_tip
			and a.cve_ter=c.cve_ter			
			and a.cve_cia=d.cve_cia
			and a.cve_cia in (select cve_cia from vw_usr_cia where usr='$usr')
			and a.stt_trj='$stt'
			and a.cve_cia='$cia'";
	}
	$_SESSION['userinfo']['sqltrj'] = $sql;
	if ($lim <> ""){
		$str = " ORDER BY fec_cap DESC LIMIT $min,$lim";
		$sql = $sql.$str;
	}
	//echo $sql."<br>";	
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$tmp=array();
			$tmp["cve_reg"] = $row["cve_reg"];			
			$tmp["cve_trj"] = $row["cve_trj"];
			$tmp["des_tip"] = $row["des_tip"];
			$tmp["cve_prs"] = $row["cve_prs"];
			$tmp["des_prs"] = $row["des_prs"];
			$tmp["des_ter"] = $row["des_ter"];
			$tmp["mon_costo"] = $row["mon_costo"];
			$tmp["moneda"] = $row["moneda"];
			$tmp["des_cia"] = $row["des_cia"];
			$tmp["stt_trj"] = $row["stt_trj"];
			$tmp["usr_cap"] = $row["usr_cap"];
			$tmp["fec_cap"] = $row["fec_cap"];
			$tmp["cmt_trj"] = $row["cmt_trj"];
			$tmp["vigencia"] = $row["vigencia"];
			array_push($arr, $tmp);
		}
	} else {
		//echo " Sin Resultados";
	}
	$conn->close();
	return $arr;
}


function getDataFromTable($conn, $sql){
	$response = array();
	//echo $sql;
	$stmt = $conn->prepare($sql);
	$stmt->execute();	
	$result = $stmt->get_result();
	while($row = $result->fetch_assoc())
		array_push($response, $row);
	$stmt->close();
	return $response;
}

function getDataFromView($conn, $sql){
	$response = array();
	//echo $sql;
	$result = $conn->query($sql);	
	while($row = $result->fetch_assoc())
		array_push($response, $row);
	//
	return $response;
}

function ValPerPag($usr,$pag){
	$pag=$pag.".phtml";
	$conn = getConnection();
	$sql = "select * from vw_usr_pag where usr='$usr' and url_pag='$pag'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		return 1;
	} else {
		return 0;
	}
}
function listTipoPV(){
	$conn = getConnection();
	$sql = "select * from cat_tip_ter";
	//echo $sql;
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($arr, $row);
		}
	} else {
		echo "Sin Resultados";
	}
	$conn->close();
	return $arr;
}

function listModoDisp(){
	$conn = getConnection();
	$sql = "select * from cat_mod_dis";
	//echo $sql;
	$result = $conn->query($sql);
	$arr = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($arr, $row);
		}
	} else {
		echo "Sin Resultados";
	}
	$conn->close();
	return $arr;
}