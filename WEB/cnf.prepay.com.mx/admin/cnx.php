<?   
function getConnection(){
	$prfix = "envdtsme_";
	$servername = "127.0.0.1";
	$dbname		= $prfix."";
	$username 	= $prfix."";
	$password	= "";
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

function executeLogin($connection, $u, $p){
	$response = array();
	$p = md5($p);
	$sql = "select a.usr,a.pwd from cat_usr a where a.usr = '$u' and a.pwd = '$p'";
	//echo $sql;
	$stmt = $connection->prepare($sql);
	//$stmt->bind_param("ss", $u, $p);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows === 0){
		$response["error"] = true;
		$response["message"] = "Fallo de autenticación";
	}else{
		$row = $result->fetch_assoc();
		$response["error"] = false;
		$response["message"] = "Login succeeded";
		$response["userfullname"] = $row["nombre"];
		$response["username"] = $row["usr"];
		$response["userprofile"] = $row["nombre"];
		$response["roluser"] = $row["des_per"];
	}
	$stmt->close();
	return $response;
}
?>