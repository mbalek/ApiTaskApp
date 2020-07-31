<?php
/**
 * Created by PhpStorm.
 * User: Ikki
 * Date: 31.07.2020
 * Time: 13:38
 */

namespace App\Service;


use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SiteService
{
    private $siteRepo;

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

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, SiteRepository $siteRepo,
                                LoggerInterface $logger)
    {
        $this->siteRepo = $siteRepo;
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    public function checkIfSiteExists(string $url)
    {
        $site = $this->siteRepo->findOneBy(['url' => $url]);

        if(null === $site)
            $site = $this->insertSite($url);

        return $site;
    }

    public function insertSite(string $url)
    {
        $site = new Site();
        $site->setUrl($url);

        $errors = $this->validator->validate($site);
        if(count($errors) > 0){
            $errorString = (string) $errors;
            $this->logger->warning('Failed to create Setting entity cause of validation errors below');
            $this->logger->warning($errorString);

            throw new ValidatorException('Failed to create Setting entity \n'.$errorString);
        }

        $this->em->persist($site);

        $this->logger->info('Creation of entity Setting successful');

        return $site;
    }

}