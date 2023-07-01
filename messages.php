<?php

//recupere le contenu de la page des forums boursorama de l'action AAPL avec CURL
try {
	// On se connecte à MySQL
	$mysqlClient = new PDO('mysql:host=db4free.net;dbname=coursbi;charset=utf8', 'coursbi', 'mv.G8#GSkLFp');
} catch(Exception $e) {
	// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
}
date_default_timezone_set('Europe/Paris');
$timezone = new DateTimeZone('Europe/Paris');
function convertDateToEnglish($date){
    $months = array('janvier'=>'January', 'février'=>'February', 'mars'=>'March', 'avril'=>'April', 'mai'=>'May', 'juin'=>'June', 'juillet'=>'July', 'août'=>'August', 'septembre'=>'September', 'octobre'=>'October', 'novembre'=>'November', 'décembre'=>'December');
    foreach($months as $k=>$v){
        $date = str_replace($k, $v, strtolower($date));
    }
    return $date;
}


$baseurl = "https://www.boursorama.com/bourse/forum/AAPL/";

$page = file_get_contents($baseurl);

// recupere le nombre de pages de forum
preg_match("/data-pagination\-button\-last\-page=\"(.*)\" data\-pagination/", $page, $matches);

$nbPages = intval($matches[1]);
$nbforum = 0;
$nbMessages = 0;

$nbPages = 1; // pour tester

for ($i = 1; $i <= $nbPages; $i++) {
    $url = $baseurl . "page-" . strval($i);
    $page = file_get_contents($url);
    // recupere les liens des pages de forum
    preg_match_all("/href=\"(.*?)\"\s*title=\"Voir le sujet\"/", $page, $matches);
    for ($j = 0; $j < count($matches[1]); $j++) {
        $forumURL = "https://www.boursorama.com" . $matches[1][$j];
        print($forumURL . "\n");
        $pageForum = file_get_contents($forumURL);
        // recupere le titre du forum
        preg_match("/<h1 class=\"c-title.*?\">(.*?)<\/h1>/", $pageForum, $matches2);
        print($matches2[1] . "\n");
        
        // recupere le code isin de l'action
        preg_match("/<h2 class=\"c-faceplate__isin.*?\">(.*?) /", $pageForum, $matchesIsin);
        print($matchesIsin[1] . "\n");

        // insert le forum dans la base de donnees et recupere son id
        $sqlQuery = 'INSERT INTO Forum (forumName, isin) VALUES ("' . $matches2[1] . '", "' . $matchesIsin[1] . '")';
        $recipesStatement = $mysqlClient->prepare($sqlQuery);
        $recipesStatement->execute();
        $forumId = $mysqlClient->lastInsertId();

        // recupere le nombre de pages du forum
        preg_match_all("/pagination__content.*>(\d*)<\/span>/", $pageForum, $matchesNbPages);

        if (sizeof($matchesNbPages[1]) == 0) {
            $nbPagesForum = 1;
        } else {
            $nbPagesForum = intval($matchesNbPages[1]);
        }

        for ($k = 1; $k <=  $nbPagesForum; $k++) {
            $pageForum = file_get_contents($forumURL . "page-" . $k);

            // retire les saut de ligne
            $pageForum = str_replace("\n", "", $pageForum);
            // retire les </br>
            $pageForum = str_replace("<br />", "", $pageForum);
            $pageForum = preg_replace("# {2,}#", " ", preg_replace("#(\r\n|\n\r|\n|\r)#", " ", $pageForum));
            // recupere les nom des utilisateurs, les dates et les messages
            preg_match_all("/<button.*?data\-popover\-url=\"\/espace\-membres\/profil\/(.*?)\/.*?\/button>.*?c-source.*?\">(.*?)<\/div>.*?message__text.*?>(.*?)<\/p>/", $pageForum, $matches3);

            // affiche les resultats
            for ($l = 0; $l < count($matches3[1]); $l++) {
                // met la date au format sql
                preg_match_all("/>(.*?)</", $matches3[2][$l], $matchesDate);
                if (count($matchesDate[1]) == 1) {
                    // met la date au format sql
                    $timestamp = strtotime($matchesDate[1][0]);
                } else {
                    $date = $matchesDate[1][0] . " " . $matchesDate[1][4];
                    $dateInEnglish = convertDateToEnglish($date);
                    $timestamp = strtotime($dateInEnglish);
                }
                // timestamp au format sql
                $timestamp = date('Y-m-d H:i:s', $timestamp);
                // insert le message dans la base de donnees

                $sqlQuery = 'INSERT INTO Message (id_Forum, pseudo, dateMessage, message) VALUES (?, ?, ?, ?)';
                $recipesStatement = $mysqlClient->prepare($sqlQuery);
                $recipesStatement->bindParam(1, $forumId);
                $recipesStatement->bindParam(2, $matches3[1][$l]);
                $recipesStatement->bindParam(3, $timestamp);
                $recipesStatement->bindParam(4, $matches3[3][$l]);
                $recipesStatement->execute();
                $nbMessages++;
            }
        }
        $nbforum++;
    }
}

print("nb forum : " . $nbforum . "\n");

print("nb messages : " . $nbMessages . "\n");
