<?php
require_once 'vendor/autoload.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Chrome\ChromeOptions;

$serverUrl = 'http://localhost:4444';

$scriptDirectory = __DIR__ + "/docs";

$options = new ChromeOptions();
$prefs = array('download.default_directory' => $scriptDirectory);
$options->setExperimentalOption('prefs', $prefs);
$capabilities = DesiredCapabilities::chrome(); 
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

$driver = RemoteWebDriver::create($serverUrl, $capabilities);
$driver->get('https://www.boursorama.com/cours/AAPL/');

$continueWithoutAcceptCookies = $driver->findElement(WebDriverBy::cssSelector('span.didomi-continue-without-agreeing'));
$continueWithoutAcceptCookies->click();

// $getFullScreenButton = $driver->findElement(WebDriverBy::id('fullscreen_btn'));
// $getFullScreenButton->click();

// $get20years = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/section[1]/div[2]/article/div[1]/div/div[1]/div[4]/div[2]/div[1]/div[3]/div[4]'));
// $get20years->click();

// $getDownload = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/section[1]/div[2]/article/div[1]/div/div[1]/div[4]/div[3]/div[9]/div/span'));
// $getDownload->click();


// sleep(10);
// $driver->quit();


// $getConnexionBtn = $driver->findElement(WebDriverBy::xpath('/html/body/div[6]/div[3]/div[2]/ol/li[3]/ol/li[3]/button'));
// $getConnexionBtn->click();


$getMemberBtn = $driver->findElement(WebDriverBy::xpath('/html/body/div[6]/div[3]/div[2]/ol/li[3]/ol/li[1]/button'));
$getMemberBtn->click();

$getLoginBtn = $driver->findElement(WebDriverBy::xpath('/html/body/div[7]/div/div[2]/form/div[1]/div/div[1]/div/input'));
$getLoginBtn->click();
$getLoginBtn->sendKeys('coursbiarbre@gmail.com');

$getPasswordBtn = $driver->findElement(WebDriverBy::xpath('/html/body/div[7]/div/div[2]/form/div[1]/div/div[2]/div/div/input'));
$getPasswordBtn->click();
$getPasswordBtn->sendKeys('CJoN&DEn7PnJkpa$');

$getSubmitLoginBtn = $driver->findElement(WebDriverBy::xpath('/html/body/div[7]/div/div[2]/form/div[2]/div/button'));
$getSubmitLoginBtn->click();
// $getProfileButton = $driver->findElement(WebDriverBy::xpath('/html/body/div[6]/div[3]/div[2]/ol/li[4]/button/div[2]/span'));
// $getProfileButton->click()->wait(3);
// $driver->get('https://www.boursorama.com/espace-membres/telecharger-cours/international')->wait(5);
sleep(2);
print('coucou je suis la');
$getDownloadsPage = $driver->findElement(WebDriverBy::xpath('/html/body/div[6]/div[3]/div[2]/ol/li[4]/div/div/div[2]/div/ul/li[4]'));
$getDownloadsPage->click();

// $getInternationalTab = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[2]/nav/ul/li[2]/a'));
// $getInternationalTab->click();


$getCheckBox = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[2]/div/ul/li[3]/label'));
$getCheckBox->click();


$getCheckBox = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[2]/div/ul/li[3]/label'));
$getCheckBox->click();


$getIsin = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[2]/div/ul/li[3]/div[3]/input'));
$getIsin->click();
$getIsin->sendKeys('US0378331005');

$getDate = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[6]/div[1]/div/div/input'));
$getDate->clear();
$getDate->sendKeys("01011900");

// $getDate->click();
// $getDate->click();
// $getDate->sendKeys('01');
// $getDate->click();
// $getDate->sendKeys('01');
// $getDate->click();
// $getDate->sendKeys('1900');

$getFinalSubmit = $driver->findElement(WebDriverBy::xpath('/html/body/main/div/div[1]/div[4]/div[1]/div/div/form/div[11]/div/input'));
$getFinalSubmit->click();