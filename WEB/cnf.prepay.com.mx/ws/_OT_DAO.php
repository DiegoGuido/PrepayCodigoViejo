<?php
function authenticateUser($conn, $auData){
    $table = "vw_login_recarga";
    $wcv = "where usr='".$auData["usuario"]."' and pwd = '".md5($auData["password"])."' and imei = '".$auData["imei"]."'";
    //echo $sql." ".$wcv;
    $login = getDataFromView($conn,$table,$wcv);	
    if(count($login) == 0 || count($login) > 2){
        return null;
    }else{
        $latitude  = "";
        $longitude = "";
        if(isset($auData["location"]) && !empty($auData["location"])){
            if(validateLatLong(explode(",",$auData["location"])[0],explode(",",$auData["location"])[1])){
                $latitude  = explode(",",$auData["location"])[0];
                $longitude = explode(",",$auData["location"])[1];              
            }else{
                include("_OT_LIB.php");
                writeLog("wrong_coordinates","USUARIO ".$auData["usuario"]." IMEI ".$auData["imei"]."\t COORDENADAS '".$auData["location"]."'");
            }
        }
        $sqlIL="insert into sis_log_app values(NULL, '".$auData["imei"]."', '$latitude', '$longitude', '".$auData["usuario"]."', '".date("Y-m-d H:i:s")."');";
        $conn->query($sqlIL);
        
        return array("userid"=>$login[0]["usr"],"name"=>$login[0]["nombre"],"profile"=>$login[0]["des_per"]);
    }
    
}
function retrieveConfiguration($conn, $inData){
    $wcd = isset($inData["lastupdate"]) && !empty($inData["lastupdate"])?"and SAT.fec_act > '".$inData["lastupdate"]."'":"";
	$wcd = "";
    $sql = "SELECT CD.imei,nom_tbl,des_tbl,SAT.cve_cia, tip_rel,tipo
            FROM sis_act_tbl SAT 
                left join cat_dis CD on CD.cve_cia =  SAT.cve_cia 
                left JOIN sis_rel_dis AS SRD ON SRD.imei = CD.imei
                where CD.imei = '".$inData["imei"]."' and tipo like if(tip_rel=1,'%C%','%R%') $wcd";  
    //echo $sql;
    $arrResponse = array();
    $arrayCats = getDataFromQuery($conn, $sql);
	//var_dump($arrayCats);
	//echo "-- ".count($arrayCats);
    if(count($arrayCats)== 0){
        $arrResponse["error"]=false;
        $arrResponse["message"]="No hay catalogos que actualizar para este dispositivo";
    }else{
        $arrValue = array();		
        foreach($arrayCats as $dc){			
            if(substr($dc["nom_tbl"],0,4)== "cat_"){
                $wcv = "";
            }else{
				if($dc["nom_tbl"] == "vw_ter_bal") {
					//echo "tabla: ".$dc["nom_tbl"];
					$wcv = " where imei = '".$inData["imei"]."' and usr_cap='".$inData["userId"]."'";
				}else{
					$wcv = " where imei = '".$inData["imei"]."'";
				}
            }
            $arrayCats[$dc["des_tbl"]]=array();
            $arrDataCatalog = getDataFromView($conn,$dc["nom_tbl"], $wcv);
            $arrValue[$dc["des_tbl"]] = $arrDataCatalog;
        }
        $arrResponse["error"]=false;
        $arrResponse["message"]="Catalogos obtenidos satisfactoriamente";
        $arrResponse["catalogs"]=$arrValue;		
    }
    return $arrResponse;
}
function valOpUsr($conn, $inData, $trnday){
	$arrResponse = array();		
	$sql = "select * from sis_tot_rec where usr_cap='".$inData["usuario"]."' and imei='".$inData["imei"]."' and fec_tran='$trnday'";
	//echo $sql;
	$r = mysqli_query($conn, $sql);
	if(($row = mysqli_fetch_assoc($r))){
		$arrResponse["error"]=true;
		$arrResponse["message"]="Cierre Realizado intente el dia de mañana";		
	}else{
		$arrResponse["error"]=false;
		//$arrResponse["message"]="Dispositivo Invalido";					
		}	
    $r ->close();
    return $arrResponse;
}
function retrieveInfDevice($conn, $imei){
    $sql = "SELECT '1001' as userId,'Miguel Angel' as name ,'Transportes Unidos CDMX' as unidad,'host/nfc/logos/101.png' as logo from cat_dis where imei = '$imei' and stt_dis='A'";
    //echo $sql;
    $rst = $conn->query($sql);
    if(($row = $rst->fetch_array())){ 
        $rst -> close();
        return $row;
    }
    return null;
}
function getDataFromQuery($conn, $sql){
	//echo $sql;
    $response = array();
    $stmt = $conn->prepare($sql);
    $stmt->execute();	
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        array_push($response, $row);
    }
    $stmt->close();	
    return $response;
}
function getDataFromView($conn, $vtable,$vclause){
    $response = array();
    $descView = "describe $vtable";
    //echo $descView."\n";
    $rdesc = mysqli_query($conn, $descView);
    $arrFields=array();
    while($rowView = mysqli_fetch_assoc($rdesc)){
        if($rowView["Field"]!="imei" && $rowView["Field"]!="usr_cap" && $rowView["Field"]!="fec_cap"){
            array_push($arrFields, $rowView["Field"]);
        }
    }
    $txtFields = implode(",",$arrFields);
    $rdesc->close();
    $sql = "select $txtFields from $vtable ".(!empty($vclause)?$vclause:"");
    //echo $sql."\n";
    $r = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($r)){
        array_push($response, $row);
    }
    $r ->close();
    return $response;
}
function validateLatLong($lat, $long) {
  return preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $lat.','.$long);
}
function getMaxAmount($conn,$imei){
    $sql = "select mon_top from vw_tope_recarga where imei = '$imei'";
    $r = mysqli_query($conn, $sql);
    if(($row = mysqli_fetch_assoc($r))){
        $tope = $row["mon_top"];
    }else{
        $tope = 0;
    }
    $r ->close();
    return $tope;
}
function getCardPrice($conn,$imei){
    $sql = "SELECT mon_costo FROM vw_tope_costos where imei='$imei'";
    $r = mysqli_query($conn, $sql);
    if(($row = mysqli_fetch_assoc($r))){
        $price = $row["mon_costo"];
    }else{
        $price = 0;
    }
    $r ->close();
    return $price;
}
/*function insertCustomerDetail($conn, $custDetail){
    $bdth = explode("/",$custDetail["birthday"])[2]."-".explode("/",$custDetail["birthday"])[1]."-".explode("/",$custDetail["birthday"])[0];
    $sql ="INSERT INTO sis_prs_trj (nom_cli, fec_nac, mail_cli, tel_cli, usr_cap, fec_cap) values ( ?,?,?,?,?,?);";
    $datetime = getSysDate(true);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name = $custDetail["name"]." ".$custDetail["surname"],$bdth ,$custDetail["email"],$custDetail["phoneNumber"],$custDetail["userId"], $datetime);
    $rc = $stmt->execute();
    if(false===$rc){
        $ret = $stmt->error; 
    }else{
       $ret = $stmt->insert_id;
    }
    $stmt->close();
    return $ret; 
}*/
function insertNewCard($conn, $cardInf, $idcustomization){
    $sql ="INSERT INTO sis_reg_trj (cve_trj, cve_tip, cve_prs, cve_ter, mon_costo, moneda, vigencia, cve_cia, stt_trj, usr_cap, fec_cap, tip_ter) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    $datetime = getSysDate(true);
    $date1year = new DateTime("now");
    $date1year->modify('+1 year');
    $valtru = $cardInf["type"]==2?$date1year->format("Y-m-d"):null;
    $stt_trj = "A";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiidssissss", $cardInf["idCard"],$cardInf["type"],$idcustomization,$cardInf["pointOfSaleId"],$cardInf["cardPrice"],$cardInf["currency"], $valtru,$cardInf["idCompany"],$stt_trj,$cardInf["userId"], $datetime, $cardInf["tip_ter"]);
    $rc = $stmt->execute();
    if(false===$rc){
        $ret = $stmt->error." ***"; 
        if($idcustomization != null){
            $stmtDel = $conn->prepare("delete from sis_prs_trj where cve_prs = $idcustomization");
            $stmtDel->execute();
        }
    }else{
        $ret = $stmt->insert_id;
    }
    $stmt->close();
    return $ret; 
}
function insertRecharge($conn, $recValue){
	//echo "fecha: ".$recValue["fecap"];
	if ($recValue["fecap"] != ""){
		$datetime = $recValue["fecap"];
	}else{
		$datetime = getSysDate(true);
	}
    $dRecharge = explode(" ",$recValue["dateTimeRecharge"])[0];
    $latitude  = ""; $longitude = "";
    if(isset($recValue["location"]) && !empty($recValue["location"])){
        if(validateLatLong(explode(",",$recValue["location"])[0],explode(",",$recValue["location"])[1])){
            $latitude  = explode(",",$recValue["location"])[0];
            $longitude = explode(",",$recValue["location"])[1];              
        }else{
            include("_OT_LIB.php");
            writeLog("wrong_coordinates","USUARIO ".$recValue["usuario"]." IMEI ".$recValue["imei"]."\t COORDENADAS '".$recValue["location"]."'");
        }
    }
    $timeRecharge = strtotime(explode(" ",$recValue["dateTimeRecharge"])[1]." ".explode(" ",$recValue["dateTimeRecharge"])[2]);
    $fTimeRecharge = date("H:i:s", $timeRecharge);
    $dtRecharge = explode("/",$dRecharge)[2]."-".explode("/",$dRecharge)[1]."-".explode("/",$dRecharge)[0]." ".$fTimeRecharge;
    $saldo = isset($recValue["balance"])?$recValue["balance"]:0;
    $transaction = isset($recValue["balance"])?"recargada":"registrada";
    $cve_mov = isset($recValue["balance"])?"R":"C";
	$tip_mov = $cve_mov; //Solo para operation
	if ($tip_mov == "C"){ $tip_mov="V"; } //Solo para operation
    //echo "insert into sis_rec_trj ( cve_ter, monto, moneda, cve_mov, cve_trj, cve_tip, saldo, fec_rec, usr_cap, fec_cap, imei, latitud, longitud, cve_ter) values ('".$recValue['pointOfSaleId']."','".$recValue['netCharge']."','".$recValue['currency']."','".$cve_mov."','".$recValue['idCard']."','".$recValue['type']."','".$saldo."','".$dtRecharge."','".$recValue['userId']."','".$datetime."','".$recValue['imei']."','".$latitude."','".$longitude."','".$recValue['tip_ter']."')";	
	$sql = "insert into sis_rec_trj ( cve_ter, monto, moneda, cve_mov, cve_trj, cve_tip, saldo, fec_rec, usr_cap, fec_cap, imei, latitud, longitud, tip_ter) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssdsssssss", $recValue["pointOfSaleId"],$recValue["netCharge"],$recValue["currency"],$cve_mov, $recValue["idCard"],$recValue["type"],$saldo, $dtRecharge, $recValue["userId"], $datetime, $recValue["imei"], $latitude, $longitude, $recValue["tip_ter"]);
    $rc = $stmt->execute();
    $ret = array();
	$ret2 = array();
    if(false===$rc){
        $ret["status"] = false;
        $ret["message"] = $stmt->error; 
        /*$stmtDelCust = $conn->prepare("delete from sis_prs_trj where cve_prs = (select cve_prs from sis_reg_trj where cve_trj = ".$recValue["idCard"].")");
        $stmtDelCust->execute();
        $stmtDelCard = $conn->prepare("delete from sis_reg_trj where cve_trj = ".$recValue["idCard"]);
        $stmtDelCard->execute();*/
    }else{
        $ret["status"] = true;
        $ret["folio"] = $stmt->insert_id;
        $ret["message"] = "Tarjeta $transaction correctamente1.";		
		$ret2["idCard"] = $recValue["idCard"];
		$ret2["dateTimeTrx"] = $recValue["dateTimeRecharge"];
		$ret2["operation"] = $tip_mov;
		$ret2["terminalbalance"] = returnBalance($conn,$recValue["imei"],$recValue["userId"]);		
		$ret["response"] = $ret2;
		//print_r($ret);
    }
    $stmt->close();
    return $ret;
}
function insertCharge($conn, $chargeValue){
    if ($chargeValue["fecap"] != ""){
		$datetime = $chargeValue["fecap"];
	}else{
		$datetime = getSysDate(true);
	}
    $dCharge = explode(" ",$chargeValue["dateTimeCharge"])[0];
    $latitude  = ""; $longitude = "";
    if(isset($chargeValue["location"]) && !empty($chargeValue["location"])){
        if(validateLatLong(explode(",",$chargeValue["location"])[0],explode(",",$chargeValue["location"])[1])){
            $latitude  = explode(",",$chargeValue["location"])[0];
            $longitude = explode(",",$chargeValue["location"])[1];              
        }else{
            include("_OT_LIB.php");
            writeLog("wrong_coordinates","USUARIO ".$chargeValue["usuario"]." IMEI ".$chargeValue["imei"]."\t COORDENADAS '".$chargeValue["location"]."'");
        }
    }
    $timeCharge = strtotime(explode(" ",$chargeValue["dateTimeCharge"])[1]." ".explode(" ",$chargeValue["dateTimeCharge"])[2]);
    $fTimeCharge = date("H:i:s", $timeCharge);
    $dtCharge = explode("/",$dCharge)[2]."-".explode("/",$dCharge)[1]."-".explode("/",$dCharge)[0]." ".$fTimeCharge;
    
    
    $cve_mov = $chargeValue["chargeType"];
	//echo "--".$cve_mov;
    if($cve_mov == "A"){
		$saldo = isset($chargeValue["balance"])?$chargeValue["balance"]:0;
		/*$sql = "INSERT INTO sis_cob_trj ( fol_dis, cve_uni, monto, saldo, moneda, cve_mov, cve_trj, cve_tip, cve_rut, cve_lin, fec_cob, imei, latitud, longitud, fec_cap) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("iiddsssiiisssss", $chargeValue["transactionId"],$chargeValue["idUnit"],$chargeValue["netCharge"],$saldo,$chargeValue["currency"],$cve_mov, $chargeValue["idCard"],$chargeValue["type"],$chargeValue["idRoute"],$chargeValue["idLine"], $dtCharge, $chargeValue["imei"], $latitude, $longitude, $datetime);*/
		$sql = "INSERT INTO sis_cob_trj ( fol_dis, cve_uni, monto, saldo, moneda, cve_mov, cve_trj, cve_tip, cve_rut, cve_lin, fec_cob, imei, latitud, longitud, fec_cap, counter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("iiddsssiiisssssi", $chargeValue["transactionId"],$chargeValue["idUnit"],$chargeValue["netCharge"],$saldo,$chargeValue["currency"],$cve_mov, $chargeValue["idCard"],$chargeValue["type"],$chargeValue["idRoute"],$chargeValue["idLine"], $dtCharge, $chargeValue["imei"], $latitude, $longitude, $datetime, $chargeValue["counter"]);
    }else{
		$sql = "INSERT INTO sis_cob_efe ( fol_dis, cve_uni, monto, moneda, cve_mov, cve_rut, cve_lin, fec_cob, imei, latitud, longitud, fec_cap) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("iidssiisssss", $chargeValue["transactionId"],$chargeValue["idUnit"],$chargeValue["netCharge"],$chargeValue["currency"],$cve_mov,$chargeValue["idRoute"],$chargeValue["idLine"], $dtCharge, $chargeValue["imei"], $latitude, $longitude, $datetime);

	}
	$rc = $stmt->execute();
    $ret = array();
	$ret2 = array();
    if(false===$rc){
        $ret["status"] = false;
        $ret["message"] = $stmt->error; 
    }else{
        $ret["status"] = true;
        $ret["folio"] = $stmt->insert_id;
        $ret["message"] = "Cobro realizado correctamente.";
		$ret2["idCard"] = $chargeValue["idCard"];
		$ret2["dateTimeTrx"] = $chargeValue["dateTimeCharge"];
		$ret2["operation"] = "C";
		$ret["response"] = $ret2;
    }
    $stmt->close();
    return $ret;
}
function insertTracking($conn, $track){
	$sql = "INSERT INTO sis_trk_uni ( cve_uni, cve_lin, cve_cia, cve_rut, latitud, longitud, fec_trk, imei) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
	$lat = explode(",", $track["location"])[0];
	$lon = explode(",", $track["location"])[1];
	$dTrack = explode(" ",$track["dateTimeTrack"])[0];
	$timeTrack = strtotime(explode(" ",$track["dateTimeTrack"])[1]." ".explode(" ",$track["dateTimeTrack"])[2]);
	$fTimeTrack = date("H:i:s", $timeTrack);
	$dtTrack = explode("/",$dTrack)[2]."-".explode("/",$dTrack)[1]."-".explode("/",$dTrack)[0]." ".$fTimeTrack;
    $stmt->bind_param("iiiissss", $track["idUnit"],$track["idLine"],$track["idCompany"],$track["idRoute"],$lat,$lon, $dtTrack,$track["imei"]);
    $rc = $stmt->execute();
	$ret = array();
    if(false===$rc){
        $ret["status"] = false;
        $ret["message"] = $stmt->error; 
    }else{
        $ret["status"] = true;
        $ret["folio"] = $stmt->insert_id;
        $ret["message"] = "Tracking registrado correctamente."; 
		$inspts = insertTrackingPoints($conn, $track);
		if ($inspts["status"] && $track["modo"] == 'A'){
		//$ret["AutoCharge"] = "12.00";	
		$ret["AutoCharge"]= selectAutoCharge($conn,$lat,$lon,$track["idRoute"],$track["idLine"],$track["idUnit"],$dTrack);
		}
    }
    $stmt->close();
    return $ret;
}

