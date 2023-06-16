<?php

$url = "https://www.boursorama.com/cours/AAPL/";

// affiche le code source de la page avec CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);$result = curl_exec($ch);
$result = str_replace("\n", "", $result);
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
// print($result);
preg_match("/<span class=\"c-instrument c-instrument--low\" (.*)<\/span>/", $result, $matches2);
preg_match("/[0-9]+\.[0-9]{4}/", $matches2[0], $matches2);
print("+bas : ".$matches2[0]."<br>");

preg_match("/<span class=\"c-instrument c-instrument--high\" (.*)<\/span>/", $result, $matches2);
preg_match("/[0-9]+\.[0-9]{4}/", $matches2[0], $matches2);
print("+haut : ".$matches2[0]."<br>");

preg_match("/<span class=\"c-instrument c-instrument--totalvolume\"(*.)<\/span>/", $result, $matches2);
var_dump($matches2);
preg_match("/[0,9]+/", $matches2[0], $matches2);
print"total volume : ".($matches2[0]."<br>");

preg_match("/<span class=\"c-instrument c-instrument--open\" (.*)<\/span>/", $result, $matches2);
preg_match("/[0-9]+\.[0-9]{4}/", $matches2[0], $matches2);
print("cours open : ".$matches2[0]."<br>");
