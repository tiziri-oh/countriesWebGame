<?php

define('QUEST_INV_MAX', 7);
define('QUEST_USR_MAX', 7);

////// GESTION DES UTILISATEURS  //////////
function est_class_active($title, $value)
{
    if(!strcmp($title, $value)){
      return "\"active\"";
    }else{
      return "\"\"";
    }
}

function enregistrer_compte($pseudo, $email, $mot_de_passe)
{
  global $db;
  $q=$db->prepare('INSERT INTO users (pseudo, email, password) VALUES (?,?,?)');
  $q->execute(array($pseudo, $email, $mot_de_passe));
  $q->closeCursor();
}

function verifier_compte_existe($pseudo, $email)
{
  global $db;
  $q = $db->prepare('SELECT pseudo, email FROM users WHERE (pseudo = ? OR email = ?)');
  $q->execute(array($pseudo, $email));
  //$resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  $size = $q->rowCount();
  $q->closeCursor();
  return ($size > 0);
}

function get_id_user($pseudo, $email)
{
  global $db;
  $ret_id = -1;
  $q = $db->prepare('SELECT id, pseudo, email FROM users WHERE (pseudo = ? OR email = ?)');
  $q->execute(array($pseudo, $email));
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  foreach( $resultat as $user ) {
   if(!strcmp($user->pseudo,$id) || !strcasecmp($user->email,$id)){
     $ret_id = $user->id;
     break;
   }
  }
  return $ret_id;
}

function verifier_connexion($id, $mdp)
{
  global $db;
  $username = null;
  $ret = false;
  $q = $db->prepare('SELECT pseudo, email, password FROM users WHERE (pseudo = ? OR email = ?)');
  $q->execute(array($id, $id));
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  foreach( $resultat as $user ) {
   if( (!strcasecmp($user->pseudo,$id) || !strcasecmp($user->email,$id)) && !strcmp($user->password,$mdp) ){
     $ret = true;
     $username = $user->pseudo;
     break;
   }
  }
  $q->closeCursor();
  if(ret){
    return $username;
  }
  else{
    return null;
  }
}

function set_user_score($pseudo, $score, $qtype)
{
  global $db;
  $ret = false;
  $q=$db->prepare('SELECT id, pseudo FROM users WHERE pseudo = ?');
  $q->execute(array($pseudo));
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);  
  $q->closeCursor();

  if($qtype == 1){
    $q=$db->prepare('INSERT INTO historique1 (id_user, score) VALUES (?,?)');
    $q->execute(array($resultat[0]->id,$score));
    $q->closeCursor();
  }
  else{
    $q=$db->prepare('INSERT INTO historique2 (id_user, score) VALUES (?,?)');
    $q->execute(array($resultat[0]->id,$score));
    $q->closeCursor();
  }

}


/*function set_user_score($pseudo, $score, $qtype)
{
  global $db;
  $ret = false;
  if($qtype == 1)
    $q = $db->prepare('UPDATE users SET score1 = ? WHERE pseudo = ?');
  else
    $q = $db->prepare('UPDATE users SET score2 = ? WHERE pseudo = ?');
  $q->execute(array($score,$pseudo));
  $q->closeCursor();
}*/

function print_db_users($pseudo)
{
  global $db;
  $q = $db->prepare('SELECT * FROM users WHERE pseudo = ?');
  $q->execute(array($pseudo));
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  $id_user = $resultat[0]->id;
  $q->closeCursor();

  $index = 0;
  echo "<table class=\"table table-bordered table-hover\">";
  echo "<thead>";
  echo "<tr>";
  echo "<th scope=\"col\">index</th>";
  echo "<th scope=\"col\">Score</th>";
  echo "<th scope=\"col\">Question type</th>";
  echo "</tr>";
  echo "</thead>";
  echo "<tbody id=\"tbody_pays\">";
  
  $q = $db->prepare('SELECT score FROM historique1 WHERE id_user = ? ORDER BY score DESC');
  $q->execute(array($id_user));  
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  $q->closeCursor();
  foreach( $resultat as $hist ) {
      $index++;
      echo "<tr>";
      echo "<th scope=\"row\">" . $index . "</th>";
      echo "<th>" . $hist->score . "</th>";
      echo "<td>" . "Question Pays" ."</td>";
      echo "</tr>";
    if($index == 10)
      break;
  }

  $q = $db->prepare('SELECT score FROM historique2 WHERE id_user = ? ORDER BY score DESC');
  $q->execute(array($id_user));  
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  $q->closeCursor();
  foreach( $resultat as $hist ) {
      $index++;
      echo "<tr>";
      echo "<th scope=\"row\">" . $index . "</th>";
      echo "<th>" . $hist->score . "</th>";
      echo "<td>" . "Question Pays" ."</td>";
      echo "</tr>";
    if($index == 10)
      break;
  }

  echo "</tbody>";
  echo "</table>";
}

////// GESTION DES INVITES //////////
function get_ip_addr_invite(){
  return (getenv('HTTP_CLIENT_IP')?:
          getenv('HTTP_X_FORWARDED_FOR')?:
          getenv('HTTP_X_FORWARDED')?:
          getenv('HTTP_FORWARDED_FOR')?:
          getenv('HTTP_FORWARDED')?:
          getenv('REMOTE_ADDR'));
}