function insertTrackingPoints($conn, $track){
	$dTrack = explode(" ",$track["dateTimeTrack"])[0];
	$dtTrack = explode("/",$dTrack)[2]."-".explode("/",$dTrack)[1]."-".explode("/",$dTrack)[0];
	$sql = "REPLACE INTO sis_trk_pts 
	select x.cve_reg,x.cve_uni,x.cve_rut,x.cve_lin,x.cve_cia,x.fec_trk, x.latitud, x.longitud,x.tip_crd, x.des_crd,x.geocerca, x.distance, x.tar1, x.tar2 from (
	 select a.cve_reg,a.cve_uni,a.cve_rut,a.cve_lin,a.cve_cia,a.fec_trk, a.latitud, a.longitud,b.tip_crd, b.des_crd,b.geocerca, 
	 (6371 * acos(cos(radians(b.lat)) * cos(radians(a.latitud)) * cos(radians(a.longitud) - radians(b.lon)) + sin(radians(b.lat)) * sin(radians(a.latitud))))AS distance, 
	 if((lag(b.des_crd,1) over (PARTITION BY a.cve_uni order by a.fec_trk)) = des_crd,0,1) as comp,
	 lag(b.tip_crd,1) over (PARTITION BY a.cve_uni order by a.fec_trk) as lsttip,
	 b.tar1,b.tar2
	 FROM sis_trk_uni a, sis_crd_rut b
	 WHERE a.cve_rut=b.cve_rut 
	 and a.fec_trk >= '$dtTrack 00:00:00' 
	 and a.fec_trk <= '$dtTrack 23:59:59' 
	 and a.cve_cia='".$track["idCompany"]."' 
	 and a.cve_lin='".$track["idLine"]."'
	 and a.cve_rut='".$track["idRoute"]."'
	 and a.cve_uni='".$track["idUnit"]."' 
	 HAVING distance < b.geocerca 
	 ORDER BY a.cve_uni,a.fec_trk) x 
	 where x.comp=1";
	//echo $sql;
    $stmt = $conn->prepare($sql);	
    $rc = $stmt->execute();
	$ret = array();
    if(false===$rc){
        $ret["status"] = false;
        $ret["message"] = $stmt->error; 
    }else{
        $ret["status"] = true;        
	}    
    $stmt->close();
    return $ret;
}

