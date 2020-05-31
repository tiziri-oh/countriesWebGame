<?php 
session_start();
include 'inc/database.php';
require_once 'inc/functions.php';

$errors = "";

if(!isset($_SESSION['identifiant']) || empty($_SESSION['identifiant'])){
	header('location: index.php');
	die();
}
if($_SESSION['identifiant'] != "admin")
{
  header('location: index.php');
  die();
}
if(isset($_POST['jsonSubmit'])){
  if($_FILES["geojson"]["error"] == UPLOAD_ERR_OK){    
    $file    = $_FILES["geojson"]["tmp_name"];
    $contenu = json_decode(file_get_contents($file));
    
    foreach ($contenu as $pays) {
      if(!verifier_pays_existe($pays->name->official)){
        enregistrer_pays(
          $pays->name->official,  $pays->translations->fra->official, $pays->area,  $pays->capital[0],   $pays->latlng[0],  $pays->latlng[1],
          $pays->cca2,  $pays->cca3,  file_get_contents("json/data/".strtolower($pays->cca3).".geo.json"), 
          "json/data/".strtolower($pays->cca3).".svg", 'description'
        );
      }
    }
  }
}
if(isset($_POST['submit'])) {
  if( ($_FILES["fichier1"]["error"] == UPLOAD_ERR_OK) && ($_FILES["fichier2"]["error"] == UPLOAD_ERR_OK)    ){
    $file1 = $_FILES["fichier1"]["tmp_name"];
    $geojson = file_get_contents($file1);

    $localdir = "uploads/";
    $localfile = $localdir . strtolower($_FILES["fichier2"]["name"]);
    move_uploaded_file($_FILES["fichier2"]["tmp_name"], $localfile);

    enregistrer_pays(
      $_POST['nom'],
      $_POST['nomFr'],
      $_POST['capitale'],
      $_POST['latitude'],
      $_POST['longitude'],
      $_POST['iso2'],
      $_POST['iso3'],
      $geojson,
      $localfile,
      $_POST['description']
    );
  }
}
?>
<?php $titre_page = "Compte Administrateur" ?>
<?php include 'inc/header.php'?>
    <div class="container">
        <h1> Administration du Jeu</h1>
    </div>

<div class="well container" style="background-color: white;">

  <div class="row">
    <div class="col-md-6"> 
      <h3> Ajouter une liste de pays</h3>
      <form class="form" action="admin.php" method="POST" enctype="multipart/form-data"> 
        <div class="form-group">
          <label for="geojson"> <span class="glyphicon glyphicon-file"> Depuis un fichier JSON </label>
          <input type="file" name="geojson" class="btn btn-default"></br>        
        </div>
        </br>
        <div class="form-group">
          <input type="submit" name="jsonSubmit" value="Envoyer" class="btn btn-primary">
        </div>
      </form>
    </div>
    <div class="col-md-6">
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">  
      <form class="form" action="admin.php" method="POST" enctype="multipart/form-data">      
        <h3> Ajouter un pays au jeu</h3>
        <div class="col-md-6 form-group">
          <label for="nomFr"> Nom en français : </label>
          <input type="text" class="form-control" name="nomFr">
        </div>

        <div class="col-md-6 form-group">
          <label for="nom"> Nom en anglais : </label>
          <input type="text" class="form-control" name="nom">
        </div>

        <div class="col-md-3 form-group">
          <label for="capitale"> Capitale : </label>
          <input type="text" class="form-control" name="capitale">
        </div>

        <div class="col-md-3 form-group">
          <label for="superficie"> Superficie: </label>
          <input type="text" class="form-control" name="superficie">
        </div>

        <div class="col-md-3 form-group">
          <label for="iso2"> ISO2 : </label>
          <input type="text" class="form-control" name="iso2">
        </div>

        <div class="col-md-3     form-group">
          <label for="iso3"> ISO3 : </label>
          <input type="text" class="form-control" name="iso3">
        </div>

        <div class="col-md-6 form-group">        
          <label for="latitude"> Latitude de la capitale : </label>
          <input type="text" class="form-control" name="latitude">
        </div>

        <div class="col-md-6 form-group">
          <label for="longitude"> Longitude de la capitale : </label>
          <input type="text" class="form-control" name="longitude"/>
        </div>

        <div class="col-md-12 form-group">
          <label for="description">Description</label>
          <textarea id="description" class="md-textarea form-control" name="description" rows="3"></textarea>       
        </div>
      
        <div class="col-md-12 form-group">
          <label for="fichier1"><span class="glyphicon glyphicon-file"> Fichier GeoJson</span></label>
          <input type="file" name="fichier1" class="btn btn-default">
        </div>
      
        <div class="col-md-12 form-group">
          <label for="fichier2"> <span class="glyphicon glyphicon-file"> Drapeau</label>
          <input type="file" name="fichier2" class="btn btn-default">
        </div>

        <input type="submit" name="submit" value="Envoyer" class="btn btn-primary">
      </form>
    </div>
    <div class="row">  
      <div class="col-md-5 admin-tables">
        <h3> Gestion des utilisateur du Jeu </h3>
        <div id="db_users_admin"><?php print_db_users_admin(); ?></div>
      </div>
      <div class="col-md-5   admin-tables">
        <h3> Gestion des invités du Jeu </h3>
        <div id="db_invites_admin"><?php print_db_invites_admin(); ?></div>
      </div>
    </div>
    
    <div class="col-md-12 admin-tables">
      <h3>Liste des pays enregistrés</h3>
      <div id="db_pays" class=""><?php print_db_pays();?></div>     
    </div>

  </div>
</div>

<script>      
                
$('.supp_user').click(function() {
  var test = confirm("confirmez la suppression !");
  if(test){
    $.ajax({
      url: "dbQuest.php",
      type: "POST",
      data: {
        request : "remove_user",
        nom : $(this).attr("id")
      },
      success: function (data) {window.location = window.location.href;},
      error  : function (data) {alert('erreur en supprimant l\'utilisateur');}
    });
  }
});

$('.supp_inv').click(function() {
  var test = confirm("confirmez la suppression!");
  if(test){
    $.ajax({
      url: "dbQuest.php",
      type: "POST",
        data: {
          request : "remove_invite",
          nom : $(this).attr("id")
        },
        success: function (data) {window.location = window.location.href;},
        error  : function (data) {alert('erreur en supprimant l\'invité');}
    });
  }   
});

if($("#tbody_pays tr").length){
  $(".supp_pays").on("click", function(){
    var test = confirm("confirmez la suppression !");
    if(test){
      $.ajax({
        url: "dbQuest.php",
        type: "POST",
        data: {
          request : "remove_pays",
          nom : $(this).attr("id")
        },
        success: function (data) {
          window.location = window.location.href;
        }
      });
    }
  });
}else{
  $('#db_pays').html("<h4> Aucun Pays n'a été enregistrés ! </h4>");              
}

</script>


<?php include 'footer.php' ?>