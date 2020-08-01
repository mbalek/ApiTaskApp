<?php

namespace App\Controller;

use App\Service\ApiClientService;
use App\Service\ClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    private $optadApiBaseUri;

    private $optadApiKey;

    public function __construct(string $optadApiBaseUri, string $optadApiKey)
    {
        $this->optadApiKey = $optadApiKey;
        $this->optadApiBaseUri = $optadApiBaseUri;
    }

    /**
     * @Route("/test/cokolwiek", name="index")
     */
    public function index(ApiClientService $apiClientService, ClientService $clientService)
    {
       $response = $apiClientService->getApiResponse('GET',
            ['headers' => [
                'accept' => 'application/json',
                //'accept-charset' => 'utf-8',
              ],
             'query' => [
                 'key' => $this->optadApiKey,
                 'startDate' => '2019-05-14',
                 'endDate' => '2020-12-15',
                ]],
            $this->optadApiBaseUri.'get');
       if(!array_key_exists('errors' , $response)){
           $clientService->computeClients($response['data'], $response['settings']);
       }

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
