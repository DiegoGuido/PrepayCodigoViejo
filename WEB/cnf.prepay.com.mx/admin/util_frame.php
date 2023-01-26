<?

function getHeaderFrm($titlepage, $innerJS){

//INICIA HTML

?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title><? echo $titlepage; ?></title>
	<script src="http://cnf.prepay.com.mx/assets/js/jquery.min.js"></script>
    <link rel="stylesheet" href="http://cnf.prepay.com.mx/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://cnf.prepay.com.mx/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=ABeeZee">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="http://cnf.prepay.com.mx/assets/css/styles.min.css">
	<link rel="shortcut icon" type="image/x-icon" href="http://cnf.prepay.com.mx/assets/img/prepay-icon.png" />
	<?php 
	if($innerJS != null){
		echo $innerJS;
	}
	?>
</head>

<body>
<?}

function getFooterFrm(){

?>    
    <script src="http://cnf.prepay.com.mx/assets/js/jquery.min.js"></script>
    <script src="http://cnf.prepay.com.mx/assets/bootstrap/js/bootstrap.min.js"></script>
	<script src="http://cnf.prepay.com.mx/assets/js/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script src="http://cnf.prepay.com.mx/assets/js/script.min.js"></script>
</body>
</html>
<? } ?>