function selectAutoCharge($conn,$lat,$lon,$rut,$lin,$uni,$dTrack){
	$fec = explode("/",$dTrack)[2]."-".explode("/",$dTrack)[1]."-".explode("/",$dTrack)[0]." ".$fTimeTrack;
	$sql="select b.tip_crd, b.des_crd,b.geocerca, 
		(6371 * acos(cos(radians(b.lat)) * cos(radians($lat)) * cos(radians($lon) - radians(b.lon)) + sin(radians(b.lat)) * sin(radians($lat))))AS distancepts,
		b.tar1,b.tar2
		FROM sis_crd_rut b
		where b.cve_rut=$rut
		HAVING distancepts < b.geocerca
		UNION
		select c.tip_crd, c.des_crd,c.geocerca, 
		(6371 * acos(cos(radians(c.latitud)) * cos(radians($lat)) * cos(radians($lon) - radians(c.longitud)) + sin(radians(c.latitud)) * sin(radians($lat))))AS distancelst,c.tar1,c.tar2
		FROM sis_trk_pts c
		where c.cve_rut=$rut
		and c.cve_uni =$uni
		and c.fec_trk = (select max(c1.fec_trk)
						from sis_trk_pts c1
						where c1.cve_rut=$rut
						and c1.cve_uni=$uni
						and c1.fec_trk >= '$fec 00:00:00' 
						and c1.fec_trk <= '$fec 23:59:59')";
	//echo $sql;
    $rate = getDataFromQuery($conn, $sql)[0];
	//print_r($rate);
	$rate1 = $rate["tar1"];
	//echo $rate1;
    return $rate1;
}

