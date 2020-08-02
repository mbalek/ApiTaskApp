<?php

namespace App\Service;


use App\Entity\Client;
use App\Entity\Setting;
use App\Entity\Site;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Exception\ExceptionInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientService implements ApiServiceInterface
{
    /**
     * @var ClientRepository
     */
    private $clientRepo;

    /**
     * @var TagService
     */
    private $tagService;

    /**
     * @var SiteService
     */
    private $siteService;

    /**
     * @var SettingService
     */
    private $settingService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ClientRepository $clientRepo, TagService $tagService, EntityManagerInterface $em,
                                SiteService $siteService, SettingService $settingService, ValidatorInterface $validator,
                                LoggerInterface $logger)
    {
        $this->clientRepo = $clientRepo;
        $this->tagService = $tagService;
        $this->siteService = $siteService;
        $this->settingService = $settingService;
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @param array $response
     * @return bool|string
     * @throws \Exception
     */
    public function compute(array $response)
    {
        if(!array_key_exists('data',$response) || !array_key_exists('settings' , $response))
            throw new ValidatorException('wrong array keys in response');

        $clients = $response['data'];
        $setting = $response['settings'];

        try{
        $sett = $this->getClientSetting($setting);

        if(!$sett instanceof Setting)
            return $errorMsg = $sett."\nYour called operation failed";

        foreach($clients as $client)
        {
            $site = $this->getClientUrl($client[0]);
            $tags = $this->getClientTags($client[1]);
            $this->checkIfClientExists($site, $tags, $client, $sett);

            $this->em->flush();
        }


        return true;

        } catch (ExceptionInterface $exception){
            return $errorMsg = $exception->getMessage();
        }
    }

    /**
     * @param Site $site
     * @param array $tags
     * @param array $clientJson
     * @param Setting $sett
     * @return Client|mixed|null
     * @throws \Exception
     */
    public function checkIfClientExists(Site $site, array $tags, array $clientJson, Setting $sett)
    {
        try {
            $client = $this->clientRepo->findClient($site, $tags, $clientJson, $sett);
        } catch (NonUniqueResultException $exception){
            $client = null;
        }

        if(null === $client)
            $client = $this->insertClient($site, $tags, $clientJson, $sett);

        return $client;
    }

    /**
     * @param Site $site
     * @param array $tags
     * @param array $clientJson
     * @param Setting $sett
     * @return Client
     * @throws \Exception
     */
    public function insertClient(Site $site, array $tags, array $clientJson, Setting $sett): Client
    {
        $client = new Client();
        try{
            $client->setDate(new \DateTime($clientJson[2]));
            $client->setEstimatedRevenue($clientJson[3]);
            $client->setAdImpressions($clientJson[4]);
            $client->setAdEcpm($clientJson[5]);
            $client->setClicks($clientJson[6]);
            $client->setAdCtr($clientJson[7]);
            $client->setSetting($sett);
            $client->setSite($site);
            foreach($tags as $tag){
                $client->addTag($tag);
            }
        }catch (\TypeError $e){
            throw new \TypeError('Wrong parameter for entity. \n'.$e->getMessage());
        }

        $errors = $this->validator->validate($client);
        if(count($errors) > 0){
            $errorString = (string) $errors;
            $this->logger->warning('Failed to create Client entity cause of validation errors below');
            $this->logger->warning($errorString);

            throw new ValidatorException('Failed to create Setting entity \n'.$errorString);
        }

        $this->em->persist($client);

        $this->logger->info('Creation of entity Setting successful');

        return $client;
    }

    /**
     * @param string $tags
     * @return array
     */
    protected function getClientTags(string $tags): array
    {
        return $this->tagService->checkIfTagsExists($tags);
    }

    /**
     * @param string $url
     * @return \App\Entity\Site|string
     */
    protected function getClientUrl(string $url)
    {
        return $this->siteService->checkIfSiteExists($url);
    }


    /**
     * @param array $setting
     * @return Setting|string
     */
    protected function getClientSetting(array $setting)
    {
        return $setting = $this->settingService->checkIfSettingExists($setting);
    }

}