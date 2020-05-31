<?php session_start(); ?>
<?php
include 'inc/database.php';
require_once 'inc/functions.php';
$is_invite = false;

if(isset($_SESSION['identifiant']) && !empty($_SESSION['identifiant']))
{
    $is_invite = false;
    $nb1 = QUEST_USR_MAX;
    $nb2 = QUEST_USR_MAX;
}
else
{
  $is_invite = true;

  $ip_invite = get_ip_addr_invite();

  if(verifier_invite($ip_invite))
  {
    
    $nb1 = QUEST_INV_MAX - get_nb_qst1_invite($ip_invite);
    $nb2 = QUEST_INV_MAX - get_nb_qst2_invite($ip_invite);

  }else{
    ajouter_invite($ip_invite);
    $nb1 = QUEST_INV_MAX;
    $nb2 = QUEST_INV_MAX;
  }
}

$_SESSION['nb1'] = $nb1;
$_SESSION['nb2'] = $nb2;

$titre_page = "Jouer";

include 'inc/header.php';

?>
<div class="container-fluid">
    <div class="row">
      <div class="col-sm-12 col-md-6 well" style="opacity: 0.9;">
            
            <h2>Jeu des capitales</h2>            

            <h3>Joueur : <?= $is_invite ? "Invité" : $_SESSION['identifiant'] ?></h3>
            <h4> Nombre de questions restantes sur les Pays      : <span id="nb1"> <?php echo $nb1;?> </span> </h4>
            <h4> Nombre de questions restantes sur les Capitales : <span id="nb2"> <?php echo $nb2;?> </span></h4>            

            <h3>Question : </h3>
            <h4 id="laQuestion">Appuyer sur le boutton blue pour demander une question.</h4>
            <h4 id="nomPaysCapital"></h4>
            </br>
            <h3> Score1 : <span id="myscore1"></span> </h3>
            <h3> Score2 : <span id="myscore2"></span> </h3>          

            <div class="form-group ">
              <label>Choisir le type de la prochaine question : </label>
              <select type="input" class="form-control" id="QuestionType">
                <option>Pays</option>
                <option>Capitale</option>
              </select>
            </div>
            
            <div class="form-group">
              <button type="button" class="btn btn-primary" id="getQuest"> Demander une nouvelle question </button>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Afficher une description</button>
            </div>
                        
      </div>
      <div class="col-sm-12 col-md-6">        
        <div id="maDiv"></div>
      </div>
    </div>
  </div>
    <div class="container row">            
      <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Wiki</h4>
            </div>
          <div class="modal-body">
            <span><b>Informations utiles :</b></span></br>
            <span id="descriptionReponse">Aucune information pour le moment.</span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>                  
      </div>
    </div>            
</div>


<script src='https://npmcdn.com/@turf/turf/turf.min.js'></script>
<script>
  //changement automatique de la taille de la map
  $('#maDiv').css("height", $(window).height());
  $(window).on("resize", resize);
  resize();
  function resize(){
    $('#maDiv').css("height", $(window).height()/1.5);
  }

  var questRestantes = [<?=$nb1;?> , <?=$nb2;?>];  
  var hasAnswered = false;

  function updateBtnGetQuest(){ 
    $("#nb1").text(questRestantes[0]);
    $("#nb2").text(questRestantes[1]);
  }  

  updateBtnGetQuest();  
  
  //changement automatique de la taille de la map
  $('#getQuest').click(function(){
    start = false;
    if($("#QuestionType").val() == "Capitale"){
      if(questRestantes[1] == 0)
        return
    }else{
      if(questRestantes[0] == 0)
        return
    }

    $.ajax({
        type: "POST",
        url: "dbQuest.php",
        dataType: "json",
        data: {
          request     : "question",
          questionType: $("#QuestionType").val()
        },
        success: function(data){
          map.eachLayer(function (layer) {
          if(coucheStamenWatercolor != layer)
            map.removeLayer(layer);
          });
          if($("#QuestionType").val() == 'Capitale')
          {
            $("#laQuestion").html("Trouvez sur la carte la capitale du pays suivant : ");
            $("#nomPaysCapital").html(data);
            incQuestCapitale();
          }else{
            $("#laQuestion").html("Trouvez sur la carte le pays de la capitale suivante : ");
            $("#nomPaysCapital").html(data);
            incQuestPays();
          }
        },
        error: function () {
          alert('Error');
        }
      }
    );
  });

  function incQuestPays()
  {
      $.ajax({
        url: "dbQuest.php",
        type: "POST",
        dataType: "json",
        data: {
          request : "incQuestPays",
        },                    
        success: function (data) {
          hasAnswered = false;
          questRestantes[0] = data;          
          updateBtnGetQuest();
        },
        error: function () {
          alert('incQuestPays error !');
        }
      });
  }
  function incQuestCapitale()
  {
      $.ajax({
        url: "dbQuest.php",
        type: "POST",
        dataType: "json",
        data: {
          request : "incQuestCapitale",
        },                    
        success: function (data) {
          hasAnswered = false;
          questRestantes[1] = data;          
          updateBtnGetQuest();
        },
        error: function () {
          alert('incQuestCapitale error !');
        }
      });
  }

