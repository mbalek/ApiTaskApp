<?php

namespace App\Tests;


use App\Entity\Client;
use App\Entity\Setting;
use App\Entity\Site;
use App\Entity\Tag;
use App\Service\ClientService;
use App\Service\SettingService;
use App\Service\SiteService;
use App\Service\TagService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientServiceTest extends KernelTestCase
{
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

    private $tagService;

    private $siteService;

    private $settingService;

    public function setUp()
    {
        self::bootKernel();
        $this->em  =  static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->validator = static::$kernel->getContainer()->get('validator');
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->tagService = $this->createMock(TagService::class);
        $this->siteService = $this->createMock(SiteService::class);
        $this->settingService = $this->createMock(SettingService::class);
    }

    public function testCompute()
    {
        $site = new Site();
        $site->setUrl('mastercuriosidadesbr.com');

        $tags = [];
        $tag1 = new Tag();
        $tag1->setName('mastercuriosidadesbr.com');
        array_push($tags, $tag1);
        $tag2 = new Tag();
        $tag2->setName('mastercuriosidadesbr.com_S1-static');
        array_push($tags, $tag2);

        $setting = new Setting();
        $setting->setCurrency('EUR');

        $clientJson = [
            2 => '2020-05-01',
            3 => 246.62,
            4 => 32419,
            5 => 7.61,
            6 => 14378,
            7 => 44.35
        ];

        $client = new Client();
        $client->setDate(new \DateTime($clientJson[2]));
        $client->setEstimatedRevenue($clientJson[3]);
        $client->setAdImpressions($clientJson[4]);
        $client->setAdEcpm($clientJson[5]);
        $client->setClicks($clientJson[6]);
        $client->setAdCtr($clientJson[7]);
        $client->setSetting($setting);
        $client->setSite($site);
        foreach($tags as $tag){
            $client->addTag($tag);
        }

        $response = [
            'settings' => [
                'currency' => 'EUR',
                'PeriodLength' => 10,
                'groupby' => ''
            ],
            'data' => [
                [
                    0 => 'mastercuriosidadesbr.com',
                    1 => 'mastercuriosidadesbr.com \u00c2\u00bb mastercuriosidadesbr.com_S1-static',
                    2 => '2020-05-02',
                    3 => 257.28,
                    4 => 32798,
                    5 => 7.84,
                    6 => 14505,
                    7 => 44.23
                ]
            ]
        ];

        $clientService = $this->getMockBuilder(ClientService::class)
            ->setMethods(['getClientSetting' , 'getClientUrl' , 'getClientTags' , 'checkIfClientExists', 'insertClient'])
            ->setConstructorArgs([ $this->em->getRepository(Client::class), $this->tagService, $this->em,
                $this->siteService, $this->settingService, $this->validator, $this->logger])
            ->getMock();
        $clientService->method('checkIfClientExists')
            ->willReturn($client);
        $clientService->method('insertClient')
            ->willReturn($client);
        $clientService->method('getClientTags')
            ->willReturn($tags);
        $clientService->method('getClientUrl')
            ->willReturn($site);
        $clientService->method('getClientSetting')
            ->willReturn($setting);

        $this->assertEquals(true, $clientService->compute($response));

        $this->expectException(ValidatorException::class);
        $clientService->compute(['']);
    }

    public function testCheckIfClientExists()
    {
        $site = new Site();
        $site->setUrl('mastercuriosidadesbr.com');

        $tags = [];
        $tag1 = new Tag();
        $tag1->setName('mastercuriosidadesbr.com');
        array_push($tags, $tag1);
        $tag2 = new Tag();
        $tag2->setName('mastercuriosidadesbr.com_S1-static');
        array_push($tags, $tag2);

        $setting = new Setting();
        $setting->setCurrency('EUR');

        $clientJson = [
            2 => '2020-05-01',
            3 => 246.62,
            4 => 32419,
            5 => 7.61,
            6 => 14378,
            7 => 44.35
        ];

        $clientInDB = $this->em->getRepository(Client::class)->findClient($site, $tags, $clientJson, $setting);

        $clientService = $this->getMockBuilder(ClientService::class)
            ->setMethods(['insertClient'])
            ->setConstructorArgs([ $this->em->getRepository(Client::class), $this->tagService, $this->em,
                $this->siteService, $this->settingService, $this->validator, $this->logger])
            ->getMock();


        $this->assertEquals($clientInDB, $clientService->checkIfClientExists($site, $tags, $clientJson, $setting));

        $clientJson[7] = 12341;

        $client = new Client();
        $client->setDate(new \DateTime($clientJson[2]));
        $client->setEstimatedRevenue($clientJson[3]);
        $client->setAdImpressions($clientJson[4]);
        $client->setAdEcpm($clientJson[5]);
        $client->setClicks($clientJson[6]);
        $client->setAdCtr($clientJson[7]);
        $client->setSetting($setting);
        $client->setSite($site);
        foreach($tags as $tag){
            $client->addTag($tag);
        }

        $clientService->method('insertClient')
            ->willReturn($client);

        $this->assertEquals($client, $clientService->checkIfClientExists($site, $tags, $clientJson, $setting));
    }

    public function testInsertClient()
    {
        $site = new Site();
        $site->setUrl('www.adsadsadsa.com');

        $tags = [];
        $tag1 = new Tag();
        $tag1->setName('jakistag');
        array_push($tags, $tag1);
        $tag2 = new Tag();
        $tag2->setName('jakistag2');
        array_push($tags, $tag2);

        $setting = new Setting();
        $setting->setCurrency('PLN');

        $clientJsonCorrect = [
            2 => '2020-08-03',
            3 => 123.02,
            4 => 23,
            5 => 212.12,
            6 => 321,
            7 => 321.23
        ];

        $clientJsonIncorrect = [
            2 => '2020-08-03',
            3 => 123.02,
            4 => 23.02,
            5 => 'dsa',
            6 => 'ad',
            7 => 321.232123
        ];

        $client = new Client();
        $client->setDate(new \DateTime($clientJsonCorrect[2]));
        $client->setEstimatedRevenue($clientJsonCorrect[3]);
        $client->setAdImpressions($clientJsonCorrect[4]);
        $client->setAdEcpm($clientJsonCorrect[5]);
        $client->setClicks($clientJsonCorrect[6]);
        $client->setAdCtr($clientJsonCorrect[7]);
        $client->setSetting($setting);
        $client->setSite($site);
        foreach($tags as $tag){
            $client->addTag($tag);
        }

        $clientService = $this->getMockBuilder(ClientService::class)
            ->setMethods(['none'])
            ->setConstructorArgs([ $this->em->getRepository(Client::class), $this->tagService, $this->em,
                                $this->siteService, $this->settingService, $this->validator, $this->logger])
            ->getMock();

        $this->assertEquals($client, $clientService->insertClient($site, $tags, $clientJsonCorrect, $setting));

        $this->expectException(\TypeError::class);
        $clientService->insertClient($site, $tags,$clientJsonIncorrect, $setting);

    }
}