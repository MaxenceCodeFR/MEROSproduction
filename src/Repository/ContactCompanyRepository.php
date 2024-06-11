<?php

namespace App\Repository;

use App\Entity\ContactCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactCompany>
 *
 * @method ContactCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactCompany[]    findAll()
 * @method ContactCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactCompany::class);
    }


    public function findEmailCompanyMotifStartEnd(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.email, c.id, c.company, m.motif_company as motif, c.start, c.end, u.firstname, u.lastname, img.images as userImage, c.isDisplayed, n.isNew as notificationIsNew, n.isSeen as notificationIsSeen')
            ->leftJoin('c.motif', 'm')
            ->leftJoin('c.user', 'u')
            ->leftJoin('u.images', 'img')
            ->leftJoin('c.notification', 'n')
            ->orderBy('c.start', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findEmailCompanyMotifStartEndLimited(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.email, c.id, c.company, m.motif_company as motif, c.start, c.end, u.firstname, u.lastname, img.images as userImage, c.isDisplayed, n.isNew as notificationIsNew, n.isSeen as notificationIsSeen')
            ->leftJoin('c.motif', 'm')
            ->leftJoin('c.user', 'u')
            ->leftJoin('u.images', 'img')
            ->leftJoin('c.notification', 'n')
            ->orderBy('c.notification', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return ContactCompany[] Returns an array of ContactCompany objects
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

//    public function findOneBySomeField($value): ?ContactCompany
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
