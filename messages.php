<?php

//recupere le contenu de la page des forums boursorama de l'action AAPL avec CURL


$url = "https://www.boursorama.com/bourse/forum/AAPL/";

$page = file_get_contents($url);

// recupere le nombre de pages de forum
preg_match("/data-pagination\-button\-last\-page=\"(.*)\" data\-pagination/", $page, $matches);

$nbPages = intval($matches[1]);
$nbforum = 0;
$nbMessages = 0;

$nbPages = 1; // pour tester

for ($i = 1; $i <= $nbPages; $i++) {
    $url = "https://www.boursorama.com/bourse/forum/AAPL/page-".strval($i);
    $page = file_get_contents($url);
    // recupere les liens des pages de forum
    preg_match_all("/href=\"(.*?)\"\s*title=\"Voir le sujet\"/", $page, $matches);
    for($j = 0; $j < count($matches[1]); $j++) {
        $forumURL = "https://www.boursorama.com".$matches[1][$j];
        //print($forumURL . "\n");

        $pageForum = file_get_contents($forumURL);
        preg_match_all("/pagination__content.*>(\d*)<\/span>/", $pageForum, $matchesNbPages);

        if(sizeof($matchesNbPages[1]) == 0) {
            $nbPagesForum = 1;
        } else {
            $nbPagesForum = intval($matchesNbPages[1]);
        }

        for ($k = 1; $k <= $nbPagesForum; $k++) {
            $pageForum = file_get_contents($forumURL."page-".$k);

            // retire les saut de ligne
            $pageForum = str_replace("\n", "", $pageForum);
            // retire les </br>
            $pageForum = str_replace("<br />", "", $pageForum);
            $pageForum = preg_replace("# {2,}#"," ",preg_replace("#(\r\n|\n\r|\n|\r)#"," ",$pageForum));
            // recupere les nom des utilisateurs, les dates et les messages
            preg_match_all("/<button.*?data\-popover\-url=\"\/espace\-membres\/profil\/(.*?)\/.*?\/button>.*?c-source__time\">(.*?)<\/span>.*?message__text.*?>(.*?)<\/p>/", $pageForum, $matches3);

            // affiche les resultats
            for($l = 0; $l < count($matches3[1]); $l++) {/*
                print("nom : ".$matches3[1][$l]."\n");
                print("date : ".$matches3[2][$l]."\n");
                print("message : ".$matches3[3][$l]."\n");
                print("\n");*/
                $nbMessages++;
            }
        }
        $nbforum++;
    }
}

print("nb forum : ".$nbforum."\n");

print("nb messages : ".$nbMessages."\n");
