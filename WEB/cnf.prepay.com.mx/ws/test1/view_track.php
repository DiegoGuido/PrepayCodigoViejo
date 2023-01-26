<?
include "../_OT_Globals.php";
$otGlobals = new OTConstants();
$prefix = $otGlobals->getPrefix();
include "../".$prefix.$otGlobals::CONNECTION_FILE;
$conn=getConnection();
echo "Empiezan Datos: </br>";
$sql = "select * from sis_gps_tst order by cve_reg DESC";
$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {		
		echo $row["cve_reg"]." ".$row["dat1"]." ".$row["dat2"]." ".$row["dat3"]." ".$row["dat4"]." ".$row["dat4"]." ".$row["dat6"]." ".$row["dat7"]." ".$row["dat8"]." ".$row["dat9"]." ".$row["dat10"]." ".$row["fec_cap"]."</br>";
		//print_r($row);
		//echo "</br>";
		}
	} else {
		echo "0 results </br>";
	}
$conn->close();
echo "Terminan Datos: </br>";
?>