<?php session_start(); ?>
<?php
  include 'inc/database.php';
  require_once 'inc/functions.php';
  if(isset($_SESSION['identifiant']) && !empty($_SESSION['identifiant'])){
    header("location: profil.php");
    exit();
  }else{
    if(!empty($_POST['identifiant']) && !empty($_POST['mot_de_passe']))
    {
      $identifiant = ($_POST['identifiant']);
      $mot_de_passe = md5($_POST['mot_de_passe']);
      $user = verifier_connexion($identifiant, $mot_de_passe);
    if($user != null){
        $_SESSION['identifiant'] = $user;
        if($_SESSION['identifiant'] == "admin"){
          header('location: admin.php');
          die();    
        }else{
          header("location: profil.php");
          die();
        }
      }else{
         echo "Identifiant ou mot de passe incorrect </br>";
      }
    }
  }
?>
<?php
$titre_page = "Se connecter";
include 'inc/header.php';
?>
        <div class="container">
            <h1> Connexion au jeu </h1>
            <form action="login.php" method="post" class="well col-md-6">
                <div class="form-group">
                    <label for="identifiant">Identifiant</label>
                    <input type="input" class="form-control" name="identifiant">
                </div>

                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" class="form-control" name="mot_de_passe">
                </div>
                <button type="submit" class="btn btn-primary" name="se_connecter">Se connecter</button>
            </form>
        </div>

<?php include 'inc/footer.php' ?>
