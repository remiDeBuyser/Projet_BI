<?php

//recupere le contenu de la page des forums boursorama de l'action AAPL avec CURL


$url = "https://www.boursorama.com/bourse/forum/AAPL/";

$page = file_get_contents($url);

// recupere le nombre de pages de forum
preg_match("/data-pagination\-button\-last\-page=\"(.*)\" data\-pagination/", $page, $matches);

$nbPages = intval($matches[1]);
var_dump($nbPages);

// recupere les liens des pages de forum
preg_match_all("/href=\"(.*?)\"\s*title=\"Voir le sujet\"/", $page, $matches);

for($i = 0; $i < count($matches[1]); $i++) {
    $matches[1][$i] = "https://www.boursorama.com".$matches[1][$i];
    print($matches[1][$i]."\n");
}