function insertIncidence($conn, $incidence){
	$sql = "INSERT INTO sis_reg_inc ( cve_uni, cve_lin, cve_cia,cve_rut, cve_inc,desc_inc, latitud, longitud, fec_inc, imei) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
	$lat = explode(",", $incidence["location"])[0];
	$lon = explode(",", $incidence["location"])[1];
	$dIncidence = explode(" ",$incidence["dateTimeIncidence"])[0];
	$timeIncidence = strtotime(explode(" ",$incidence["dateTimeIncidence"])[1]." ".explode(" ",$incidence["dateTimeIncidence"])[2]);
	$fTimeIncidence = date("H:i:s", $timeIncidence);
	$dtIncidence = explode("/",$dIncidence)[2]."-".explode("/",$dIncidence)[1]."-".explode("/",$dIncidence)[0]." ".$fTimeIncidence;
    $stmt->bind_param("iiiiisssss", $incidence["idUnit"],$incidence["idLine"],$incidence["idCompany"],$incidence["idRoute"],$incidence["idIncidence"],$incidence["description"],$lat,$lon, $dtIncidence,$incidence["imei"]);
    $rc = $stmt->execute();
	$ret = array();
    if(false===$rc){
        $ret["status"] = false;
        $ret["message"] = $stmt->error; 
    }else{
        $ret["status"] = true;
        $ret["folio"] = $stmt->insert_id;
        $ret["message"] = "Incidencia registrada correctamente."; 
    }
    $stmt->close();
    return $ret;
}
function isRegisteredCard($conn, $idCard){
    $val_trj = getDataFromQuery($conn, "select count(*) as existsCard from sis_reg_trj where cve_trj = '".$idCard."'")[0]["existsCard"];
    return $val_trj>0;
}
function validateCompanyCard($conn, $idCard){
    $val_trj = getDataFromQuery($conn, "select count(*) as existsCard from sis_reg_trj where cve_trj = '".$idCard."'")[0]["existsCard"];
    return $val_trj>0;
}
function isBannedCard($conn, $idCard){
    $sql = "select count(*) as bannedcard from vw_lista_negra where cve_trj = '$idCard'";
    //echo $sql;
    $r = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($r);
    $bannedcard = !empty($row["bannedcard"]);
    $r ->close();
    return $bannedcard;
}
function validateThruDate($conn, $idCard){
    $val_trhu = getDataFromQuery($conn, "select if(cve_tip = 2, if(date(vigencia) >= '".date("Y-m-d")."',1,0),1) as validThru from sis_reg_trj where cve_trj = '$idCard' ");
    if(count($val_trhu)> 0){
        return $val_trhu[0]["validThru"]==1;
    }else{
        return false;
    }
    return $val_trhu > 0;
}
function validateUserId($conn, $userId){
    $val_usr = getDataFromQuery($conn, "select count(*) as existsUser from cat_usr where usr = '".$userId."' and stt_usr = 'A'")[0]["existsUser"];
    return $val_usr > 0;
}
function loadCompanyProfile($conn, $inData){
	$sql = "SELECT D.cve_cia as cve_cia, 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png' as imagen, '' as color from cat_dis D left join cat_cia C on C.cve_cia= D.cve_cia
                where D.imei = '".$inData["imei"]."'";  
   // echo $sql;
    $arrResponse = array();
	$arrResponse["error"]=true;
    $arrayData = getDataFromQuery($conn, $sql);
	//var_dump($arrayData);
	//echo "-- ".count($arrayData);
    if(count($arrayData)== 0){
        $arrResponse["message"]="Dispositivo no encontrado";
    }else{
		if(count($arrayData)> 1){
			$arrResponse["message"]="Mas de un dispositivo con el mismo IMEI";
		}else{
			$compInfo = $arrayData[0];
			if(empty($compInfo["cve_cia"])){
				$arrResponse["message"]="Dispositivo no tiene compañia asociada";
			}else{
				if(empty($compInfo["imagen"])){
					$arrResponse["message"]="Compañia no tiene imagen asociada";
				}else{
					if(!urlExists($compInfo["imagen"])){
						$arrResponse["message"]="Problemas al comprobar imagen asociada.";
					}else{
						$arrResponse["error"]=false;
						$arrResponse["message"]="Información recuperada correctamente.";
						$arrResponse["companyProfile"]=array();
						$arrResponse["companyProfile"]["image"]=$compInfo["imagen"];
						if(empty($compInfo["color"])){
							$arrResponse["companyProfile"]["colorHex"]="#FFFFFF";
							$arrResponse["companyProfile"]["colorRGB"]="255,255,255";
						}else{
							$arrResponse["companyProfile"]["colorHex"]=$compInfo["color"];
							$arrResponse["companyProfile"]["colorRGB"]=colorHex2RGB($compInfo["color"]);
						}
					}
				}
			}
		}
        
    }
    return $arrResponse;
}
function getSysDate($it){
    $today = new DateTime("now");
    if($it){
        return $today->format("Y-m-d H:i:s");
    }else{
        return $today->format("Y-m-d");
    }
}
function urlExists($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($code == 200) {
        $status = true;
    } else {
        $status = false;
    }
    curl_close($ch);
    return $status;
}
function colorHex2RGB($hex){
	list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
	echo "$r,$g,$b";
}

