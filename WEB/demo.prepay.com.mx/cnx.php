<?   
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function getConnection(){
	$prfix = "envdtsme_";
	$servername = "127.0.0.1";
	$dbname		= $prfix."dbppy_dev";
	$username 	= $prfix."usuario";
	$password	= "contraseña";
	$conn = new mysqli($servername, $username, $password, $dbname);
	//$acentos = $dbname->query("SET NAMES 'utf8'");
	if ($conn->connect_error) {
		return null;
	}else
		return $conn;
}
function desconectar(){
	mysqli_close();
}

function executeLogin($conn, $u, $p, $url){	
	$response = array();
	$p = md5($p);
	//$sql = "select a.usr,a.pwd from cat_usr a where a.usr = '$u' and a.pwd = '$p'";
	$sql = "select a.usr,a.pwd,b.cve_cia,c.des_cia,b.cve_lin,b.des_lin,c.url_cia 
			from cat_usr a, vw_usr_cia b, cat_cia c 
			where a.usr=b.usr and b.cve_cia=c.cve_cia
			and a.usr='$u' and a.pwd='$p' and c.url_cia='$url'";
	//echo $sql;
	//$stmt = $connection->prepare($sql);
	//$stmt->bind_param("ss", $u, $p);
	//$stmt->execute();
	//$result = $stmt->get_result();
	$result = $conn->query($sql);
	if($result->num_rows === 0){
		$response["error"] = true;
		$response["message"] = "Fallo de autenticación";
	}else{
		$row = $result->fetch_assoc();
		$response["error"] = false;
		$response["message"] = "Login succeeded";
		$response["userfullname"] = $row["nombre"];
		$response["username"] = $row["usr"];
		//$response["userprofile"] = $row["nombre"];
		//$response["roluser"] = $row["des_per"];
		$response["usercia"] = $row["cve_cia"];
		$response["userciades"] = $row["des_cia"];
		$response["userlin"] = $row["cve_lin"];
		$response["userlindes"] = $row["des_lin"];
	}
	$conn->close();
	return $response;
}

?>