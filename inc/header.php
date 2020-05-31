<!DOCTYPE html>
<?php require_once 'functions.php'; ?>
<html lang="fr">
	<head>
		<title><?php echo $titre_page . " - "?>Jeu des capitales</title>
		<meta charset="utf-8" />
        <!-- leaflet library -->
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" />
		<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"></script>
        <!-- bootstrap library -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	</head>
	<body>
         <nav class="navbar navbar-inverse" style="background-color: #333333;">
          <div class="container-fluid">
            <div class="navbar-header">
              <a class="navbar-brand" href="index.php"> Jeu des capitales </a>
            </div>
            
            <ul class="nav navbar-nav">
              <li class=<?= est_class_active("Acceuil", $titre_page); ?> ><a href="index.php"> Acceuil </a></li>
              <li class=<?= est_class_active("Jouer", $titre_page); ?> ><a href="jeu.php"> Jeu </a></li>
            </ul>
            
            <ul class="nav navbar-nav navbar-right">
						  <?php if(isset($_SESSION['identifiant']) && !empty($_SESSION['identifiant'])) {?>
									<li class=<?= est_class_active("Se deconnecter", $titre_page); ?> ><a href="logout.php"><span class="glyphicon glyphicon-user"></span> Se deconnecter</a></li>
							<?php } else {?>
                <li class=<?= est_class_active("Creer un compte", $titre_page); ?> ><a href="creercompte.php"><span class="glyphicon glyphicon-user"></span> Creer un compte</a></li>
                <li class=<?= est_class_active("Se connecter", $titre_page); ?> ><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Se connecter</a></li>
							<?php }?>
            </ul> 

          </div>
        </nav>
        <script>
          $("body").css("background-image","url(\"img/bg-map.jpg\")");
          $("body").css("background-size","100%",);
        </script>