</script>

<script>
  var start = true;
  // bornes pour empecher la carte StamenWatercolor de "dériver" trop loin...
  var northWest = L.latLng(90, -180);
  var southEast = L.latLng(-90, 180);
  var bornes = L.latLngBounds(northWest, southEast);
  // Initialisation de la couche StamenWatercolor
  var coucheStamenWatercolor = L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.{ext}', {
    attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    subdomains: 'abcd',
    ext: 'jpg'
  });
  // Initialisation de la carte et association avec la div
  var map = new L.Map('maDiv', {
    center: [48.858376, 2.294442],
    minZoom: 2,
    maxZoom: 18,
    zoom: 5,
    maxBounds: bornes
  });
  // Affichage de la carte
  map.addLayer(coucheStamenWatercolor);
  // Juste pour changer la forme du curseur par défaut de la souris
  document.getElementById('maDiv').style.cursor = 'crosshair'
  // Initilisation d'un popup
  var popup = L.popup();
  
  var score1 = 0;
  var score2 = 0;

  $("#myscore1").text(score1);
  $("#myscore2").text(score2);
  
  // Fonction qui réagit au clic sur la carte (e contiendra les données liées au clic)
  function onMapClick(e) {
    var popUpInfos = "";
    $.ajax({
      type: "POST",
      url: "dbQuest.php",
      dataType: "json",
      data: {
        request      : "result",
        mapLocation  : $("#nomPaysCapital").text(),
        questionType : $("#QuestionType").val(),
      },
      success: function(result){
        if(start){
          alert("Il faut demander une question d'abort !");
          return;
        }
        if(hasAnswered == true){
          if($("#QuestionType").val() == "Capitale"){
            if(questRestantes[1]){
              alert('Vous avez déjà répondu à cette quesiton !');
            }else{
              alert('Vous devez créer un compte pour pouvoir repondre à plus de question dans cette catégorie!');
            }
          }else{
            if(questRestantes[0]){
              alert('Vous avez déjà répondu à cette quesiton !');
            }else{
              alert('Vous devez créer un compte pour pouvoir repondre à plus de question dans cette catégorie!');
            }
          }                
          return;
        }
        
        map.eachLayer(function (layer) {
          if(coucheStamenWatercolor != layer)
            map.removeLayer(layer);
        });

        var cap  = result[0];
        var pays = result[1];
        var desc = result[2];

        var distance;
        var estCorrect = false;
        var reference = 200;

        if($("#QuestionType").val() == 'Capitale'){              
          var c_lat = cap[1];
          var c_lon = cap[0];
          var circleCap = L.circle([c_lat, c_lon], 500000);
          circleCap.addTo(map);
          //creer un objet feature <point> du barycentre
          //creer un objet feature <point> de l'evenement (click sur la map)
          var from = turf.point([c_lat, c_lon]);
          var to   = turf.point([e.latlng.lat, e.latlng.lng]);
          var options = {units: 'kilometers'};
          distance = turf.distance(from, to, options);                
          if(distance > reference){
            popUpInfos += "<div style=\"color: red;\">" + 
                            "Mauvaise réponse !</br>Distance: " + distance + "km </br>" + 
                          " </div>";
          }else{
            estCorrect = true;
            popUpInfos += "<div style=\"color: green;\">" + 
                            "Bonne réponse !   </br>Distance: " + distance + "km </br>" + 
                          " </div>";
          }                
        }else{
          //ajouter polygon GeoJSON à la map
          L.geoJson(pays).addTo(map);
          //Calcule du centre de masse du pays : center format geojson
          var center = turf.centerOfMass(pays);
          var c_lon = center["geometry"]["coordinates"][0];
          var c_lat = center["geometry"]["coordinates"][1];
                
          // featureCollection <point>
          var points = turf.points([[e.latlng.lng, e.latlng.lat]]);
          var pointsValides = turf.pointsWithinPolygon(points,pays);
                
          //creer un objet feature <point> du barycentre
          //creer un objet feature <point> de l'evenement (click sur la map)
          var from = turf.point([c_lat, c_lon]);
          var to   = turf.point([e.latlng.lat, e.latlng.lng]);
          var options = {units: 'kilometers'};
          distance = turf.distance(from, to, options);

          if(pointsValides["features"].length == 0){
            popUpInfos += "<div style=\"color: red;\">" + 
                            "Mauvaise réponse !</br> " + 
                            "Le point est en dehors des frontières du pays ! </br>" +
                            "Precision (Distance) : " + distance + "km </br> "+
                          "</div>";
          }else{
            estCorrect = true;
            popUpInfos += "<div style=\"color: green;\">" + 
                            "Bonne réponse !</br> " + 
                            "Le point est dans les frontières du pays! </br>" +
                            "Precision (Distance) : " + distance + "km </br> "+
                          "</div>";
          } 
        }

        var pointList = [[cap[1],cap[0]],[e.latlng.lat, e.latlng.lng]];
        map.fitBounds(pointList);        
        popup.setLatLng(e.latlng).setContent(popUpInfos).openOn(map);
        var score;
        if($("#QuestionType").val() == 'Capitale'){
          L.polyline(pointList, {color: 'blue'}).addTo(map);
          score2 += (estCorrect ? (reference - distance) :  0);
          score2 = Math.round(score2);
          $("#myscore2").text(score2);          
          score  = score2;
        }else{
          L.polyline(pointList, {color: 'blue'}).addTo(map);  
          score1 += (estCorrect ? reference : 0);          
          score1 = Math.round(score1);
          $("#myscore1").text(score1);
          score  = score1;
        }                
        
        $("#descriptionReponse").html(desc);
              
        hasAnswered = true;

        if( (questRestantes[0] == 0 && $("#QuestionType").val() == "Pays") || 
            (questRestantes[1] == 0  && $("#QuestionType").val() == "Capitale")
          ) {
          $.ajax({
            type: "POST",
            url: "dbQuest.php",
            dataType: "json",
            data: {
              request      : "score",
              points       : score,
              questionType : $("#QuestionType").val()
            },
            success: function(score){
              
            },
            error: function () {
              alert('error when sending score !');
            }
          });
        }         
      },
      error: function () {              
        if($("#QuestionType").val() == "Capitale"){
          if(questRestantes[1]){
            alert('Veuillez cliquez d\'abort sur le bouton bleu pour demander une question !');
          }else{
            alert('Vous devez créer un compte pour pouvoir repondre à plus de question dans cette catégorie!');
          }
        }else{
          if(questRestantes[0]){
            alert('Veuillez cliquez d\'abort sur le bouton bleu pour demander une question !');
          }else{
            alert('Vous devez créer un compte pour pouvoir repondre à plus de question dans cette catégorie!');
          }
        }
      }
    });
  }
  // Association Evenement/Fonction handler
  map.on('click', onMapClick);
</script>


<?php include 'inc/footer.php' ?>

