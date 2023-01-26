<?php
session_start();
$loc = ".";

if(isset($_POST["lgnusername"]) && isset($_POST["lgnpassword"])){
	
	if(empty($_POST["lgnusername"]) || empty($_POST["lgnpassword"])){
		$_SESSION["loginreply"]["error"]=true;
		$_SESSION["loginreply"]["message"]="Ingresa usuario y contraseña.";
	}else{
		require_once("cnx.php"); 
		$conn = getConnection();
		$login = executeLogin($conn, $_POST["lgnusername"],$_POST["lgnpassword"]);
		if($login["error"]){
			$_SESSION["loginreply"]["error"]=true;
			$_SESSION["loginreply"]["message"]=$login["message"];
			//echo ":(".$_SESSION["loginreply"]["message"];
		}else{
			$_SESSION["userinfo"]["userfullname"] 	= $login["userfullname"];
			$_SESSION["userinfo"]["username"] 		= $login["username"];
			$_SESSION["userinfo"]["userprofile"] 	= $login["userprofile"];
			$_SESSION["userinfo"]["roluser"] 		= $login["roluser"];
			$loc= "main.phtml";
		}
	}
}
header("Location: $loc");
?>
