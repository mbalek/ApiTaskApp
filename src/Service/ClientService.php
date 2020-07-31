<?php
/**
 * Created by PhpStorm.
 * User: Ikki
 * Date: 31.07.2020
 * Time: 13:42
 */

namespace App\Service;


use App\Entity\Client;
use App\Entity\Setting;
use App\Entity\Site;
use App\Entity\Tag;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\ExceptionInterface;

class ClientService
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

    public function __construct(ClientRepository $clientRepo, TagService $tagService, EntityManagerInterface $em,
                                 SiteService $siteService, SettingService $settingService)
    {
        $this->clientRepo = $clientRepo;
        $this->tagService = $tagService;
        $this->siteService = $siteService;
        $this->settingService = $settingService;
        $this->em = $em;
    }

    public function computeClients(array $clients, array $setting)
    {
        try{
        $sett = $this->getClientSetting($setting);

        if(!$sett instanceof Setting)
            return $errorMsg = $sett."\nYour called operation failed";

        foreach($clients as $client)
        {
            $site = $this->getClientUrl($client[0]);
            $tags = $this->getClientTags($client[1]);

            $this->em->flush();
        }


        return true;

        } catch (ExceptionInterface $exception){
            return $errorMsg = $exception->getMessage();
        }
    }

    protected function checkIfClientExists(Site $site, array $tags, array $clientJson, Setting $sett)
    {
        $client = $this->clientRepo->findOneBy(['date' => $clientJson[2], "estimatedRevenue" => $clientJson[3],
                    "adImpressions" => $clientJson[4], "clicks" => $clientJson[6]]);

        //TODO dorobic zapytanie do repo do wyszukania danego clienta z site,tags
        //TODO dokonczyc insertClient i checkIfClientExists
        //TODO zrobic komende do konsoli dla wywolania zapytania do api a nastepnie case ktory rozdzieli to do odpowiedniego service
        //TODO zrobic service optadService ktory zajmie sie handlowaniem komendy


    }

    protected function insertClient(Site $site, array $tags, array $clientJson, Setting $sett)
    {
        $client = new Client();
        $client->setDate($clientJson[2]);
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