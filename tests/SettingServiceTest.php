<?php

namespace App\Tests;


use App\Entity\Setting;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SettingServiceTest extends KernelTestCase
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

    public function testCheckIfSettingExists()
    {
        $setting = new Setting();
        $setting->setCurrency('PLN');

        $settingArrayExisting = ['currency' => 'EUR'];
        $settingArrayNonExisting = ['currency' => 'PLN'];

        $settingInDb = $this->em->getRepository(Setting::class)->findOneBy(['currency' => 'EUR']);

        $settingService = $this->getMockBuilder(SettingService::class)
            ->setConstructorArgs([ $this->em->getRepository(\App\Entity\Setting::class), $this->em, $this->validator, $this->logger])
            ->setMethods(['insertSetting'])
            ->getMock();
        $settingService->method('insertSetting')
            ->willReturn($setting);

        $this->assertEquals($settingInDb, $settingService->checkIfSettingExists($settingArrayExisting));
        $this->assertEquals($setting, $settingService->checkIfSettingExists($settingArrayNonExisting));
    }

    public function testInsertSetting()
    {
        $settingService = $this->getMockBuilder(SettingService::class)
            ->setMethods(['none'])
            ->setConstructorArgs([ $this->em->getRepository(Setting::class), $this->em, $this->validator, $this->logger])
            ->getMock();

        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'); //
        $rand = '';
        foreach (array_rand($seed, 3) as $k) $rand .= $seed[$k];

        $setting = new Setting();
        $setting->setCurrency($rand);
        $settingArray = ['currency' => $rand];

        $this->assertEquals($setting, $settingService->insertSetting($settingArray));

        $this->expectException(ValidatorException::class);
        $settingService->insertSetting([]);
        $settingService->insertSetting(['currency' => 'pln' ]);
    }

}