<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//DateTime for reaquest
$requestTime = new DateTime('now');
$request['time'] = $requestTime->format('Y-m-d H:i:s');

if (!empty($_POST)) {
    $request['POST'] = $_POST;
}

//Example for internal test if apitest if set to 'myTestFolder'
$urlRequest = explode('/', $_SERVER['REQUEST_URI']);
if ($urlRequest[2] === "myTestFolder") {
    // some logic i.e. logging
    if (!empty(print_r(json_decode(file_get_contents("php://input")), true))) {
        $request['INPUT'] = print_r(json_decode(file_get_contents("php://input")), true);
    }
    if (!empty($_FILES)) {
        $request['files'] = $_FILES;
    }
    if (!empty($_GET)) {
        $request['GET'] = $_GET;
    }
    if (!empty($_SERVER['REQUEST_METHOD'])) {
        $request['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $request['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
    }

    error_log(json_encode($request) . ",\n", 3, __DIR__ . "/myTestFolder/logFiles/log.log");
    die;
}

// check if post is a valid Scrive document
if (
    !isset($_POST['document_id'])
    || empty($_POST['document_id'])
    || !is_numeric($_POST['document_id'])
    || !isset($_POST['document_signed_and_sealed'])
    || empty($_POST['document_signed_and_sealed'])
    || !boolval($_POST['document_signed_and_sealed'])
    || !isset($_POST['document_json'])
    || empty($_POST['document_json'])
) {
    die;
}
// I.e logging or change status of documet internally
error_log(json_encode($request) . ",\n", 3, __DIR__ . "/logFolder/logFile.log");
