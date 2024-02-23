<?php

namespace App\Repository;

use App\Entity\PromotedLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PromotedLink>
 *
 * @method PromotedLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromotedLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromotedLink[]    findAll()
 * @method PromotedLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotedLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromotedLink::class);
    }

//    /**
//     * @return PromotedLink[] Returns an array of PromotedLink objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PromotedLink
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
