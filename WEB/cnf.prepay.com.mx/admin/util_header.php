<?

function getHeader($titlepage, $innerJS){

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

    <nav class="navbar navbar-light navbar-expand-md navigation-clean">
        <div class="container"><a href="main.phtml"><img src="http://cnf.prepay.com.mx/assets/img/prepay-horizontal.png" width="180" height="40"></a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav ml-auto">                    
                    <li class="dropdown nav-item"><a class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false" href="#">🔑 Administración</a>
                        <div class="dropdown-menu" role="menu">						
						<a class="dropdown-item" role="presentation" href="mng_usuarios.phtml">Usuarios</a>
						<a class="dropdown-item" role="presentation" href="mng_compania.phtml">Compañias</a>						
						<a class="dropdown-item" role="presentation" href="mng_dispositivos.phtml">Dispositivos</a>												
						</div>
                    </li>
					<li class="nav-item" role="presentation"><a class="nav-link" href="logout.php">❌ Cerrar Sesion</a></li>
                </ul>
            </div>
        </div>
    </nav>
	<h3 align="center"><? echo $titlepage; ?></h3>
<?}

function getFooter(){

?>

<div class="container"></div>
    <div class="footer-2" style="background-color: rgb(24,165,88);">
        <div class="container">
            <div class="row">
                <div class="col-8 col-sm-6 col-md-6">
                    <p class="text-left" style="margin-top:5%;margin-bottom:3%;">© PrePay 2020</p>
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <p class="text-right" style="margin-top:5%;margin-bottom:8%%;font-size:1em;">Reportar Incidencia.</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="http://cnf.prepay.com.mx/assets/js/jquery.min.js"></script>
    <script src="http://cnf.prepay.com.mx/assets/bootstrap/js/bootstrap.min.js"></script>
	<script src="http://cnf.prepay.com.mx/assets/js/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script src="http://cnf.prepay.com.mx/assets/js/script.min.js"></script>
</body>
</html>

<? 
//TERMINA HTML
}
?>