function get_nb_qst1_invite($ip)
{
  global $db;
  $ret = false;
  $q = $db->prepare('SELECT ip_addr, nb_qst1 FROM invites WHERE (ip_addr = ?)');
  $q->execute(array($ip));
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  $nb = $resultat[0]->nb_qst1;
  $q->closeCursor();
  return ($nb);
}

function get_nb_qst2_invite($ip)
{
  global $db;
  $ret = false;
  $q = $db->prepare('SELECT ip_addr, nb_qst2 FROM invites WHERE (ip_addr = ?)');
  $q->execute(array($ip));
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  $nb = $resultat[0]->nb_qst2;
  $q->closeCursor();
  return ($nb);
}

function set_nb_qst1_invite($ip, $nb)
{
  global $db;
  $ret = false;
  $q = $db->prepare('UPDATE invites SET nb_qst1 = ? WHERE ip_addr = ?');
  $q->execute(array($nb,$ip));
  $q->closeCursor();

}

function set_nb_qst2_invite($ip, $nb)
{
  global $db;
  $ret = false;
  $q = $db->prepare('UPDATE invites SET nb_qst2 = ? WHERE ip_addr = ?');
  $q->execute(array($nb,$ip));
  $q->closeCursor();
}

function verifier_invite($ip)
{
  global $db;
  $q = $db->prepare('SELECT ip_addr FROM invites WHERE (ip_addr = ?)');
  $q->execute(array($ip));
  $size = $q->rowCount();
  $q->closeCursor();
  return ($size > 0);
}

function ajouter_invite($ip)
{
  global $db;
  $q=$db->prepare('INSERT INTO invites (ip_addr, nb_qst1, nb_qst2) VALUES (?,0,0)');
  $q->execute(array($ip));
  $q->closeCursor();
}

/////////////////

function gat_capital_pays($nomPays)
{
  $json = file_get_contents('./json/capitals.geojson');
  $json_data = json_decode($json,true);
  foreach ($json_data["features"] as $contryPtes) {
    if($contryPtes["properties"]["country"] == $nomPays)
      return $contryPtes["properties"]["city"];
  }
  return "";
}

function get_infos_pays_capital($nomPaysOuCapital)
{
  $json = file_get_contents('./json/capitals.geojson');
  $json_data = json_decode($json,true);
  foreach ($json_data["features"] as $contryPtes) {
    if( (strcasecmp ($contryPtes["properties"]["country"], $nomPaysOuCapital) == 0) ||
        (strcasecmp ($contryPtes["properties"]["city"],$nomPaysOuCapital) == 0)
      ){
        $infos["pays"]    = $contryPtes["properties"]["country"];
        $infos["capitale"] = $contryPtes["properties"]["city"];
        $infos["iso3"]    = $contryPtes["properties"]["iso3"];
        $infos["iso2"]    = $contryPtes["properties"]["iso2"];
        $infos["coordonnees"]    = $contryPtes["geometry"]["coordinates"];
        return $infos;
    }
  }
  return NULL;
}

function get_geojson_iso3($iso3)
{
  $filename = './json/data/'. $iso3 .'.geo.json';
  $json = file_get_contents($filename);
  return $json;
}

function flipGeoJSONPoint ($data){
  return array($data[1], $data[0]);
}

function flipGeoJSONPoints ($multiPoly){
  $newMPoly = [];
  $mPolySize = sizeof($multiPoly);
  for($i = 0; $i < $mPolySize; $i++){
    $poly = $multiPoly[$i];
    $polySize = sizeof($poly);
    for($j = 0; $j < $polySize; $j++){
      $newfront[$i][$j] = flipGeoJSONPoint($poly[$j]);
    }
  }
  return $newfront;
}

