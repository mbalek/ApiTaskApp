<?php
/**
 * Created by PhpStorm.
 * User: Ikki
 * Date: 31.07.2020
 * Time: 09:08
 */

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClientService
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param string $method
     * @param array $params
     * @param string $url
     * @return array
     */
    public function getApiResponse(string $method, array $params, string $url): array
    {
        try{
            $response = $this->client->request($method, $url, $params);
            if($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299)
                return $response->toArray();

            print_r(['statusCode' => $response->getStatusCode(), 'errors' => $response->getContent(false)]);
            return ['statusCode' => $response->getStatusCode(), 'errors' => $response->getContent(false)];
        } catch(ExceptionInterface $e){
            print_r($e->getMessage());
            $this->logger->error( $e->getMessage());
        }
        return [];
    }

}