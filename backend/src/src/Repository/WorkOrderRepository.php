<?php

namespace App\Repository;

use App\Entity\WorkOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkOrder>
 *
 * @method WorkOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkOrder[]    findAll()
 * @method WorkOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkOrder::class);
    }

//    /**
//     * @return WorkOrder[] Returns an array of WorkOrder objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WorkOrder
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findAssignedToUser($user, ?string $status = null): array
{
    $qb = $this->createQueryBuilder('w')
        ->andWhere('w.assignedTo = :user')
        ->setParameter('user', $user)
        ->orderBy('w.createdAt', 'DESC');

    if ($status) {
        $qb->andWhere('w.status = :status')
           ->setParameter('status', strtoupper($status));
    }

    return $qb->getQuery()->getResult();
}
}
