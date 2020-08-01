<?php

namespace App\Service;


class Optad360Service
{
    private $optadApiBaseUri;

    private $optadApiKey;

    private $apiClientService;

    private $clientService;

    public function __construct(ApiClientService $apiClientService, string $optadApiBaseUri, string $optadApiKey, ClientService $clientService)
    {
        $this->apiClientService = $apiClientService;
        $this->optadApiKey = $optadApiKey;
        $this->optadApiBaseUri = $optadApiBaseUri;
        $this->clientService = $clientService;
    }

    public function handleGetCommand(array $params)
    {
        $queryArray =
            [
                'headers' => [
                    'accept' => 'application/json',
                    'accept-charset' => 'utf-8',
            ],
                'query' => [
                    'key' => $this->optadApiKey,
                ]
            ];

        for($i=3; $i<count($params); $i++){
            $temp = explode( '=', $params['param'.($i-2)]);
            $queryArray['query']+=[$temp[0] => $temp[1]];
        }

        $response = $this->apiClientService->getApiResponse(strtoupper($params['method']), $queryArray,
                                            $this->optadApiBaseUri.strtolower($params['url']));

        if(!array_key_exists('errors' , $response) && !array_key_exists('msg', $response)){
            $result = $this->clientService->compute($response);
            return $result;
        }
        return $response;
    }

    public function handlePostCommand(){/*TODO if needed*/}
    public function handlePutCommand(){/*TODO if needed*/}
    public function handleDeleteCommand(){/*TODO if needed*/}
}