function getValidCard($conn, $inData){
	$arrResponse = array();	
	if($inData['idPrcs'] == "V"){
		$sql = "select x.cve_trj from 
				(select cve_trj from sis_reg_trj
				 UNION
				 select cve_trj from sis_inv_crd)x 
				 where x.cve_trj='".$inData['idCard']."'";		
		$r = mysqli_query($conn, $sql);
		if(($row = mysqli_fetch_assoc($r))){
			$arrResponse["error"]=true;
			$arrResponse["message"]="Tarjeta Existente";
			//$arrResponse["resp"]=$sql;
		}else{
			$arrResponse["error"]=false;
			$arrResponse["message"]="Tarjeta Inexistente";
		}		
	}else{		
		$sql = "select cve_trj from sis_reg_trj where cve_trj='".$inData['idCard']."' and stt_trj='A'";		
		$r = mysqli_query($conn, $sql);
		if(($row = mysqli_fetch_assoc($r))){
			$arrResponse["error"]=false;
			$arrResponse["message"]="Tarjeta valida";
		}else{
			$arrResponse["error"]=true;
			$arrResponse["message"]="Tarjeta Invalida 1";			
			$motivo = "Tarjeta No Registrada";
			//$arrResponse["insert"]=insertInvalidCard($conn, $inData, $motivo);
			
		}
	}
    $r ->close();
    return $arrResponse;
}

function insertInvalidCard($conn, $inData, $motivo){
	$sql = "insert into sis_inv_crd (cve_trj,cve_rel,cve_cia,fec_cap,latitud,longitud,cmt_trj,imei,usr,tip_rel) values (?,?,?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
	$lat = explode(",", $inData["location"])[0];
	$lon = explode(",", $inData["location"])[1];
	$dVal = explode(" ",$inData["dateTimeCheck"])[0];
	$timeVal = strtotime(explode(" ",$inData["dateTimeCheck"])[1]." ".explode(" ",$inData["dateTimeCheck"])[2]);
	$fTimeVal = date("H:i:s", $timeVal);
	$dtVal = explode("/",$dVal)[2]."-".explode("/",$dVal)[1]."-".explode("/",$dVal)[0]." ".$fTimeVal;
	if ($inData["idPrcs" == "C"]){
		$tiprel = 1;
	} else {
		$tiprel = 2;
	}
    $stmt->bind_param("siissssssi", $inData["idCard"],$inData["idReference"],$inData["idCompany"],$dtVal,$lat,$lon,$motivo,$inData["imei"],$inData["user"],$tiprel);
    $rc = $stmt->execute();
	$ret = array();
    if(false===$rc){
        //$ret["status"] = false;
        $ret["message"] = $stmt->error; 
    }else{
        //$ret["status"] = true;
        $ret["folio"] = $stmt->insert_id;
        //$ret["message"] = "Incidencia registrada correctamente."; 
    }
    $stmt->close();
    return $ret;
}

