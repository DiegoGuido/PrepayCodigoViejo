<?php
	session_start();
	include "../cnx.php";
	require "compania.php";	
	class DAOCompania{
		var $con;
		public function __construct(){
			
		}
		public function insert($objetoCompania){
			$conn = getConnection();
			$nomCompania	= $objetoCompania->getNomCompania();
			$moneda			= $objetoCompania->getMoneda();
			$status			= empty($objetoCompania->getStatus())?"A":$objetoCompania->getStatus();
			$urlCompania	= trim($objetoCompania->getUrlCompania());
			
			//$usr = 'admin';
			$usr = $_SESSION["userinfo"]["username"];
			$fec = $currDate = date("Y-m-d H:i:s");
			//echo "usr: ".$usr." fec: ".$fec;
			//echo "nom: ".$nomCompania." mon: ".$moneda." status ".$status;
			//$sql = "INSERT INTO cat_cia (des_cia, moneda, stt_cia, usr_cap, fec_cap) VALUES (?, ?, ?, ?, ?)";			
			$sql = "INSERT INTO cat_cia (des_cia, moneda, stt_cia, url_cia, usr_cap, fec_cap) 
					VALUES ('$nomCompania', '$moneda', '$status', '$urlCompania', '$usr', '$fec')";
			//echo $sql;			
			$stmt = $conn->prepare($sql);
			//$stmt->bind_param("sssss", $nomCompania, $moneda, $status, $usr, $fec);
			$rc = $stmt->execute();
			if(false===$rc){
				$ret = $stmt->error; 
				//echo "error";
			}else{
				$ret = $stmt->insert_id;
				$rel = InsCiaRel($conn,$ret,$usr,$fec);
				$dir = CreateDirectory($urlCompania);
				$tab = insTabAct($conn,$ret,$usr,$fec);
				$ret = $ret." relacion: ".$rel." directorio: ".$dir." tablas: ".$tab;
			}
			$stmt->close();
			$conn->close();
			return $ret;
		}
		public function update($objetoCompania){ 
		
			$conn = getConnection();
			$idCompania		= $objetoCompania->getIdCompania();
			$nomCompania	= $objetoCompania->getNomCompania();			
			$moneda			= $objetoCompania->getMoneda();
			$status			= $objetoCompania->getStatus();	
			$urlCompania	= $objetoCompania->getUrlCompania();
			//echo "id:".$idCompania."nom: ".$nomCompania." mon: ".$moneda." status ".$status;		
			$sql = "update cat_cia set des_cia = ?, moneda = ?, stt_cia = ?, url_cia= ? where cve_cia = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssssi", $nomCompania, $moneda, $status, $urlCompania, $idCompania);
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
		
		public function searchById($idCompania){
			$conn = getConnection();
			$sql = "select * from cat_cia where cve_cia = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i",$idCompania);
			$stmt->execute();
			$result = $stmt->get_result();
			if($row = $result->fetch_assoc()) {
				$compania = new Compania();
				$compania->setIdCompania 	($row["cve_cia"]);
				$compania->setNomCompania 	($row["des_cia"]);				
				$compania->setMoneda		($row["moneda"]);
				$compania->setStatus	    ($row["stt_cia"]);
				$compania->setUrlCompania 	($row["url_cia"]);
			}
			$conn->close();
			return $compania;
		}		
	}

function InsCiaRel($conn,$ret,$usr,$fec){
	$sql = "INSERT INTO cat_cia_rel (cve_cia,cve_cia_rel,stt_rel,usr_cap,fec_cap) 
			VALUES ('$ret','$ret','A','$usr','$fec')";
	//echo "sqlrel: ".$sql."<br>";
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

function insTabAct($conn,$ret,$usr,$fec){
	$sql = "INSERT INTO sis_act_tbl (nom_tbl,des_tbl,cve_cia,fec_act,usr_cap,tipo) 
			select nom_tbl,des_tbl,'$ret','$fec','$usr',tipo from sis_act_tbl where cve_cia=1";
	//echo "sqlrel: ".$sql."<br>";
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


function CreateDirectory($dat){
	$url = trim($dat);
	$aurl = explode("/",$url);
	$dom = $aurl[0];
	if ($dom == "prepay.com.mx"){
		$cpt = $aurl[1];
		$des = $dom."/".$cpt;
		$path = str_replace($_SERVER['HTTP_HOST'],$des,$_SERVER['DOCUMENT_ROOT']);
		$org  = str_replace($_SERVER['HTTP_HOST'],"demo.prepay.com.mx",$_SERVER['DOCUMENT_ROOT']);

		if (is_dir($path)) {
			$ret = "El directorio especificado ya existe";
		} else {			
			if (mkdir($path, 0755)) {
				//echo "creo directorio: ".$path."<br>";
				if (xcopy($org,$path,true)){
					$ret = "Directorio creado correctamente";					
				} else {
					$ret = "Error al copiar archivos";
					
				}
			} else {				
				$ret = "Error al crear el directorio";
			}	
		}
	} else {
		$ret = "El dominio especificado es externo";
	}
return $ret;
}


function xcopy($src,$dst,$rwrite=false) { 
    $dir = opendir($src); 
    if (!file_exists($dst)) mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
		//echo $file."<br>";
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                xcopy($src . '/' . $file,$dst . '/' . $file,$rwrite); 			
            } 
            else { 
                if ($rwrite) copy($src . '/' . $file,$dst . '/' . $file);				
            } 
        } 
    } 
    closedir($dir);
	return true;	
}

?>