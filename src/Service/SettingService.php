<?php

namespace App\Service;


use App\Entity\Setting;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SettingService
{
    /**
     * @var SettingRepository
     */
    private $settingRepo;

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

    public function __construct(SettingRepository $settingRepo, EntityManagerInterface $em, ValidatorInterface $validator,
                                LoggerInterface $logger)
    {
        $this->settingRepo = $settingRepo;
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @param array $setting
     * @return Setting|null
     */
    public function checkIfSettingExists(array $setting):?Setting
    {
        $sett = $this->settingRepo->findOneBy(['currency' => $setting['currency'], 'periodLength' => $setting['PeriodLength']]);

        if(null === $sett)
            $sett = $this->insertSetting($setting);

       return $sett;
    }

    /**
     * @param array $setting
     * @return Setting
     */
    public function insertSetting(array $setting):Setting
    {
        if(empty($setting) || !array_key_exists('currency', $setting) || !array_key_exists('PeriodLength', $setting)
            || !array_key_exists('groupby', $setting))
            throw new ValidatorException('Failed to create Setting entity , param array isnt correct\n');

        $sett = new Setting();
        $sett->setCurrency($setting['currency']);
        $sett->setPeriodLength($setting['PeriodLength']);
        $sett->setGroupBy($setting['groupby']);

        $errors = $this->validator->validate($sett);
        if(count($errors) > 0){
            $errorString = (string) $errors;
            $this->logger->warning('Failed to create Setting entity cause of validation errors below');
            $this->logger->warning($errorString);

            throw new ValidatorException('Failed to create Setting entity \n'.$errorString);
        }

        $this->em->persist($sett);

        $this->logger->info('Creation of entity Setting successful');

        return $sett;
    }

}