function getBlackList($conn, $inData){
	$arrResponse = array();
	//if ($inData["updfec"] == ""){
	$sup="select x.cve_trj from ( ";
	$sqlina="select cve_trj,fec_cap
	from sis_reg_trj
	where stt_trj='I'
	and cve_cia in (select cve_cia from cat_dis where imei='".$inData["imei"]."')";
	$sqlinv="select cve_trj,fec_cap";
	$inf = " from sis_inv_crd) x";
	$uni =	" UNION ";
	$fecupd = " where x.fec_cap >= '".$inData["updfec"]."'";
	//$sql = $sup.$sqlinv.$inf;
	$sql = $sup.$sqlina.$uni.$sqlinv.$inf;
	//$sql = $sup.$sqlina.$uni.$sqlinv.$inf.$fecupd;
	//echo $sql;    
    $arrayCards = getDataFromQuery($conn, $sql);	
	//echo "-- ".count($arrayCards);
    if(!empty($arrayCards)){		
		$arrResponse["error"]=false;        
        $arrResponse["message"]="Lista Negra Actualizada correctamente";
		$arrResponse["cards"] = $arrayCards;
	
    }else{      
		$arrResponse["error"]=true;
        $arrResponse["message"]="Nada que Actualizar";
    }
    return $arrResponse;
}

function getValidDevice($conn, $inData){
	$arrResponse = array();
	$sql = "select imei,cve_cia,cve_lin from vw_rel_dis where imei='".$inData['imei']."' and stt_rel='A'";		
	//echo $sql;
	$r = mysqli_query($conn, $sql);
	if(($row = mysqli_fetch_assoc($r))){
		$arrResponse["error"]=true;
		$arrResponse["message"]="Dispositivo Valido";
		$arrResponse["device"]=$row;
	}else{
		$arrResponse["error"]=false;
		$arrResponse["message"]="Dispositivo Invalido";					
		}	
    $r ->close();
    return $arrResponse;
}
function isPersonalizedCard($conn, $idCard){
	$sql="select count(*) as existsCard from sis_prs_trj where cve_trj = '".$idCard."'";
	//echo $sql;
    $val_trj = getDataFromQuery($conn,$sql)[0]["existsCard"];
    return $val_trj>0;
}
function insertCustomerDetail1($conn, $custDetail){
    $bdth = explode("/",$custDetail["birthday"])[2]."-".explode("/",$custDetail["birthday"])[1]."-".explode("/",$custDetail["birthday"])[0];
	$latitude  = ""; $longitude = "";
    if(isset($custDetail["location"]) && !empty($custDetail["location"])){
        if(validateLatLong(explode(",",$custDetail["location"])[0],explode(",",$custDetail["location"])[1])){
            $latitude  = explode(",",$custDetail["location"])[0];
            $longitude = explode(",",$custDetail["location"])[1];              
        }else{
            include("_OT_LIB.php");
            writeLog("wrong_coordinates","USUARIO ".$custDetail["userId"]." IMEI ".$custDetail["imei"]."\t COORDENADAS '".$custDetail["location"]."'");
        }
	}    
    /*$sqldat = "INSERT INTO sis_prs_trj_1 (cve_trj, nom_cli, fec_nac, mail_cli, tel_cli, fiscal_id, photo, cve_ter, imei, latitud, longitud, usr_cap, fec_cap) 
	           values ('".$custDetail["idCard"]."','".$custDetail['name']." ".$custDetail['surname']."'";
			   //,'".$bdth"','".$custDetail['email']."','".$custDetail['phoneNumber']."','".$custDetail['fiscalid']."','".$custDetail['photo']."','".$custDetail['pointOfSaleId']."','".$custDetail['imei']."','".$latitude."','".$longitude."','".$custDetail['userId']."','".$datetime."')";
	echo $sqldat;*/
	$sql ="INSERT INTO sis_prs_trj (cve_trj, nom_cli, fec_nac, mail_cli, tel_cli, fiscal_id, photo, cve_ter, imei, latitud, longitud, usr_cap, fec_cap) values (?,?,?,?,?,?,?,?,?,?,?,?,?);";
	$datetime = getSysDate(true);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssisssss", $custDetail["idCard"], $name = $custDetail["name"]." ".$custDetail["surname"],$bdth ,$custDetail["email"], $custDetail["phoneNumber"], $custDetail["fiscalid"], $custDetail["userpic"], $custDetail["pointOfSaleId"], $custDetail["imei"],$latitude,$longitude,$custDetail["userId"], $datetime);
    $rc = $stmt->execute();
    if(false===$rc){
        $ret = $stmt->error; 
    }else{
        $ret = $stmt->insert_id;
		$sql2 = "update sis_reg_trj set cve_prs = '$ret' where cve_trj='".$custDetail["idCard"]."'";
		$stmt2 = $conn->prepare($sql2);
		$rc2 = $stmt2->execute();
		if(false===$rc){
			$ret2 = $stmt2->error;
		}else{
			$ret2 = $stmt2->insert_id;
		}
    }
    $stmt->close();
    return $ret; 
}
function getTerminalRecharge($conn, $inData){
	$datetime = getSysDate(true);
	$usr = $inData["userId"];
	$imei = $inData["imei"];
	$arrResponse = array();	
	$sql = "update sis_rec_ter set stt_act='1', fec_act='$datetime' where imei='$imei' and usr_rec='$usr' and stt_rec='A' and stt_act='0'";
	$stmt = $conn->prepare($sql);			
	$rc = $stmt->execute();
	if(false===$rc){
		$ret = $stmt->error; 
		$arrResponse["error"]=true;
        $arrResponse["message"]="Nada que Actualizar";
	}else{
		$ret = $stmt->affected_rows;
		$sql = "select * from vw_ter_bal where imei='$imei' and usr_cap='$usr'";	
		$arrayTmp = getDataFromViewTable($conn, $sql);
		if(!empty($arrayTmp)){		
			$arrResponse["error"]=false;
			$arrResponse["message"]="Saldo Actualizado";		
			$arrResponse["balance"] = array_values($arrayTmp)[0];
		
		}else{      
			$arrResponse["error"]=true;
			$arrResponse["message"]="Nada que Actualizar";
		}
	}	
		
    return $arrResponse;
}

