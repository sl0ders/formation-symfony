<?php

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    public function findBestAds($limit){
        return $this->createQueryBuilder('a') //je crée les annonce
            ->select('a as annonce, AVG(c.rating) as avgRating') // je selectionne les annonce en precisant leurs alias, la moyenne des note AVG
            ->join('a.comments', 'c') //je crée la jointure entre les annonce (a) et les commentaires (c) donc a.comments ce sera (c)
            ->groupBy('a') // je groupe le resultat par annonce
            ->orderBy('avgRating', 'DESC') // et je les trie par note du plus grand au plus petit
            ->setMaxResults($limit) //je donne la limit max de resultat que je souhaite
            ->getQuery() //et je demande le resultat
            ->getResult();
    }

    // /**
    //  * @return Ad[] Returns an array of Ad objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ad
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
