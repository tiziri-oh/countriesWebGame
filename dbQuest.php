<?php session_start(); ?>
<?php
include 'inc/database.php';
require_once 'inc/functions.php';

switch ($_POST['request']) {  

  case  "remove_pays" :
    supprimer_pays($_POST['nom']);
    echo "Suppression de l'invité " . $_POST['nom'] . " OK !";
    break;

  case "remove_user" :
    supprimer_user($_POST['nom']);
    echo "Suppression de l'utilisateur " . $_POST['nom'] . " OK !"; 
    break;

  case "remove_invite":
    supprimer_invite($_POST['nom']);
    break;

  case "incQuestPays" :
    if(isset($_SESSION['identifiant']) && !empty($_SESSION['identifiant'])){
      $_SESSION['nb1']-=1;
      echo $_SESSION['nb1'];
    }else{
      $ip_invite = get_ip_addr_invite();
      $nb1 = get_nb_qst1_invite($ip_invite);
      $nb1++;
      if($nb1 >= QUEST_INV_MAX){
        set_nb_qst1_invite($ip_invite, QUEST_INV_MAX);
      }
      else{
        set_nb_qst1_invite($ip_invite, $nb1);
      }
      echo json_encode(QUEST_INV_MAX - $nb1);
    }
    break;

  case "incQuestCapitale" :
    if(isset($_SESSION['identifiant']) && !empty($_SESSION['identifiant'])){
      $_SESSION['nb2']-=1;
      echo $_SESSION['nb2'];
    }else{
      $ip_invite = get_ip_addr_invite();
      $nb2 = get_nb_qst2_invite($ip_invite);
      $nb2++;
      if($nb2 >= QUEST_INV_MAX){  
        set_nb_qst2_invite($ip_invite, QUEST_INV_MAX);
      }
      else{
        set_nb_qst2_invite($ip_invite, $nb2);
      }
      echo json_encode(QUEST_INV_MAX - $nb2);
    }
    break;

  case "result" :
    $pays_ou_capital = $_POST["mapLocation"];
    $qType = $_POST["questionType"];
    $liste_pays = get_liste_pays();
    $liste_size = sizeof($liste_pays,0);
    foreach ($liste_pays as $pays) {
      if( !strcasecmp($pays->nom,$pays_ou_capital) || !strcasecmp($pays->capitale,$pays_ou_capital) ){
        $description  =  "<img class=\"border\" src=\"". $pays->imgPath ."\" alt=\"\" height=\"50px\" width=\"100px\"/></br>";
        $description .=  "Pays:        " . $pays->nomFr       . "</br>";
        $description .=  "Capitale:    " . $pays->capitale    . "</br>";
        $description .=  "Superficie:  " . (float) $pays->superficie  . "km²</br>";
        $description .=  "description: " . (empty($pays->description) ? "Aucune information enregistrée" : $pays->description) . "</br>";
        echo "[[" . $pays->lng . "," . $pays->lat . "]," . $pays->geojson  . "," . json_encode($description) .  "]";
        break;
      }
    }
    break;
  
  case "question" :
    $pays_ou_capital = $_POST["mapLocation"];
    $qType = $_POST["questionType"];
    $liste_pays = get_liste_pays();
    $liste_size = count($liste_pays);

    if($qType == "Capitale"){
        $index     = rand(0,$liste_size);
        $ip_invite = get_ip_addr_invite();
        $nb2       = get_nb_qst2_invite($ip_invite);      
        echo json_encode($liste_pays[$index]->nom);
    }else{
        $index     = rand(0,$liste_size);
        $ip_invite = get_ip_addr_invite();
        $nb1       = get_nb_qst1_invite($ip_invite);
        echo json_encode($liste_pays[$index]->capitale);
    }
    break;

  case "score" :
    if(isset($_SESSION['identifiant']) && !empty($_SESSION['identifiant']))
    {
      if ($_POST["questionType"] == "Capitale")
        set_user_score($_SESSION['identifiant'], $_POST["points"],2);
      else
        set_user_score($_SESSION['identifiant'], $_POST["points"],1);
    }
    echo json_encode(true);
    break;
  
  default:
    break;
}
?>