function getDataFromViewTable($conn, $sql){
	//echo $sql;
	$response = array();	
	$result = $conn->query($sql);	
	while($row = $result->fetch_assoc()){
		array_push($response, $row);
	}
	return $response;
}

function insertTotalRecharge($conn, $data){	
	$sql = "INSERT INTO sis_tot_rec (tot_rec, tot_vta, tot_tar, usr_cap, fec_tran, imei, cve_ter, fec_cap) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
	$datetime = getSysDate(true);
	$dTransaction = explode(" ",$data["dateTimeTransaction"])[0];
	$tTransaction = strtotime(explode(" ",$data["dateTimeTransaction"])[1]." ".explode(" ",$data["dateTimeTransaction"])[2]);
	$fTimeTransaction = date("H:i:s", $tTransaction);
	$dtTransaction = explode("/",$dTransaction)[2]."-".explode("/",$dTransaction)[1]."-".explode("/",$dTransaction)[0]." ".$fTimeTransaction;
    $stmt->bind_param("ddisssis", $data["totalRecharge"],$data["totalSale"],$data["totalCard"],$data["userId"],$dtTransaction,$data["imei"],$data["pointOfSaleId"],$datetime);
    $rc = $stmt->execute();
	$ret = array();
    if(false===$rc){
        $ret["error"] = true;
        $ret["message"] = $stmt->error; 
    }else{
        $ret["error"] = false;        
        $ret["message"] = "Cierre Realizado Correctamente"; 
		$ret["folio"] = $stmt->insert_id;
    }
    $stmt->close();
    return $ret;
}

function insertTotalCharge($conn, $data,$trjtot,$cnt,$jsntrj){	
	$sql = "INSERT INTO sis_tot_cob (tot_trj, tot_efe, tot_qr, cve_uni, usr_cap, fec_tran, imei, fec_cap) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
	$datetime = getSysDate(true);		
	$totqr=0;
	$dTransaction = explode(" ",$data["dateTimeTransaction"])[0];
	$tTransaction = strtotime(explode(" ",$data["dateTimeTransaction"])[1]." ".explode(" ",$data["dateTimeTransaction"])[2]);
	$fTimeTransaction = date("H:i:s", $tTransaction);
	$dtTransaction = explode("/",$dTransaction)[2]."-".explode("/",$dTransaction)[1]."-".explode("/",$dTransaction)[0]." ".$fTimeTransaction;
    $stmt->bind_param("dddissss",$trjtot,$data["total_E"],$totqr,$data["idUnit"],$data["userId"],$dtTransaction,$data["imei"],$datetime);
    $rc = $stmt->execute();
	$ret = array();
    if(false===$rc){
        $ret["error"] = true;
        $ret["message"] = $stmt->error; 
    }else{
        $ret["error"] = false;
		$fol = $stmt->insert_id;
		if ($fol <> ""){
			for ($i=0; $i<=$cnt-1; $i++){				
				$trjdet=insertChargeDetail($conn,$fol,$jsntrj[$i]["id"],$jsntrj[$i]["total"]);
			}
		}
        $ret["message"] = "Cierre Realizado Correctamente"; 
		$ret["folio"] = $fol;
    }
    $stmt->close();
    return $ret;
}

function insertChargeDetail($conn,$fol,$tip,$tot){
	$sql = "insert into sis_tot_cob_det (cve_tot_cob,cve_tip,tot_trj) values ('$fol','$tip','$tot')";
	//echo $sql;
    $stmt = $conn->prepare($sql);
    $rc = $stmt->execute();
	$ret = array();
    if(false===$rc){
        $ret["error"] = true;
        $ret["message"] = $stmt->error; 
    }else{
        $ret["error"] = false;                
    }
    $stmt->close();
    return $ret;
}

function validateConcentrated($conn,$data){
	$dTransaction = explode(" ",$data["dateTimeTransaction"])[0];
	$fec = explode("/",$dTransaction)[2]."-".explode("/",$dTransaction)[1]."-".explode("/",$dTransaction)[0]." ".$fTimeTransaction;
	$usr = $data["userId"];
	$tip = $data["conType"];
	$imei = $data["imei"];	
	
	if ($tip='R'){
		$sql="select cve_reg from sis_tot_rec where fec_tran='$fec' and usr_cap='$usr' and imei='$imei'";
	}
	if ($tip='C'){
		$sql="select cve_reg from sis_tot_cob where fec_tran='$fec' and usr_cap='$usr' and imei='$imei'";				
		$jsntrj=json_decode($data["totalTarjetas"],true);		
		$cnt=count($jsntrj);		
		$trjtot=0;
		for ($i=0; $i<=$cnt-1; $i++){
			$trjtot += $jsntrj[$i]["total"];
		}
		//echo "total: ".$trjtot;
		
	}
	$r = mysqli_query($conn, $sql);
	if(($row = mysqli_fetch_assoc($r))){
		$arrResponse["error"]=true;
		$arrResponse["message"]="Ya existen Registros con los datos enviados";
		$arrResponse["folio"]=$row["cve_reg"];
	}else{
		$arrResponse["error"]=false;
		$arrResponse["jsntrj"]=$jsntrj;
		$arrResponse["cnt"]=$cnt;
		$arrResponse["trjtot"]=$trjtot;
		}	
	//echo $arrResponse["message"];
	//echo "valtot: ".$arrResponse["trjtot"];
    $r ->close();
    return $arrResponse;
}
function returnMultidata($conn, $imei, $fecap){
	$sql="select imei, cve_reg as folio, cve_trj as idCard, fec_rec as dateTimeTrx, case when cve_mov='C' then 'V' else cve_mov end as operation from sis_rec_trj where imei='$imei' and fec_cap='$fecap'";
	//echo $sql;
    $ret = getDataFromQuery($conn, $sql);
    return $ret;
}

