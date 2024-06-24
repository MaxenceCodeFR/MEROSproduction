<?php

namespace App\Repository;

use App\Entity\ContactInfluencer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactInfluencer>
 *
 * @method ContactInfluencer|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactInfluencer|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactInfluencer[]    findAll()
 * @method ContactInfluencer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactInfluencerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactInfluencer::class);
    }

    public function findAllCandidatesByNewest()
    {
        return $this->createQueryBuilder('notif')
            ->orderBy('notif.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllCandidatesByNewestLimit()
    {
        return $this->createQueryBuilder('notif')
            ->orderBy('notif.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function findCandidate($motif)
    {
        return $this->createQueryBuilder('ci')
            ->innerJoin('ci.motif', 'm')
            ->andWhere('m.id = :motif')
            ->setParameter('motif', $motif)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return ContactInfluencer[] Returns an array of ContactInfluencer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ContactInfluencer
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
