<?php session_start(); ?>
<?php
$titre_page = "Creer un compte";
include 'inc/database.php';
require_once 'inc/functions.php';
if(!empty($_POST['pseudo'])
   && !empty($_POST['email'])
   && !empty($_POST['mot_de_passe'])
   && !empty($_POST['verifier_mot_de_passe']))
{
    $pseudo = $_POST['pseudo'];
    $email = $_POST['email'];
    $mot_de_passe = md5($_POST['mot_de_passe']);

    if(verifier_compte_existe($pseudo, $email)){
      echo "pseudo ou mail déja existant </br>";
    }else{
      enregistrer_compte($pseudo, $email, $mot_de_passe);
      echo "compte enregistré </br>";
    }
}
?>

<?php include 'inc/header.php'?>
        <div class="container">
            <h1>Devenez un membre du jeu </h1>
            <form action="creercompte.php" method="post" class="well col-md-6" autocomplete="off">
                <div class="form-group">
                    <label for="pseudo">Pseudo</label>
                    <input type="input" value="" class="form-control" name="pseudo">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" value="" class="form-control" name="email">
                </div>

                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" value="" class="form-control" name="mot_de_passe">
                </div>

                <div class="form-group">
                    <label for="verifier_mot_de_passe">Verifier mot de passe</label>
                    <input type="password" value="" class="form-control" name="verifier_mot_de_passe">
                </div>
                <button type="submit" class="btn btn-primary" name="creer un comte">Creer un compte</button>
            </form>
        </div>

<?php include 'inc/footer.php' ?>
