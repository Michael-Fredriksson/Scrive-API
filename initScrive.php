<?php

if (file_exists("config.php")) {
    include_once ("config.php");
}

if ($token) {
    die("Credentials already set. Delete the config file if you need to update the credentials");
}

if (!isset($_GET['email']) || !isset($_GET['password']) || empty($_GET['email'])|| empty($_GET['password'])) {
    die("Pass email and password for initiation in url header as ?email={Scrive account email}&password={Scrive account password} without the curly brackets");
}

if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
    die("Not a valid email address");
}

$data = [
    'email' => $_GET['email'],
    'password' => $_GET['password']
];

if (isset($_GET['test']) && $_GET['test'] === "true") {
    //Test enviroment
    $url = 'https://api-testbed.scrive.com/api/v2/getpersonaltoken'; 
    $apiPath = "https://api-testbed.scrive.com";
} else {
    //live enviroment
    $url = 'https://scrive.com/api/v2/getpersonaltoken';
    $apiPath = "https://scrive.com"; 
}

$apiCallback = (isset($_GET['webhook']) && !empty($_GET['webhook'])) ? 'https://" . $_SERVER[\'HTTP_HOST\'] . "/' . ltrim($_GET['webhook'], '/') : "";
$apiTest = (isset($_GET['apitest']) && !empty($_GET['apitest'])) ? 'https://" . $_SERVER[\'HTTP_HOST\'] . "/' . ltrim($_GET['apitest'], '/') : "";

if (!empty($apiCallback)) {
    $apiCallback = rtrim($apiCallback, '/') . '/';
}
if (!empty($apiTest)) {
    $apiTest = rtrim($apiTest, '/');
}


$curlConfig = [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => $data
];

$ch = curl_init();
curl_setopt_array($ch, $curlConfig);
$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($status != 200) {
    die("HTTP status $status<br />$response");

}

$apiResponse = json_decode($response);

$arrayToFile = ["oauth_signature_method" => "PLAINTEXT", "oauth_consumer_key" => $apiResponse->apitoken, "oauth_token" => $apiResponse->accesstoken, "oauth_signature" => $apiResponse->apisecret . "&" . $apiResponse->accesssecret];

$arrayTxt = var_export($arrayToFile, true);
$myfile = fopen("config.php", "w");
$txt = '<?php
$oauth = ' . $arrayTxt . ';
foreach ($oauth as $key => $value) {
    if (!empty($toToken)) {
        $toToken .= ", ";
    }
    $toToken .= $key . \'="\' . $value . \'"\';
}
if (!empty($toToken)) {
    $apiToken[] = "Authorization: " . $toToken;
}
$apiPath = "' . $apiPath . '";
$apiTest = "' . $apiTest . '";
$apiCallback = "' . $apiCallback . '";';

fwrite($myfile, $txt);
fclose($myfile);
if (!file_exists("log")) {
    mkdir("log");
}
if (!file_exists("tmp")) {
    mkdir("tmp");
}
die("Scrive configuration is completed");