<?php
session_start();
$loc = ".";
$url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$url=explode("/login.php",$url);
//echo "url: ".$url[0];

if(isset($_POST["lgnusername"]) && isset($_POST["lgnpassword"])){
	
	if(empty($_POST["lgnusername"]) || empty($_POST["lgnpassword"])){
		$_SESSION["loginreply"]["error"]=true;
		$_SESSION["loginreply"]["message"]="Ingresa usuario y contraseña.";
	}else{
		require_once("cnx.php"); 
		$conn = getConnection();
		$login = executeLogin($conn, $_POST["lgnusername"],$_POST["lgnpassword"],$url[0]);
		if($login["error"]){
			$_SESSION["loginreply"]["error"]=true;
			$_SESSION["loginreply"]["message"]=$login["message"];
			//echo ":(".$_SESSION["loginreply"]["message"];
		}else{
			$_SESSION["userinfo"]["userfullname"] 	= $login["userfullname"];
			$_SESSION["userinfo"]["username"] 		= $login["username"];
			//$_SESSION["userinfo"]["userprofile"] 	= $login["userprofile"];
			//$_SESSION["userinfo"]["roluser"] 		= $login["roluser"];
			$_SESSION["userinfo"]["usercia"] 		= $login["usercia"];		
			$_SESSION["userinfo"]["userlin"] 		= $login["userlin"];
			$_SESSION["userinfo"]["userciades"] 	= $login["userciades"];
			$_SESSION["userinfo"]["userlindes"] 	= $login["userlindes"];
			$loc= "main.phtml";
		}
	}
}
header("Location: $loc");
?>
