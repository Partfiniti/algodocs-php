<?php
namespace AlgoDocs;

class AlgoDocs
{
    const API_BASE_URL = 'https://api.algodocs.com/v1/';
    private $apiKey;

    /**
     * AlgoDocs constructor.
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * this method is used for testing authentication.
     */
    public function me()
    {
        $request = new AlgoDocsApiRequest(self::API_BASE_URL, $this->apiKey);

        return $request->makeGetRequest('me');
    }

    /**
     * retrieves all extractors
     */
    public function getExtractors()
    {
        $request = new AlgoDocsApiRequest(self::API_BASE_URL, $this->apiKey);
        $response = $request->makeGetRequest('extractors');

        return $response;
    }

    /**
     * retrieves all folders
     */
    public function getFolders()
    {
        $request = new AlgoDocsApiRequest(self::API_BASE_URL, $this->apiKey);
        $response = $request->makeGetRequest('folders');

        return $response;
    }

    /**
     * uploads documents from a given file path
     *
     * @api
     * @param $extractor_id
     * @param $folder_id
     * @param $file_path
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws AlgoDocsApiException
     */
    public function uploadDocumentLocal($extractor_id, $folder_id, $file_path)
    {
        if (!file_exists($file_path)) {
            throw new AlgoDocsApiException("No such file.");
        }

        if (is_dir($file_path)) {
            throw new AlgoDocsApiException("Passed a directory, expected file.");
        }

        return $this->uploadDocumentByContents($extractor_id, $folder_id, fopen($file_path, 'r'), basename($file_path));
    }

    /**
     * uploads document by content or file handle
     *
     * @api
     * @param $extractor_id
     * @param $folder_id
     * @param $file
     * @param null $filename
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws AlgoDocsApiException
     */
    public function uploadDocumentByContents($extractor_id, $folder_id, $file, $filename)
    {
        if (empty($file)) {
            throw new AlgoDocsApiException("File content is empty");
        }

        $request = new AlgoDocsApiRequest(self::API_BASE_URL, $this->apiKey);
        $endpoint = 'document/upload_local/' . $extractor_id . '/' . $folder_id;

        $response = $request->uploadDocument($endpoint, $file, $filename);

        return $response;
    }

    /**
     * uploads a document from a publicly accessible URL
     *
     * @api
     * @param $extractor_id
     * @param $folder_id
     * @param $url
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function uploadDocumentURL($extractor_id, $folder_id, $url)
    {
        $request = new AlgoDocsApiRequest(self::API_BASE_URL, $this->apiKey);
        $endpoint = 'document/upload_url/' . $extractor_id . '/' . $folder_id;

        $response = $request->makePostRequest($endpoint, [
            'url' => $url
        ]);

        return $response;
    }

    /**
     * uploads a base64 encoded document
     * @api
     * @param $extractor_id
     * @param $folder_id
     * @param $file_base64
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function uploadDocumentBase64($extractor_id, $folder_id, $file_base64, $filename)
    {
        $request = new AlgoDocsApiRequest(self::API_BASE_URL, $this->apiKey);
        $endpoint = 'document/upload_base64/' . $extractor_id . '/' . $folder_id;

        $response = $request->makePostRequest($endpoint, [
            'file_base64' => $file_base64,
            'filename' => $filename
        ]);

        return $response;
    }
    
    /**
     * retrieves extracted data of a single document by document id
     * @api
     * @param $document_id
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function getExtractedDataByDocumentID($document_id)
    {
        $request = new AlgoDocsApiRequest(self::API_BASE_URL, $this->apiKey);
        $endpoint = 'extracted_data/' . $document_id;

        $response = $request->makeGetRequest($endpoint);

        return $response;
    }

    /**
     * retrieves extracted data of multiple documents by extractor id
     * 
     * @param $extractor_id
     * @param array $options
     * options contains the keys:
     * folder_id, date, limit
     *
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function getExtractedDataByExtractorID($extractor_id, $options = [])
    {
        $folder_id = (isset($options['folder_id'])) ? $options['folder_id'] : null;
        $limit = (isset($options['limit'])) ? $options['limit'] : 100; //max is 10000...
        $date = (isset($options['date'])) ? $options['date']->format('c') : null;
        
        $request = new AlgoDocsApiRequest(self::API_BASE_URL, $this->apiKey);
        $endpoint = 'extracted_data/' . $extractor_id;

        $response = $request->makeGetRequest($endpoint, [
            'folder_id' => $folder_id,
            'limit' => $limit,
            'date' => $date
        ]);

        return $response;
    }
}
