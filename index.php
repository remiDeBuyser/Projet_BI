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

preg_match("/<div class=\"c-faceplate__price \">(.*)<\/div>/", $result, $matches);

// affiche le cours de l'action
preg_match("/[0-9]+\.[0-9]+/", $matches[0], $matches);
$actual_price = $matches[0];
$actual_time = date("Y-m-d H:i:s");
print("<p>Prix actuel : ".$actual_price."$</p>");
print("<p>Heure : ".$actual_time."</p>");
