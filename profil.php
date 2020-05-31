<?php 
session_start(); 
if(!isset($_SESSION['identifiant']) || empty($_SESSION['identifiant'])){
	header('location: index.php');
	die();
}
$pseudo = $_SESSION['identifiant'];
include 'inc/database.php';
require_once 'inc/functions.php';
?>
<?php $titre_page = "Page de profile" ?>
<?php 
include 'inc/header.php';
?>

<div class="well container" style="background-color: white;">
  <div class="col-md-6">
  	<h3 class="jumbotron text-center">
  		Bienvenu <?php echo $pseudo?>
  	</h3>
  	<div class="text-center">
  		<a class="btn btn-success btn-lg" href=jeu.php>Démmarer une partie</a>
  	</div>
  </div>
  <div class="col-md-6">
  	  <div class="text-center">
  		<h3>Scores du Jeu</h3>
      <div id="db_users">
        <?php print_db_users($pseudo); ?>
      </div>
  	</div>
  </div>
</div>

<?php include 'footer.php' ?>