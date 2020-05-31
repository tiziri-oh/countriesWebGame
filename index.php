<?php session_start(); 

if(isset($_SESSION['identifiant']) || !empty($_SESSION['identifiant'])){
  if($_SESSION['identifiant'] == "admin")
  {
    header('location: admin.php');
    die();
  }
  header('location: profil.php');
  die();
}

?>
<?php
$titre_page = "Acceuil";
include 'inc/header.php';
?>
<div class="container">
  <h2 class="jumbotron text-center" style="background-color: white;">
  Bienvenue
  </h2>
  <h3 class="jumbotron text-center" style="background-color: white;">
  Vous êtes incollable en géographie, venez tenez l'aventure avec notre super jeu des capitales. </br> </br> 
  Et vous deviendrez peut être notre super champion. </br></br>
  Cliquer sur le boutton suivant pour lancer le jeu en mode invité.</br></br> 
  <a class="btn btn-success btn-lg" href=jeu.php>Commencer le jeu</a>
  </br></br> 
  N'oubliez pas de creer un compte si vous voulez sauvegarder votre score et le comparer avec celui des autres joueurs.
  </h3>
</div>
<?php include 'inc/footer.php' ?>