////////////////////////FONCTION ADMINSTRATEUR ///////////////////////////
function enregistrer_pays($nom, $nomFr, $superficie, $capt, $lng, $lat, $iso2, $iso3, $geojson, $imgPath, $desc)
{
  global $db;  
  $q=$db->prepare('INSERT INTO pays (nom, nomFr, superficie, capitale, geojson, imgPath, lat, lng, iso2, iso3, description) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
  $q->execute(array($nom, $nomFr, $superficie, $capt, $geojson, $imgPath, $lng, $lat, $iso2, $iso3, $desc));
  $q->closeCursor();

}

function verifier_pays_existe($nom)
{
  global $db;
  $q = $db->prepare('SELECT nom FROM pays WHERE (nom = ?)');
  $q->execute(array($nom));
  $size = $q->rowCount();
  $q->closeCursor();
  return ($size > 0);
}

function get_select_options_pays(){
  global $db;  
  $q=$db->query('SELECT * FROM pays');
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  foreach ($resultat as $pays) {
    echo "<option>". $pays ."</option>";
  }  
  $q->closeCursor();
  return $resultat;
}

function get_liste_pays()
{
  global $db;  
  $q=$db->query('SELECT * FROM pays');
  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  $q->closeCursor();
  return $resultat;
}

function get_rand_capitales()
{

  global $db;  
  $q=$db->prepare('INSERT INTO pays (nom, capitale, geojson, lat, lng, iso2, iso3) VALUES (?,?,?,?,?,?,?)');
  $q->execute(array($nom, $capt, $geojson, $lng, $lat, $iso2, $iso3));
  $q->closeCursor();
}

function supprimer_pays($nom)
{

  global $db;  
  $q=$db->prepare('DELETE FROM pays WHERE pays.nom = ?');
  $q->execute(array($nom));
  $q->closeCursor();
}

function supprimer_user($pseudo)
{

  global $db;  
  $q=$db->prepare('DELETE FROM users WHERE users.pseudo = ?');
  $q->execute(array($pseudo));
  $q->closeCursor();
}

function supprimer_invite($ip)
{

  global $db;  
  $q=$db->prepare('DELETE FROM invites WHERE invites.ip_addr = ?');
  $q->execute(array($ip));
  $q->closeCursor();
}

function print_db_pays()
{

  global $db;
  $q = $db->prepare('SELECT * FROM pays ORDER BY nomFr ASC');  
  $q->execute();

  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  //*
  echo "<table class=\"table table-bordered table-hover table-responsive\">";
  echo "<thead>";
  echo "<tr>";
  echo "<th scope=\"col\">Pays</th>";
  echo "<th scope=\"col\">Capitale</th>";
  echo "<th scope=\"col\">latitude</th>";
  echo "<th scope=\"col\">longitude</th>";
  echo "<th scope=\"col\">ISO2</th>";
  echo "<th scope=\"col\">ISO3</th>";
  echo "<th scope=\"col\">Superficie</th>";
  echo "<th scope=\"col\">Action</th>";
  echo "</tr>";
  echo "</thead>";
  echo "<tbody id=\"tbody_pays\">";
  foreach( $resultat as $pays ) {  
    echo "<tr>";
    echo "<th scope=\"row\">" . $pays->nomFr . "</br><img src=\"". $pays->imgPath ."\" alt=\"\" height=\"25px\" width=\"50px\"/></th>";
    echo "<td>" . $pays->capitale . "</td>";
    echo "<td>" . $pays->lat . "</td>";
    echo "<td>" . $pays->lng . "</td>";
    echo "<td>" . strtoupper($pays->iso2) . "</td>";
    echo "<td>" . strtoupper($pays->iso3) . "</td>";
    echo "<td>" . (float) ($pays->superficie) . " km²</td>";
    echo "<td> <button id=\"". $pays->nom . "\" class=\"btn btn-danger supp_pays\"> Supprimer </button></td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";  
  //*/
}

function print_db_users_admin()
{
  global $db;
  $q = $db->prepare('SELECT * FROM users ORDER BY pseudo ASC');  
  $q->execute();

  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  //*
  echo "<table class=\"table table-bordered table-hover table-responsive\">";
  echo "<thead>";
  echo "<tr>";
  echo "<th scope=\"col\">Pseudo</th>";
  echo "<th scope=\"col\">Email</th>";
  echo "<th scope=\"col\">Date d'enregistrement</th>";
  echo "<th scope=\"col\">Action</th>";
  echo "</tr>";
  echo "</thead>";
  echo "<tbody id=\"tbody_pays\">";  
  foreach( $resultat as $user ) {    
    if(strcasecmp($user->pseudo, "admin")){
      echo "<tr>";
      echo "<th scope=\"row\">" . $user->pseudo . "</th>";
      echo "<td>" . $user->email . "</td>";
      echo "<td>" . $user->dateEnregistrement . "</td>";      
      echo "<td> <button id=\"". $user->pseudo . "\" class=\"btn btn-danger supp_user\"> Supprimer </button></td>";
      echo "</tr>";
    }
  }
  echo "</tbody>";
  echo "</table>";  
  //*/
}

function print_db_invites_admin()
{
  global $db;
  $q = $db->prepare('SELECT * FROM invites');  
  $q->execute();

  $resultat  = $q->fetchAll(PDO::FETCH_OBJ);
  //*
  echo "<table class=\"table table-bordered table-hover table-responsive\">";
  echo "<thead>";
  echo "<tr>";
  echo "<th scope=\"col\">Addresse IP</th>";
  echo "<th scope=\"col\">Question Repondues Pays (Max: ". QUEST_INV_MAX .")</th>";
  echo "<th scope=\"col\">Question Repondues Capitales (Max: ". QUEST_INV_MAX .")</th>";
  echo "<th scope=\"col\">Action</th>";
  echo "</tr>";
  echo "</thead>";
  echo "<tbody id=\"tbody_pays\">";
  foreach( $resultat as $invite ) {  
    echo "<tr>";
    echo "<th scope=\"row\">" . $invite->ip_addr . "</th>";
    echo "<td>" . $invite->nb_qst1 . "</td>";
    echo "<td>" . $invite->nb_qst2 . "</td>";
    echo "<td> <button id=\"". $invite->ip_addr . "\" class=\"btn btn-danger supp_inv\"> Supprimer </button></td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";  
  //*/
}
?>
