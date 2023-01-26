<?php
session_start();
if(isset($_POST["param_idusuario"])){
	include "cnx.php";	
	include "util_list.php";
	
	$idusuario = $_POST["param_idusuario"];
	
	switch($_POST["param_key"]){
		case "COMPANY":
			$sqlAva = "select cve_cia as cve_elem, des_cia as des_elem from cat_cia where cve_cia not in (select cve_cia from sis_usr_cia where usr='$idusuario') and stt_cia = 'A'";
			$sqlUsed = "select cve_reg, UC.cve_cia as cve_elem, des_cia as des_elem, stt_usr_cia as stt_elem from sis_usr_cia UC inner join cat_cia C on C.cve_cia = UC.cve_cia where usr='$idusuario'";
			break;
		case "PAGE":
			$sqlAva = "select cve_pag as cve_elem, des_pag as des_elem from cat_pag where cve_pag not in (select cve_pag from sis_per_pag where usr='$idusuario') and stt_pag = 'A'";
			$sqlUsed = "select cve_reg, UP.cve_pag as cve_elem, des_pag as des_elem, UP.stt_pag as stt_elem from sis_per_pag UP inner join cat_pag C on C.cve_pag = UP.cve_pag where usr='$idusuario'";
			break;
		case "APPLICATION":
			$sqlAva = "select cve_per as cve_elem, des_per as des_elem from cat_per where cve_per not in (select cve_per from sis_pri_usr where usr='$idusuario') and stt_per = 'A'";
			$sqlUsed = "select cve_pri as cve_reg, UP.cve_per as cve_elem, des_per as des_elem, stt_pri as stt_elem from sis_pri_usr UP inner join cat_per C on C.cve_per = UP.cve_per where usr='$idusuario'";
			break;
		
	}
	$arrElements = array();
	$arrElements["available"] = getDataFromTable1($sqlAva);
	$arrElements["used"] = getDataFromTable1($sqlUsed);
	//echo base64_encode(json_encode(utf8ize($arrElements))); // Este cuando los caracteres especiales se meten a mano en la DB Ã± => ñ
	echo base64_encode(json_encode($arrElements));
}else{
	if(isset($_POST["param_idelement"])){
		include "cnx.php";
		$cat = explode("-",$_POST["param_idelement"])[0];
		$ide = explode("-",$_POST["param_idelement"])[1];
		switch($cat){
			case "COMPANY":
				$sqlUpd = "update sis_usr_cia set stt_usr_cia = if(stt_usr_cia ='I','A','I') where cve_reg = ?";
				break;
			case "PAGE":
				$sqlUpd = "update sis_per_pag set stt_pag = if(stt_pag ='I','A','I') where cve_reg = ?";
				break;
			case "APPLICATION":
				$sqlUpd = "update sis_pri_usr set stt_pri = if(stt_pri ='I','A','I') where cve_pri = ?";
				break;
		}
		$conn = getConnection();
		$stmt = $conn->prepare($sqlUpd);
		$stmt->bind_param("i", $ide);
		$rc = $stmt->execute();
		if(false===$rc){
			$ret = $stmt->error; 
		}else{
		   $ret = $stmt->affected_rows;
		}
		$stmt->close();
		$conn->close();
		echo $ret;
	}else{
		if(isset($_POST["param_relation"])){
			include "cnx.php";
			$cat = explode("-",$_POST["param_relation"])[0];
			$ide = explode("-",$_POST["param_relation"])[1];
			$idu = explode("-",$_POST["param_relation"])[2];
			switch($cat){
				case "COMPANY":
					$sqlIns = "INSERT INTO sis_usr_cia (usr, cve_cia, stt_usr_cia, usr_cap, fec_cap) VALUES ( ?,?,?,?,?)";
					break;
				case "PAGE":
					$sqlIns = "INSERT INTO sis_per_pag (usr, cve_pag, stt_pag,	   usr_cap, fec_cap) VALUES ( ?,?,?,?,?)";
					break;
				case "APPLICATION":
					$sqlIns = "INSERT INTO sis_pri_usr (usr, cve_per, stt_pri,	cve_lin ,  usr_cap, fec_cap) VALUES ( ?,?,?,1,?,?)";
				break;
			}
			$conn = getConnection();
			$stmt = $conn->prepare($sqlIns);
			$stmt->bind_param("sssss",$idu, $ide, $stt_elem='A',$_SESSION["userinfo"]["username"], $currDate = date("Y-m-d H:i:s"));
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
			}else{
				$ret = $stmt->insert_id;
			}
			$stmt->close();
			$conn->close();
			echo $ret;
		}
	}
}
function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}