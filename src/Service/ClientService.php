<?php
/**
 * Created by PhpStorm.
 * User: Ikki
 * Date: 31.07.2020
 * Time: 13:42
 */

namespace App\Service;


use App\Entity\Setting;
use App\Entity\Site;
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
            $site = $this->getClientUrl(/*$client[0]*/ '');
            $tags = $this->getClientTags($client[1]);

            $this->em->flush();
        }


        return true;

        } catch (ExceptionInterface $exception){
            return $errorMsg = $exception->getMessage();
        }
    }

    protected function checkIfClientExists()
    {}

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