function returnMultiCharge($conn, $imei, $fecap){
	$sql="select true as status, imei, cve_reg as folio, cve_trj as idCard, fec_cob as dateTimeTrx, 'C' as operation from sis_cob_trj where imei='$imei' and fec_cap='$fecap'
		UNION
		select true as status, imei, cve_reg as folio, '' as idCard, fec_cob as dateTimeTrx, 'C' as operation from sis_cob_efe where imei='$imei' and fec_cap='$fecap'
		UNION
		select false as status, imei, '' as folio, cve_trj as idCard, fec_cob as dateTimeTrx, '' as operation from sis_cob_trj_err where imei='$imei' and fec_cap='$fecap'";
	//echo $sql;
    $ret = getDataFromQuery($conn, $sql);
    return $ret;
}


function insertChargeErr($conn, $chargeValue, $errdesc){
    if ($chargeValue["fecap"] != ""){
		$datetime = $chargeValue["fecap"];
	}else{
		$datetime = getSysDate(true);
	}
    $dCharge = explode(" ",$chargeValue["dateTimeCharge"])[0];
    $latitude  = ""; $longitude = "";
    if(isset($chargeValue["location"]) && !empty($chargeValue["location"])){
        if(validateLatLong(explode(",",$chargeValue["location"])[0],explode(",",$chargeValue["location"])[1])){
            $latitude  = explode(",",$chargeValue["location"])[0];
            $longitude = explode(",",$chargeValue["location"])[1];              
        }else{
            include("_OT_LIB.php");
            writeLog("wrong_coordinates","USUARIO ".$chargeValue["usuario"]." IMEI ".$chargeValue["imei"]."\t COORDENADAS '".$chargeValue["location"]."'");
        }
    }
    $timeCharge = strtotime(explode(" ",$chargeValue["dateTimeCharge"])[1]." ".explode(" ",$chargeValue["dateTimeCharge"])[2]);
    $fTimeCharge = date("H:i:s", $timeCharge);
    $dtCharge = explode("/",$dCharge)[2]."-".explode("/",$dCharge)[1]."-".explode("/",$dCharge)[0]." ".$fTimeCharge;
    
    
    $cve_mov = $chargeValue["chargeType"];
	//echo "--".$cve_mov;
    if($cve_mov == "A"){
		$saldo = isset($chargeValue["balance"])?$chargeValue["balance"]:0;		
		//$sql = "INSERT INTO sis_cob_err ( fol_dis, cve_uni, monto, saldo, moneda, cve_mov, cve_trj, cve_tip, cve_rut, cve_lin, fec_cob, imei, latitud, longitud, fec_cap, counter) VALUES ('".$chargeValue['transactionId']."','".$chargeValue['idUnit']."','".$chargeValue['netCharge']."','".$saldo."','".$chargeValue['currency']."','".$cve_mov."','".$chargeValue['idCard']."','".$chargeValue['type']."','".$chargeValue['idRoute']."','".$chargeValue['idLine']."','".$dtCharge."','".$chargeValue['imei']."','$latitude','$longitude','$datetime','".$chargeValue['counter']."')";
		//echo "sql: ".$sql;
		$sql = "INSERT INTO sis_cob_trj_err ( fol_dis, cve_uni, monto, saldo, moneda, cve_mov, cve_trj, cve_tip, cve_rut, cve_lin, fec_cob, imei, latitud, longitud, fec_cap, counter, dsc_err) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";		
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("iiddsssiiisssssis", $chargeValue["transactionId"],$chargeValue["idUnit"],$chargeValue["netCharge"],$saldo,$chargeValue["currency"],$cve_mov, $chargeValue["idCard"],$chargeValue["type"],$chargeValue["idRoute"],$chargeValue["idLine"], $dtCharge, $chargeValue["imei"], $latitude, $longitude, $datetime, $chargeValue["counter"], $errdesc);
    }else{		
		$sql = "INSERT INTO sis_cob_trj_err ( fol_dis, cve_uni, monto, moneda, cve_mov, cve_rut, cve_lin, fec_cob, imei, latitud, longitud, fec_cap, dsc_err) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("iidssiissssss", $chargeValue["transactionId"],$chargeValue["idUnit"],$chargeValue["netCharge"],$chargeValue["currency"],$cve_mov,$chargeValue["idRoute"],$chargeValue["idLine"], $dtCharge, $chargeValue["imei"], $latitude, $longitude, $datetime, $errdesc);		
	}
	$rc = $stmt->execute();
    $ret = array();
	$ret2 = array();
    if(false===$rc){
        $ret["status"] = false;
        $ret["message"] = $stmt->error; 
    }else{
        $ret["status"] = true;
        $ret["folio"] = $stmt->insert_id;        
    }
    $stmt->close();
    return $ret;
}


//PRUEBAS INSERCION GPS
function insertTrackGPS($conn, $track){
	$sql = "INSERT INTO sis_gps_tst (dat1,dat2,dat3,dat4,dat5,dat6,dat7,dat8,dat9,dat10) VALUES ('".$track['dat1']."','".$track['dat2']."','".$track['dat3']."','".$track['dat4']."','".$track['dat5']."','".$track['dat6']."','".$track['dat7']."','".$track['dat8']."','".$track['dat9']."','".$track['dat10']."')";
    $stmt = $conn->prepare($sql);	
    //$stmt->bind_param("iiiissss", $track["idUnit"],$track["idLine"],$track["idCompany"],$track["idRoute"],$lat,$lon, $dtTrack,$track["imei"]);
    $rc = $stmt->execute();
	$ret = array();
    if(false===$rc){
        $ret["status"] = false;
        $ret["message"] = $stmt->error; 
    }else{
        $ret["status"] = true;
        $ret["folio"] = $stmt->insert_id;
        $ret["message"] = "Tracking registrado correctamente."; 	
    }
    $stmt->close();
    return $ret;
}

function returnBalance($conn, $imei, $usr){
	$sql="select bal_ter from vw_ter_bal where imei='$imei' and usr_cap='$usr'";	
	$result = $conn->query($sql);	
	while($row = $result->fetch_assoc()){
		$balter = $row["bal_ter"];
	}
	return $balter;
}