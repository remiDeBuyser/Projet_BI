<?php
require_once 'vendor/autoload.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Chrome\ChromeOptions;

try {
	// On se connecte à MySQL
	$mysqlClient = new PDO('mysql:host=db4free.net;dbname=coursbi;charset=utf8', 'coursbi', 'mv.G8#GSkLFp');
} catch(Exception $e) {
	// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
} 

$serverUrl = 'http://localhost:4444';

$scriptDirectory = __DIR__;

// l'action à scraper
$isin = "US0378331005";
// est ce que l'action est internationale
$isInternational = false;

// préparation du chrome driver
$options = new ChromeOptions();
$prefs = array('download.default_directory' => $scriptDirectory);
$options->setExperimentalOption('prefs', $prefs);
$capabilities = DesiredCapabilities::chrome(); 
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

// création du driver
$driver = RemoteWebDriver::create($serverUrl, $capabilities);
// la page de laquelle on part pour scraper
$driver->get('https://www.boursorama.com/cours/AAPL/');


// permet de fermer la popup des cookies sans accepter les cookies
$continueWithoutAcceptCookies = $driver->findElement(WebDriverBy::cssSelector('span.didomi-continue-without-agreeing'));
$continueWithoutAcceptCookies->click();

// permet d'appuyer sur le bouton pour se connecter
$getMemberBtn = $driver->findElement(WebDriverBy::xpath('/html/body/div[6]/div[3]/div[2]/ol/li[3]/ol/li[1]/button'));
$getMemberBtn->click();

// permet de remplir le formulaire de connexion partie login 
$getLoginBtn = $driver->findElement(WebDriverBy::xpath('/html/body/div[7]/div/div[2]/form/div[1]/div/div[1]/div/input'));
$getLoginBtn->click();
$getLoginBtn->sendKeys('coursbiarbre@gmail.com');

// permet de remplir le formulaire de connexion partie password 
$getPasswordBtn = $driver->findElement(WebDriverBy::xpath('/html/body/div[7]/div/div[2]/form/div[1]/div/div[2]/div/div/input'));
$getPasswordBtn->click();
$getPasswordBtn->sendKeys('CJoN&DEn7PnJkpa$');

// permet de submit le formulaire de connexion 
$getSubmitLoginBtn = $driver->findElement(WebDriverBy::xpath('/html/body/div[7]/div/div[2]/form/div[2]/div/button'));
$getSubmitLoginBtn->click();

// on attend 2 secondes pour que la page se charge bien 
sleep(2);

// on va sur la page de téléchargement
$getDownloadsPage = $driver->findElement(WebDriverBy::xpath('/html/body/div[6]/div[3]/div[2]/ol/li[4]/div/div/div[2]/div/ul/li[4]'));
$getDownloadsPage->click();

// si l'action est internationale on va sur la page internationale
if($isInternational) {
    // on change de tab pour aller sur la page internationale
    $getInternationalTab = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[2]/nav/ul/li[2]/a'));
    $getInternationalTab->click();
}

// on choisit de trouver l'action grâce à son ISIN
$getCheckBox = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[2]/div/ul/li[3]/label'));
$getCheckBox->click();

// on met l'ISIN de l'action
$getIsin = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[2]/div/ul/li[3]/div[3]/input'));
$getIsin->sendKeys($isin);

// on ajoute une vieille date pour pouvoir télécharger l'historique de l'action
$getDate = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[6]/div[1]/div/div/input'));
$getDate->clear();
sleep(1);
$getDate->sendKeys("01011900");
sleep(1);

// on récupère la date de fin de l'historique de l'action pour pouvoir connaitre le nom du fichier qui va être téléchargé
$getCurrentDate = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[6]/div[2]/div/div/input'));
$getCurrentDate->getAttribute('value');

// on formate la date pour avoir le bon format
$tmpCurrentDate = explode("/",$getCurrentDate->getAttribute('value'));
$formatedCurrentDate = $tmpCurrentDate[2]."-".$tmpCurrentDate[1]."-".$tmpCurrentDate[0];

// on submit le formulaire pour télécharger l'historique de l'action
$getFinalSubmit = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[11]/div/input'));
$getFinalSubmit->click();

// on attend 5 secondes pour que le fichier soit bien téléchargé
sleep(5);

// le nom du fichier qui a été téléchargé
$file = "SICOVAM_".$formatedCurrentDate.".txt";

// on prépare les deux requêtes SQL qui vont être utilisées
// permet de savoir si le jour que l'on veut insérer existe déjà dans la base de données
$sqlQueryWhere = "SELECT COUNT(*) AS count FROM `Historic` WHERE `date_historic` = '%s' AND `isin` = '%s'";
// permet d'insérer le jour dans la base de données
$sqlQueryInsert = "INSERT INTO `Historic`(`date_historic`,`opening_price`, `high_price`, `low_price`, `closing_price`, `volume`, `currency`,`isin`) VALUES ('%s', '%s', '%s', '%s', '%s', %d, '%s', '%s')";

// on ouvre le fichier téléchargé s'il existe 
if (($handle = fopen($file, "r")) !== FALSE) {
    // on passe la première ligne du fichier
    fgetcsv($handle, 1000, "\t");
    
    // on parcourt le fichier ligne par ligne
    while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
        // on récupère le label de l'action
        $label = $data[1];
        // on récupère la date de l'action
        $date = explode("/",$data[2]);
        $date_historic = $date[2]."-".$date[1]."-".$date[0];
        // on récupère le prix d'ouverture de l'action
        $opening_price = str_replace(',', '.', $data[3]);
        // on récupère le prix le plus haut de la journée de l'action
        $high_price = str_replace(',', '.', $data[4]);
        // on récupère le prix le plus bas de la journée de l'action
        $low_price = str_replace(',', '.', $data[5]);
        // on récupère le prix de fermeture de l'action
        $closing_price = str_replace(',', '.', $data[6]);
        $volume = (int)$data[7];
        // on récupère le volume de l'action
        $currency = $data[8];
        
        // on check si le jour existe déjà dans la base de données
        $checkSql = sprintf($sqlQueryWhere, $date_historic, $isin);
        $result = $mysqlClient->query($checkSql);
        
        // si le jour n'existe pas on l'insère dans la base de données
        $count = $result->fetchColumn();
        if ($count == 0) {
            // insertion du jour dans la base de données
            $sql = sprintf($sqlQueryInsert, $date_historic, $opening_price, $high_price, $low_price, $closing_price, $volume, $currency, $isin);
            $result = $mysqlClient->query($sql);
        }
        
        // si il y a une erreur on l'affiche
        if (!$result) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    // si pas de soucis on supp le fichier pour ne pas avoir de problème si on lance le script 2 fois dans la même journée (il aura le même nom et donc si on le supprime pas ça va poser problème)
    if (unlink($file)) {
        echo "Le fichier a été supprimé avec succès.";
    } else {
        echo "Une erreur s'est produite lors de la suppression du fichier.";
    }
}