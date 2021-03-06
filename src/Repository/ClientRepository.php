<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Setting;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @param Site $site
     * @param array $tags
     * @param array $clientJson
     * @param Setting $setting
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function findClient(Site $site, array $tags, array $clientJson, Setting $setting )
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->join('c.setting' , 'sett')
            ->join('c.site' , 'site')
            ->andWhere('c.date = :date')
            ->andWhere('sett.currency = :curr')
            ->setParameter(':date', new \DateTime($clientJson[2]))
            ->setParameter(':curr', $setting->getCurrency());

        if((new \DateTime())->format('Y-m-d') > (new \DateTime($clientJson[2]))->format('Y-m-d')){
        $qb
            ->andWhere('c.estimatedRevenue = :revenue')
            ->andWhere('c.adImpressions = :impressions')
            ->andWhere('c.adEcpm = :adEcpm')
            ->andWhere('c.clicks = :clicks')
            ->andWhere('c.adCtr = :adCtr')
            ->setParameter(':revenue', $clientJson[3])
            ->setParameter(':impressions', $clientJson[4])
            ->setParameter(':adEcpm', $clientJson[5])
            ->setParameter(':clicks', $clientJson[6])
            ->setParameter(':adCtr', $clientJson[7]);
        }

        $qb->andWhere('site.url = :url')
            ->setParameter(':url' , $site->getUrl());

        for($i=0;$i<count($tags);$i++){
            $qb ->join('c.tags' , 't'.$i)
                ->andWhere('t'.$i.'.name = :name_'.$i)
                ->setParameter(':name_'.$i , $tags[$i]->getName());
        }

        return $qb->getQuery()
            ->getOneOrNullResult();
    }
}
