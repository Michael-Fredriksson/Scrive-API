# Scrive API php integration

 PHP repository to work with scrive API v2.0 (e-signing service)

Scrive API is a document e-signing service [Scrive](https://www.scrive.com/). I've created this PHP repository as a job to automate e-signing process for a customer. If you're looking to implement Scrive API in your project I'll hope this could help you with the project.

This repository only contain the basic configuration, all interteral integrations must be made added to the correcorresponding file(s) i.e. index.php to handle webhook callbacks.

The repository includes files to handle API calls and returning webhooks.

The API documentation for Scrive API could be found here [Scrive API documentation](https://apidocs.scrive.com/).

---

## License

This repository is distributed under MIT License.

---

## Files included

 .htaccess  
 index.php  
 initScrive.php  
 scriveAPI.php  
 LICENCE  
 README.md

---

## Local setup and initiation  

- Copy the files to a desired directory
- Goto the initScrive.php with the relevant query strings in your browser:
  - email=`scrive account email` ! required
  - password=`scrive account password` ! required
  - test=true ! set test to true if using scrive test enviroment
  - webhook=`directory path for webhook callbacks` ! optional (if you're not using same site you've to change this parameter in initScrive.php, it's preset with `https://" . $_SERVER['HTTP_HOST'])
  - apitest=`directory path for testing API calls` ! optional (if you're not using same site you've to change this parameter in initScrive.php, it's preset with `https://" . $_SERVER['HTTP_HOST']) Set if you'll be testing the API calls to a diffrent url than scrive API. Usage on funtions are the $url argument = true

Once you run the init file it will connect to scrive and get the nessesary oauth setting for your account and create the nessesary config.php file witch is requried by scriveAPI.php.

To reconfigure the config file, delete it and run initScrive.php again or edit the file manually.

---

## Scrive functions

*Note*: Functions with argument $url. This argument is used for test the api calls to local location set by initScrive.php, use the argument as string `"test"`.

Document metadata update/changes can not be made (other than function `changeMailPhone()`) once the document is started.

If a function fails it tries to return http status along with error an document data is provided from API response.

---

`newDocument(string $filePath, ?string $fileName = NULL, ?string $url = NULL)`  
> New document: $filePath must be path to PDF file, $fileName name of document if empty basename of the pdf file will be used
>> *Return document json*

`updateDocument(int $docID, array $post, ?string $url = NULL)`  
>Update document: $docID scrive document ID, $post updates as array
>>*Return document json*

`startDocument(int $docID, ?string $url = NULL)`  
> Start the signing process: $docID scrive document ID
>> *Return document json*

`getFile(int $docID, ?string $url = NULL)`  
> Get main file: $docID scrive document ID
>> *Return PDF* (use header('Content-type: application/pdf') in php file if using php to show content)

`appendDocument(string $filePath, int $docID, ?string $url = NULL)`  
> Change the main PDF on draft: $filePath must be path to PDF file, $docID scrive document ID
>> *Return document json*

`trashDocument(int $docID, ?string $url = NULL)`  
> Move a document to trash: $docID scrive document ID
>> *Return document json*

`deleteDocument(int $docID, ?string $url = NULL)`  
> Deletes a trashed document: $docID scrive document ID
>> *Return document json*

`recallDocument(int $docID, ?string $url = NULL)`  
> Cancel a started document: $docID scrive document ID
>> *Return document json*

`restartDocument(int $docID, ?string $url = NULL)`  
> Creates a new document draft from canceled document: $docID scrive document ID
>> *Return document json*

`remindDocument(int $docID, ?string $url = NULL)`  
> Send a reminder to signers: $docID scrive document ID
>> *Return true*

`prolongDocument(int $docID, int $days, ?string $url = NULL)`  
> Prolong signing period for a started document: $docID scrive document ID, $days days to prolong document
>> *Return document json*

`getDocument(int $docID, ?string $url = NULL)`  
> Get document metadata: $docID scrive document ID
>> *Return document json*

`documentCallback(int $docID, ?string $url = NULL)`  
> Tell document to make a webhook callback: $docID scrive document ID
>> *Return true if callback url is set for document*

`signer(array $signerare)`  
> Creates a signer array (parties)
> Example:
> $signerare = [  
    `"namn" => 'Name'`, //required  
    `"mail" => 'email'`, //required  
    `"tel" => 'phone number'`,  
    `"personnummer" => "personal number"`,  
    `"company" => "Company"`,  
    `"attachments" => array([subarray])`,  
    `"sign_order" => "sign order"`,  //numeric signing order
    `"authentications" => array([subarray])`,
];
>
>> *Return signer as array - to be included in update array['parties']*

`signerCompany(array $signerare)`  
> Creates a company signer array
> Example:
>$signerare = [  
    `"company" => 'name'`, //required  
    `"mail" => 'email'`, //required  
    `"tel" => 'phone number'`,  
    `"org" => "orgnumber"`,  
    `"namn" => "person name"`,  
    `"sign_order" => "sign order"`,  //numeric signing order  
    `"attachments" => array([subarray])`,  
    `"authentications" => array([subarray])`,  
];
>
>> *Return signer as array - to be included in update array['parties']*

`defaultViewer(int $docID)`  
> Get author for a document and change role to viewer: $docID scrive document ID
>> *Return author as array - to be included in update array['parties']*

`getAuthor(int $docID)`  
> Get author for document: $docID scrive document ID
>> *Return author as array - to be included in update array['parties']*

`listDocuments(?array $filter = NULL, ?array $sort = NULL, ?int $max = NULL, ?string $url = NULL)`  
> Get documents as list from Scrive  
> $filter must be an array with at least one of the following type(s):
[`"preparation"`,
`"awaiting_start"`,
`"pending"`,
`"closed"`,
`"canceled"`,
`"timedout"`,
`"rejected"`,
`"document_error"`,]
>
> $sort must be array with order and sort_by
>> [`'order' => "ascending" (Default) || "ascending",`  
`'sort_by' => "title" | "status" "mtime" "author",`]
>
> $max: max number of returned documents
>> *Return json ([total_matching] => , [documents] => Array())*

`changeMailPhone(int $docID, int $signerID, ?string $email = NULL, ?string $phone = NULL, ?string $url = NULL)`  
> Change email/phone for a signer on a started document: $docID scrive document ID, $signerID id for the specific signer that should be changed, $email new email-address that should be used, $phone new mobile number that should be used
>
> Email or/and phone must be set to update signer
>> *Return document json*

`setReminder(int $docID, ?int $days = NULL, ?string $url = NULL)`  
> Set days for automatic reminder document/remove reminder: $docID scrive document ID, $days include number to set reminder days, leave empty to remove reminder days
>> *Return document json*

`trashBatch(array $docIDs, ?string $url = NULL)`  
> Move one or more documents to Trash: $docIDs array with document id's to trash
>> *Return json ([total_matching] => , [documents] => Array())*

`deleteBatch(array $docIDs, ?string $url = NULL)`  
> Delete one or more trashed documents: $docIDs array with document id's to delete
>> *Return json ([total_matching] => , [documents] => Array())*

`setAPIcallback(int $docID, ?string $urlCallback = NULL)`  
> Set API callback (webhook callback url) for document: $docID scrive document ID, $urlCallback url for callback (if not provided callback url that were set with initScrive is attempted to be used )
>> *Return document json*

`changeAuthor(array $getAuthor, ?bool $signerare = false, ?bool $callback = false, ?int $sign_order = 0)`  
> Change author role to viewer input response parties array: $getAuthor parties array from earlier document json response i.e. `$apiResponse->parties`, $signerare`true` (is signer) | `false` (is viewer), $callback should author get notification as viewer `true | false`, $sign_order set signingorder for author
>> *Return author as array - to be included in update array['parties']*

`documentHistory(int $docID, ?string $url = NULL)`  
> Get document event history: $docID scrive document ID
>> *Return event json [events] =>*

`getAttachment(int $docID, int $attachmentID, ?string $filename = NULL, ?string $url = NULL)`  
> Get attachemnt as pdf file
>> *Return PDF* (use header('Content-type: application/pdf') in php file if using php to show content)

### authentications array

Signer authentications is used to configure signer access to the document, `standard` is default and no extra idenfication is set. Access is set on view document, sign document and view archived document.

Array example:  
"authentications" => [  
    `"view" => ["name" => "standard" || 'se_bankid' || 'freja']`,  
    `"sign" => ["name" => "standard" || 'se_bankid' || 'freja']`,  
    `"view_archived" => ["name" => "standard" || 'se_bankid' || 'freja']`,  
],

### Attachment array (signatory attachment)

Attachment array must contain one subarray per attachment (name and description is required):  
array(  
    [`'name' => 'Title / Name'`, `'description' => 'Decription'`,]  
)

---

## API callback (webhook callback)

If document is set with an `api_callback_url` url Scrive will send a webhook for for events `started`, `viewed`, `signed`, `reminded`, `callback`, `canceled`, `recalled` and `timedout` on a document.

The webhook is post type with the following keys;  
[document_id] => // numeric  
[document_signed_and_sealed] => boolean true|false  
[document_json] => document data in json format

---

## .htaccess and index.php

### .htaccess

Provided for webhook folder to redirect all pages/request to index.php but keeping the url pattern.

    RewriteEngine On  
    RewriteBase /  
    RewriteCond %{REQUEST_FILENAME} !-f  
    RewriteRule ^(.*)$ index.php [QSA,NC,L]`

### index.php

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

The later error_log will produce something like:  

    stdClass Object
        (
            [time] => **DateTime**
            [POST] => stdClass Object
                (
                    [document_id] => **document_id**
                    [document_signed_and_sealed] => **true|false**
                    [document_json] => **document data json**
                )

        )

If you don't want to use index.php edit the .htaccess file and change to a folder/file of your choise.

---

## Some document update parameters

    'days_to_sign' => int, //Numeric days to sign document
    'days_to_remind' => int, //Numeric days to sign document
    'invitation_message' => str, //String message for the invitation email
    'confirmation_message' => str, //String message for confirmation email when all parties have signed the document
    'api_callback_url' => str, //String url for webhook callback
    'parties' => array, //Array with all signer data

---

## Some parties parameters

All parties must be set as separate arrys within 'parties' update parameter.

    "is_author": false,
    "signatory_role": "signing_party",
    "fields": [
    {
        "type": "name",
        "order": 1,
        "value": str, // String First name
        "is_obligatory": true,
        "should_be_filled_by_sender": false,
        "placements": []
    },
    {
        "type": "name",
        "order": 2,
        "value": str, // String Surname
        "is_obligatory": true,
        "should_be_filled_by_sender": false,
        "placements": []
    },
    {
        "type": "email",
        "value": str, // String email
        "is_obligatory": false,
        "should_be_filled_by_sender": false,
        "editable_by_signatory": false,
        "placements": []
    },
    {
        "type": "mobile",
        "value": str, // String mobile number
        "is_obligatory": false,
        "should_be_filled_by_sender": false,
        "placements": []
    },
    {
        "type": "company",
        "value": str, // String company name
        "is_obligatory": false,
        "should_be_filled_by_sender": false,
        "placements": []
    },
    {
        "type": "company_number",
        "value": str, // String company number
        "is_obligatory": false,
        "should_be_filled_by_sender": false,
        "placements": []
    },
    {
        "type" => "personal_number",
        "value" => str, // String personal number
        "is_obligatory" => false,
        "should_be_filled_by_sender" => false,
        "editable_by_signatory" => false,
        "placements" => [],
    },
    ],
    "sign_order": int, // numeric sign order
    "authentications": {[]},

---

## Dates field from Scrive

All DateTime varibles that are sent from Scrive is in UTC time format
