<?php

$url = "https://www.boursorama.com/cours/AAPL/";

// affiche le code source de la page avec CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

// affiche le titre de la page
preg_match_all("/<title>(.*)<\/title>/", $result, $matches);
print("<p>".$matches[1][0] . "</p>");

preg_match_all("/<span>(.*)<\/span>/", $result, $matches);
var_dump($matches);