<?php

$url = "https://www.boursorama.com/cours/AAPL/";
try {
	// On se connecte à MySQL
	$mysqlClient = new PDO('mysql:host=db4free.net;dbname=coursbi;charset=utf8', 'coursbi', 'mv.G8#GSkLFp');
} catch(Exception $e) {
	// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
}
function addToDatabase($url, $mysqlClient) {
    // affiche le code source de la page avec CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);$result = curl_exec($ch);
    $result = str_replace("\n", "", $result);
    curl_close($ch);

    preg_match("/<div class=\"c-faceplate__price \">(.*)<\/div>/", $result, $matches);

    // recupere l'isin de l'action
    preg_match("/<h2 class=\"c-faceplate__isin\">(.*)<\/h2>/", $result, $matches2);
    preg_match("/[A-Z]{2}[0-9]{10}/", $matches2[0], $matches2);
    $isin = $matches2[0];
    print("ISIN : ".$isin);

    // affiche le cours de l'action
    preg_match("/[0-9]+\.[0-9]+/", $matches[0], $matches);
    $actual_price = floatval($matches[0]);

    // print($result);
    preg_match("/<span class=\"c-instrument c-instrument--low\" (.*)<\/span>/", $result, $matches2);
    preg_match("/[0-9]+\.[0-9]{4}/", $matches2[0], $matches2);
    $bas = floatval($matches2[0]);

    preg_match("/<span class=\"c-instrument c-instrument--high\" (.*)<\/span>/", $result, $matches2);
    preg_match("/[0-9]+\.[0-9]{4}/", $matches2[0], $matches2);
    $haut = floatval($matches2[0]);

    preg_match("/<span class=\"c-instrument c-instrument--totalvolume\" (.*)<\/span>/", $result, $matches2);
    preg_match("/[0,9]+/", $matches2[0], $matches2);
    $volume = intval($matches2[0]);

    preg_match("/<span class=\"c-instrument c-instrument--open\" (.*)<\/span>/", $result, $matches2);
    preg_match("/[0-9]+\.[0-9]{4}/", $matches2[0], $matches2);
    $open = floatval($matches2[0]);

    print("Prix actuel : ".$actual_price."$");
    print("+bas : ".$matches2[0]."<br>");
    print("+haut : ".$matches2[0]."<br>");
    print("total volume : ".$matches2[0]."<br>");
    print("cours open : ".$matches2[0]."<br>");


    # requete sql pour ajouter une donnée live
    $actual = date("H:i:s");
    if($actual >= "09:00:00" && $actual <= "17:30:00") {
        $sqlQuery = 'INSERT INTO `StockDataLive`(`opening_price`, `high_price`, `low_price`, `current_price`, `volume`, `ISIN`) VALUES (\''.$open.'\',\''.$haut.'\',\''.$bas.'\',\''.$actual_price.'\',\''.$volume.'\',\''.$isin.'\')';
        $recipesStatement = $mysqlClient->prepare($sqlQuery);
        $recipesStatement->execute();
    } else {
        print($open." ".$haut." ".$bas." ".$actual_price." ".$volume." ".$isin);
        $sqlQuery = 'INSERT INTO `StockDataEOD`(`opening_price`, `high_price`, `low_price`, `current_price`, `volume`, `ISIN`) VALUES (\''.$open.'\',\''.$haut.'\',\''.$bas.'\',\''.$actual_price.'\',\''.$volume.'\',\''.$isin.'\')';
        $recipesStatement = $mysqlClient->prepare($sqlQuery);
        $recipesStatement->execute();
    }
}

addToDatabase($url, $mysqlClient);
