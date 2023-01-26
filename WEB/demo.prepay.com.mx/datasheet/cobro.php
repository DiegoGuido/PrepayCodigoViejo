<?php 
session_start();
if(isset($_SESSION["userinfo"])){
date_default_timezone_set("America/Mexico_City");

include "../cnx.php";	
include "../util_list.php";	
$conn = getConnection();
$sql = $_SESSION['userinfo']['sql'];
//echo "sql1: ".$sql;
$fecact = date("Y-m-d");
}
header('Pragma: public');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1 
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Transfer-Encoding: none');
header('Content-type: application/vnd.ms-excel;');
header("Content-Disposition: attachment; filename=Reporte_Recargas_".$fecact.".xls");


$b = array("á", "é", "í", "ó", "ú");
$c = array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;");

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1 " />
</head>
<body>
<table>
<tbody>
<tr bgcolor="#a9a9a9">
<td>FOLIO</td>
<td>UNIDAD</td>
<td>LINEA</td>							
<td>RUTA</td>
<td>MONTO</td>
<td>MON.</td>
<td>TIPO MOV.</td>
<td>CLAVE TARJETA</td>
<td>TIPO TARJETA</td>
<td>SALDO ANT.</td>
<td>FECHA COBRO</td>                            
<td>IMEI</td>
</tr>
<?
$arrDat = getDataFromTable1($sql);
foreach($arrDat as $dat){	
?>
<tr>
<td><? echo $dat["cve_reg"] ?></td>
<td><? echo $dat["num_eco"] ?></td>
<td><? echo $dat["des_lin"] ?></td>
<td><? echo $dat["ruta"] ?></td>
<td>$<? echo $dat["monto"] ?></td>
<td><? echo $dat["moneda"] ?></td>
<td><? echo $dat["des_mov"] ?></td>
<td><? echo $dat["cve_trj"] ?></td>
<td><? echo $dat["des_tip"] ?></td>
<td>$<? echo $dat["saldo"] ?></td>
<td><? echo $dat["fec_cob"] ?></td>							
<td>#<? echo $dat["imei"] ?>#</td>
</tr>
<? } 
$_SESSION['userinfo']['sql'] = "";
?>
</tbody>
</table>
</body>
</html>