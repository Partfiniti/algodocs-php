<?php

require('./vendor/autoload.php');

use AlgoDocs\AlgoDocs;

test_authenticate();
//test_getExtractors();
//test_getFolders();
//test_uploadDocumentLocal();
//test_uploadDocumentUrl();
//test_uploadDocumentBase64();
//test_getExtractedDataByDocumentID();
//test_getExtractedDataByExtractorID();

function getClient()
{
    //create AlgoDocs client by passing your API Key as parameter...
    //get your API Key here: https://app.algodocs.com/restapi
    return new AlgoDocs('YOUR_SECRET_API_KEY');
}

function test_authenticate()
{
    $algodocs_client = getClient();
    //test authentication by getting credentials...
    echo '<b>Credentials...</b><br/><br/>';
    $credentials = $algodocs_client->me();
    echo $credentials['fullName'] . '<br/>';
    echo $credentials['email'] . '<br/>';
}

function test_getExtractors()
{
    $algodocs_client = getClient();
    //get all extractors...
    echo '<br/><b>Extractors...</b><br/><br/>';
    $extractors = $algodocs_client->getExtractors();
    foreach ( $extractors as $extractor ) {
        echo '<b>id</b> = '.$extractor['id'] . ' ';
        echo '<b>name</b> = '.$extractor['name'] . '<br/>';
    }
}

function test_getFolders()
{
    $algodocs_client = getClient();
    //get all folders...
    echo '<br/><b>Folders...</b><br/><br/>';
    $folders = $algodocs_client->getFolders();
    foreach ( $folders as $folder ) {
        echo '<b>id</b> = '.$folder['id'] . ' ';
        echo '<b>name</b> = '.$folder['name'] . '<br/>';
    }
}

function test_uploadDocumentLocal()
{
    $algodocs_client = getClient();
    $extractors = $algodocs_client->getExtractors();
    if(count($extractors) > 0)
    {
        $folders = $algodocs_client->getFolders();
        //As an example here we will upload a document to the first available folder (root) and first created extractor... 
        echo '<br/>Upload file to folder: ' . $folders[0]['name'];
        echo '<br/>Use extractor: ' . $extractors[0]['name'];

        $folder_id = $folders[0]['id'];
        $extractor_id = $extractors[0]['id'];
        $file_path = "path_to_your_document.pdf";

        //upload file from local drive...
        echo '<br/><b>Upload file from local drive...</b><br/>';
        $response = $algodocs_client->uploadDocumentLocal($extractor_id, $folder_id, $file_path);
        echo '<b>Response:</b>';
        echo '<br/><b>document_id:</b> ' . $response['id']; //this id will be used later for fetching extracted data...
        echo '<br/><b>fileSize:</b> ' . $response['fileSize'] . ' bytes';
        echo '<br/><b>fileMD5CheckSum:</b> ' . $response['fileMD5CheckSum'];
        echo '<br/><b>uploadedAt:</b> ' . $response['uploadedAt'];
    }
    else
    {
        echo "No extractor found! First create an extractor.";
    }
}

function test_uploadDocumentUrl()
{
    $algodocs_client = getClient();
    $extractors = $algodocs_client->getExtractors();
    if(count($extractors) > 0)
    {
        $folders = $algodocs_client->getFolders();
        //As an example here we will upload a document to the first available folder (root) and first created extractor... 
        echo '<br/>Upload file to folder: ' . $folders[0]['name'];
        echo '<br/>Use extractor: ' . $extractors[0]['name'];

        $folder_id = $folders[0]['id'];
        $extractor_id = $extractors[0]['id'];

        //upload file from url...
        echo '<br/><br/><b>Upload file from url...</b><br/>';
        $url = "https://api.algodocs.com/content/SampleInvoice.pdf";
        $response = $algodocs_client->uploadDocumentUrl($extractor_id, $folder_id, $url);
        echo '<b>Response:</b>';
        echo '<br/><b>document_id:</b> ' . $response['id']; //this id will be used later for fetching extracted data...
        echo '<br/><b>fileSize:</b> ' . $response['fileSize'] . ' bytes';
        echo '<br/><b>fileMD5CheckSum:</b> ' . $response['fileMD5CheckSum'];
        echo '<br/><b>uploadedAt:</b> ' . $response['uploadedAt'];
    }
    else
    {
        echo "No extractor found! First create an extractor.";
    }
}

function test_uploadDocumentBase64()
{
    $algodocs_client = getClient();
    $extractors = $algodocs_client->getExtractors();
    if(count($extractors) > 0)
    {
        $folders = $algodocs_client->getFolders();
        //As an example here we will upload a document to the first available folder (root) and first created extractor... 
        echo '<br/>Upload file to folder: ' . $folders[0]['name'];
        echo '<br/>Use extractor: ' . $extractors[0]['name'];

        $folder_id = $folders[0]['id'];
        $extractor_id = $extractors[0]['id'];

        //upload file with base64...
        echo '<br/><br/><b>Upload file with base64...</b><br/>';
        $file_path = "path_to_your_document.pdf";
        $file_contents = file_get_contents($file_path);          
        $data = base64_encode($file_contents);
        $response = $algodocs_client->uploadDocumentBase64($extractor_id, $folder_id, $data, basename($file_path));
        echo '<b>Response:</b>';
        echo '<br/><b>document_id:</b> ' . $response['id']; //this id will be used later for fetching extracted data...
        echo '<br/><b>fileSize:</b> ' . $response['fileSize'] . ' bytes';
        echo '<br/><b>fileMD5CheckSum:</b> ' . $response['fileMD5CheckSum'];
        echo '<br/><b>uploadedAt:</b> ' . $response['uploadedAt'];
    }
    else
    {
        echo "No extractor found! First create an extractor.";
    }
}

function test_getExtractedDataByDocumentID()
{
    $document_id = 263520;//this document_id comes from $response['id'], so use your actual document_id that you received in response object after importing the document to AlgoDocs...
    $algodocs_client = getClient();
    echo '<br/><b>Extracted data by document id...</b><br/><br/>';
    $extracted_data = $algodocs_client->getExtractedDataByDocumentID($document_id);
    echo json_encode($extracted_data);
}

function test_getExtractedDataByExtractorID()
{
    $algodocs_client = getClient();
    $extractors = $algodocs_client->getExtractors();
    if(count($extractors) > 0)
    {
        $extractor_id = $extractors[0]['id'];
        /*
        $options = [
            "folder_id" => "<folder_id>",
            "date" => new DateTime('01 June 2022'),
            "limit" => 10
        ];
        */
        echo '<br/><b>Extracted data of multiple documents by extractor id...</b><br/><br/>';
        $extracted_data = $algodocs_client->getExtractedDataByExtractorID($extractor_id, /*$options*/);
        echo json_encode($extracted_data);
    }
}