<?php

namespace App\Tests;

use App\Entity\Site;
use App\Service\SiteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SiteServiceTest extends KernelTestCase
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

    public function setUp()
    {
        self::bootKernel();
        $this->em  =  static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->validator = static::$kernel->getContainer()->get('validator');
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testCheckIfSiteExists()
    {
        $siteString = 'mastercuriosidadesbr.com';
        $site = new Site();
        $site->setUrl('TestSite');
        $siteInDb = $this->em->getRepository(Site::class)->findOneBy(['url' => $siteString]);

        $siteService = $this->getMockBuilder(SiteService::class)
            ->setConstructorArgs([$this->em, $this->validator, $this->em->getRepository(Site::class), $this->logger])
            ->setMethods(['insertSite'])
            ->getMock();
        $siteService->method('insertSite')
            ->willReturn($site);

        $this->assertEquals($siteInDb, $siteService->checkIfSiteExists($siteString));
        $this->assertEquals($site, $siteService->checkIfSiteExists('TestSite'));
    }

    public function testInsertSite()
    {
        $siteService = $this->getMockBuilder(SiteService::class)
            ->setMethods(['none'])
            ->setConstructorArgs([$this->em, $this->validator, $this->em->getRepository(Site::class),  $this->logger])
            ->getMock();

        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'); //
        $rand = '';
        foreach (array_rand($seed, 5) as $k) $rand .= $seed[$k];

        $site = new Site();
        $site->setUrl($rand);
        $this->assertEquals($site, $siteService->insertSite($rand));

        $this->expectException(ValidatorException::class);
        $siteService->insertSite('');
    }

}