<?php

// Get passcodes and secrets
if (file_exists(__DIR__ . "/config.php")) {
    include_once __DIR__ . "/config.php";
} else {
    die("Config file not created");
}

if (!$apiToken || !is_array($apiToken) || !$apiPath) {
    die("Config file not set");
}

function newDocument(string $filePath, ?string $fileName = NULL, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (!file_exists($filePath)) {
        return "File can't be found";
    }
    if (mime_content_type($filePath) !== "application/pdf") {
        return "File must be a PDF";
    }
    if (empty($fileName)) {
        $fileName = basename($filePath);
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/new";

    $postArray = ['file' => new CurlFile($filePath, 'application/pdf', $fileName), 'is_saved' => true];

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
        CURLOPT_POSTFIELDS => $postArray,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "201" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not upload file to Scrive";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function updateDocument(int $docID, array $post, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }
    if (empty($post)) {
        return "Update data can't be empty";
    }

    $update = ['document' => json_encode($post)];

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/update";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
        CURLOPT_POSTFIELDS => $update,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "200" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not update Scrive document";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function startDocument(int $docID, ?string $url = NULL)
{
    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/start";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "200" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not start Scrive Document";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function getFile(int $docID, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/files/main/undefined";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!isset($status) || $status != "200") {
        $error['error'] = "Error! could not get file from Scrive";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function appendDocument(string $filePath, int $docID, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }
    if (!file_exists($filePath)) {
        return "File can't be found";
    }
    if (mime_content_type($filePath) !== "application/pdf") {
        return "File must be a PDF";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/appendfile";

    $postArray = ['file' => new CurlFile($filePath, 'application/pdf')];

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
        CURLOPT_POSTFIELDS => $postArray,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "201" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not upload file to Scrive";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function trashDocument(int $docID, ?string $url = NULL)
{
    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/trash";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "200" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not trash Scrive Document";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function deleteDocument(int $docID, ?string $url = NULL)
{
    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/delete";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "200" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not delete Scrive Document";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function restartDocument(int $docID, ?string $url = NULL)
{
    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/restart";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "201" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not restart Scrive Document";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function remindDocument(int $docID, ?string $url = NULL)
{
    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/remind";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!isset($status) || $status != "202") {
        $error['error'] = "Error! could not send a reminder invitation message";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return true;

}

function prolongDocument(int $docID, int $days, ?string $url = NULL)
{
    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }
    if (empty($days)) {
        return "Days to prolong can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/prolong";

    $postArray = ['days' => $days];

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
        CURLOPT_POSTFIELDS => $postArray,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "200" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not prolong document";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function getDocument(int $docID, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/get";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "200" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not send a reminder invitation message";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function documentCallback(int $docID, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/callback";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!isset($status) || $status != "202") {
        $error['error'] = "Error! could not get document info";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return true;


}

function recallDocument(int $docID, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/cancel";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "200" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not cancel document";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function signer(array $signerare)
{

    if (!array_key_exists('namn', $signerare) || !array_key_exists('mail', $signerare) || empty($signerare['namn'] || empty($signerare['mail']))) {
        return "Array does not contain name/mail keys or values";
    }

    $checkKeys = [
        "namn",
        "mail",
        "tel",
        "personnummer",
        "company",
        "attachments",
        "sign_order",
    ];

    foreach ($signerare as $key => $value) {
        if (!in_array($key, $checkKeys)) {
            continue;
        }
        ${$key} = $value;
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        return "Not a valid email address";
    }

    $nameArray = explode(' ', $namn, 2);
    $fornamn = $nameArray['0'];
    $efternamn = $nameArray['1'];

    $signArray = array(
        "is_author" => false,
        "signatory_role" => "signing_party",
        "fields" => array(
            array(
                "type" => "name",
                "order" => 1,
                "value" => $fornamn,
                "is_obligatory" => true,
                "should_be_filled_by_sender" => false,
                "editable_by_signatory" => false,
            ),
            array(
                "type" => "name",
                "order" => 2,
                "value" => $efternamn,
                "is_obligatory" => true,
                "should_be_filled_by_sender" => false,
                "editable_by_signatory" => false,
            ),
            array(
                "type" => "email",
                "value" => $mail,
                "is_obligatory" => true,
                "should_be_filled_by_sender" => true,
                "editable_by_signatory" => false,
            ),
            array(
                "type" => "full_name",
            ),
        ),
        "consent_module" => null,
        "email_delivery_status" => "unknown",
        "mobile_delivery_status" => "unknown",
        "confirmation_email_delivery_status" => "unknown",
        "has_authenticated_to_view" => false,
        "delivery_method" => "email",
        "authentications" => array(
            "view" => array(
                "name" => "standard"
            ),
            "sign" => array(
                "name" => "standard"
            ),
            "view_archived" => array(
                "name" => "standard"
            )
        ),
        "authentication_method_to_view" => "standard",
        "authentication_method_to_sign" => "standard",
        "authentication_method_to_view_archived" => "standard",
        "confirmation_delivery_method" => "email",
        "notification_delivery_method" => "none",
        "allows_highlighting" => false,
        "hide_personal_number" => false,
        "can_forward" => false,
        "attachments" => array(),
        "highlighted_pages" => array(),
        "document_roles" => array(),

    );

    if (isset($tel) && !empty($tel)) {
        $tel = trim(str_replace(' ', '', $tel));
        $tel = str_replace('-', '', $tel);
        $tel = str_replace('+', '', $tel);
        // if (!str_starts_with($tel, '46')) { //php 8
        if (substr($tel, 0, 2) !== "46") {
            $tel = "46" . $tel;
        }
        $tel = "+" . $tel;

        $signArray['fields'][] = array(
            "type" => "mobile",
            "value" => $tel,
            "is_obligatory" => false,
            "should_be_filled_by_sender" => false,
            "editable_by_signatory" => false,
            "placements" => array(),
            "description" => null
        );

    }
    if (isset($company) && !empty($company)) {

        $signArray['fields'][] = array(
            "type" => "company",
            "value" => "",
            "is_obligatory" => false,
            "should_be_filled_by_sender" => false,
            "editable_by_signatory" => false,
            "placements" => array(),
            "description" => null
        );

    }
    if (isset($personnummer) && !empty($personnummer)) {

        $signArray['fields'][] = array(
            "type" => "personal_number",
            "value" => $personnummer,
            "is_obligatory" => true,
            "should_be_filled_by_sender" => false,
            "editable_by_signatory" => false,
            "placements" => array(),
            "description" => null
        );

    }

    if (isset($attachments) && is_array($attachments)) {

        foreach ($attachments as $requestAttachment) {
            if (isset($requestAttachment['name']) && isset($requestAttachment['description'])) {

                $signArray["attachments"][] = [

                    "name" => $requestAttachment['name'],
                    "description" => $requestAttachment['description'],
                    "required" => true,
                    "add_to_sealed_file" => true,
                    "file_id" => null,
                    "file_name" => null

                ];
            }
        }
    }

    if (isset($sign_order) && !empty($sign_order)) {
        $signArray['sign_order'] = $sign_order;
    }

    return $signArray;
}

function signerCompany(array $signerare)
{

    if (!array_key_exists('company', $signerare) || !array_key_exists('mail', $signerare) || empty($signerare['company'] || empty($signerare['mail']))) {
        return "Array does not contain name/mail keys or values";
    }

    $checkKeys = [
        "company",
        "mail",
        "tel",
        "org",
        "namn",
        "attachments",
        "sign_order",
    ];

    foreach ($signerare as $key => $value) {
        if (!in_array($key, $checkKeys)) {
            continue;
        }
        ${$key} = $value;
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        return "Not a valid email address";
    }
    if (isset($namn) && !empty($namn)) {
        $nameArray = explode(' ', $namn, 2);
        $fornamn = $nameArray['0'];
        $efternamn = $nameArray['1'];
    }

    $signArray = array(
        "is_author" => false,
        "signatory_role" => "signing_party",
        "fields" => array(

            array(
                "type" => "company",
                "value" => $company,
                "is_obligatory" => true,
                "should_be_filled_by_sender" => false,
                "editable_by_signatory" => false,

            ),
            array(
                "type" => "email",
                "value" => $mail,
                "is_obligatory" => true,
                "should_be_filled_by_sender" => true,
                "editable_by_signatory" => false,
            ),
        ),
        "consent_module" => null,
        "sign_order" => 1,
        "email_delivery_status" => "unknown",
        "mobile_delivery_status" => "unknown",
        "confirmation_email_delivery_status" => "unknown",
        "has_authenticated_to_view" => false,
        "delivery_method" => "email",
        "authentications" => array(
            "view" => array(
                "name" => "standard"
            ),
            "sign" => array(
                "name" => "standard"
            ),
            "view_archived" => array(
                "name" => "standard"
            )
        ),
        "authentication_method_to_view" => "standard",
        "authentication_method_to_sign" => "standard",
        "authentication_method_to_view_archived" => "standard",
        "confirmation_delivery_method" => "email",
        "notification_delivery_method" => "none",
        "allows_highlighting" => false,
        "hide_personal_number" => false,
        "can_forward" => false,
        "attachments" => array(),
        "highlighted_pages" => array(),
        "document_roles" => array(),
    );

    if (isset($tel) && !empty($tel)) {
        $tel = trim(str_replace(' ', '', $tel));
        $tel = str_replace('-', '', $tel);
        $tel = str_replace('+', '', $tel);
        // if (!str_starts_with($tel, '46')) { //php 8
        if (substr($tel, 0, 2) !== "46") {
            $tel = "46" . $tel;
        }
        $tel = "+" . $tel;

        $signArray['fields'][] = array(
            "type" => "mobile",
            "value" => $tel,
            "is_obligatory" => false,
            "should_be_filled_by_sender" => false,
            "editable_by_signatory" => false,
            "placements" => array(),
            "description" => null
        );

    }

    if (!isset($namn)) {
        $signArray['fields'][] = array(
            "type" => "name",
            "order" => 1,
            "value" => $company,
            "is_obligatory" => true,
            "should_be_filled_by_sender" => false,
            "editable_by_signatory" => false,
        );
    } else {
        if (isset($fornamn) && !empty($fornamn)) {
            $signArray['fields'][] = array(
                "type" => "name",
                "order" => 1,
                "value" => $fornamn,
                "is_obligatory" => true,
                "should_be_filled_by_sender" => false,
                "editable_by_signatory" => false,
            );
        }
        if (isset($efternamn) && !empty($efternamn)) {
            $signArray['fields'][] = array(
                "type" => "name",
                "order" => 2,
                "value" => $efternamn,
                "is_obligatory" => true,
                "should_be_filled_by_sender" => false,
                "editable_by_signatory" => false,
            );
        }
    }

    if (isset($org) && !empty($org)) {

        $signArray['fields'][] = array(
            "type" => "company_number",
            "value" => $org,
            "is_obligatory" => true,
            "should_be_filled_by_sender" => false,
            "editable_by_signatory" => false,
            "placements" => array(),
            "description" => null
        );

    }

    if (isset($attachments) && is_array($attachments)) {

        foreach ($attachments as $requestAttachment) {
            if (isset($requestAttachment['name']) && isset($requestAttachment['description'])) {

                $signArray["attachments"][] = [

                    "name" => $requestAttachment['name'],
                    "description" => $requestAttachment['description'],
                    "required" => true,
                    "add_to_sealed_file" => true,
                    "file_id" => null,
                    "file_name" => null

                ];
            }
        }
    }

    if (isset($sign_order) && !empty($sign_order)) {
        $signArray['sign_order'] = $sign_order;
    }

    return $signArray;
}

function defaultViewer(int $docID)
{

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    $getAuthor = json_decode(getDocument($docID));

    foreach ($getAuthor->parties as $value) {
        if ($value->is_author === false) {
            continue;
        }
        $returnArray = $value;
    }

    $returnArray->is_signatory = false;

    return $returnArray;
}

function changeAuthor(array $getAuthor, ?bool $signerare = false, ?bool $callback = false, ?int $sign_order = 0)
{

    foreach ($getAuthor as $value) {
        if ($value->is_author === false) {
            continue;
        }
        $returnArray = $value;
    }
    if (!$signerare) {
        $returnArray->is_signatory = false;
    }
    if (!$callback) {
        $returnArray->confirmation_delivery_method = 'none';
    }

    $returnArray->sign_order = $sign_order;

    return $returnArray;

}

function getAuthor(int $docID)
{

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    $getAuthor = json_decode(getDocument($docID));

    foreach ($getAuthor->parties as $value) {
        if ($value->is_author === false) {
            continue;
        }
        $returnArray = $value;
    }

    return $returnArray;

}

function listDocuments(?array $filter = NULL, ?array $sort = NULL, ?int $max = NULL, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/list?offset=0";

    if (!empty($max) && is_numeric($max)) {
        $url .= "&max=$max";
    }

    if (is_array($filter) && count($filter) > 0) {

        $checkFilter = [
            "preparation",
            "awaiting_start",
            "pending",
            "closed",
            "canceled",
            "timedout",
            "rejected",
            "document_error"
        ];

        foreach ($filter as $check) {
            if (!in_array($check, $checkFilter)) {
                $noFilter = true;
            }
        }

        if (!$noFilter) {
            $filterArray = [
                "filter_by" => "status",
                "statuses" => $filter
            ];
        }
    }
    if ($filterArray) {
        $url .= "&filter=[" . json_encode($filterArray) . "]";
    }

    if (is_array($sort) && count($sort) === 2) {

        $orderCheck = [
            "ascending",
            "descending"
        ];
        $sort_byCheck = [
            "title",
            "status",
            "mtime",
            "author",
        ];

        if (in_array($sort['order'], $orderCheck) && in_array($sort['sort_by'], $sort_byCheck)) {
            $sortArray = $sort;
        }
    }
    if ($sortArray) {
        $url .= "&sorting=[" . json_encode($sortArray) . "]";
    }

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!isset($status) || $status != "200") {
        $error['error'] = "Error! could not get documents";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function changeMailPhone(int $docID, int $signerID, ?string $email = NULL, ?string $phone = NULL, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }
    if (empty($signerID)) {
        return "Signer ID can't be empty";
    }
    if (empty($email) && empty($phone)) {
        return "Either email or phone must be set";
    }

    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Not a valid email address";
        }
        $post['email'] = $email;
    }
    if (!empty($phone)) {
        $tel = trim(str_replace(' ', '', $phone));
        $tel = str_replace('-', '', $tel);
        $tel = str_replace('+', '', $tel);
        // if (!str_starts_with($tel, '46')) { //php 8
        if (substr($tel, 0, 2) !== "46") {
            $tel = "46" . $tel;
        }
        $post['mobile_number'] = "+" . $tel;
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/$signerID/changeemailandmobile";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
        CURLOPT_POSTFIELDS => $post,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "200" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could update signer email and/or phone";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function setReminder(int $docID, ?int $days = NULL, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/setautoreminder";

    $post = array();
    if (!empty($days)) {
        $post['days'] = $days;
    }

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
        CURLOPT_POSTFIELDS => $post,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);
    $docIDresponse = $jsonResponse->id;

    if (!isset($status) || $status != "200" || !isset($docIDresponse) || empty($docIDresponse)) {
        $error['error'] = "Error! could not set reminder document(s)";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($docIDresponse)) {
            $error['docID'] = "DocID: $docIDresponse";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function trashBatch(array $docIDs, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (count($docIDs) === 0) {
        return "Document IDs can't be empty";
    }

    $post = ["document_ids" => json_encode($docIDs)];

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/trash";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
        CURLOPT_POSTFIELDS => $post,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);


    if (!isset($status) || $status != "200" || !isset($jsonResponse->documents) || empty($jsonResponse->documents)) {
        $error['error'] = "Error! could not trash document(s)";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function deleteBatch(array $docIDs, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (count($docIDs) === 0) {
        return "Document IDs can't be empty";
    }

    $post = ["document_ids" => json_encode($docIDs)];

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/delete";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
        CURLOPT_POSTFIELDS => $post,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);

    if (!isset($status) || $status != "200" || !isset($jsonResponse->documents) || empty($jsonResponse->documents)) {
        $error['error'] = "Error! could not delete document(s)";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}

function setAPIcallback(int $docID, ?string $urlCallback = NULL)
{

    global $apiCallback;

    if (!$urlCallback) {
        if ($apiCallback) {
            $urlCallback = $apiCallback;
        } else {
            return "No callback url is given!";
        }
    }

    $updateArray = array("api_callback_url" => $urlCallback);
    $resonse = updateDocument($docID, $updateArray);
    return $resonse;

}

function documentHistory(int $docID, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/history";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $jsonResponse = json_decode($response);

    if (!isset($status) || $status != "200" || !isset($jsonResponse->events) || empty($jsonResponse->events)) {
        $error['error'] = "Error! could not delete document(s)";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }


    return $response;


}

function getAttachment(int $docID, int $attachmentID, ?string $filename = NULL, ?string $url = NULL)
{

    global $apiToken, $apiPath, $apiTest;

    if (empty($docID)) {
        return "Document ID can't be empty";
    }

    if (empty($attachmentID)) {
        return "Attachment ID can't be empty";
    }

    if ($url === "test") {
        $apiPath = $apiTest;
    } else if (!empty($url)) {
        return "Argument url ($url) isn't valid";
    }

    $url = "$apiPath/api/v2/documents/$docID/files/$attachmentID/$filename";

    $curlConfig = [
        CURLOPT_URL => $url,
        CURLOPT_POST => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $apiToken,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!isset($status) || $status != "200") {
        $error['error'] = "Error! could not get file from Scrive";
        if (isset($status)) {
            $error['status'] = "HTTP status: $status";
        }
        if (isset($response)) {
            $error['response'] = $response;
        }
        return $error;
    }

    return $response;

}
