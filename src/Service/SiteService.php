<?php

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

    /**
     * @param string $url
     * @return Site|null
     */
    public function checkIfSiteExists(string $url):?Site
    {
        $site = $this->siteRepo->findOneBy(['url' => $url]);

        if(null === $site)
            $site = $this->insertSite($url);

        return $site;
    }

    /**
     * @param string $url
     * @return Site
     */
    public function insertSite(string $url):Site
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