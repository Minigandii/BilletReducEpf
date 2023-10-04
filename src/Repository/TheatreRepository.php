<?php

namespace App\Repository;

use App\Entity\Theatre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Theatre>
 *
 * @method Theatre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Theatre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Theatre[]    findAll()
 * @method Theatre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TheatreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theatre::class);
    }

    public function save(Theatre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Theatre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getTheatreById(int $id): ?Theatre
    {
        return $this->find($id);
    }

    
    public function findById($id)
    {
        return $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(); 
    }

    public function findByBRId($BRId)
    {
        return $this->createQueryBuilder('o')
            ->where('o.BRId = :BRId')
            ->setParameter('BRId', $BRId)
            ->getQuery()
            ->getOneOrNullResult();
    }
        
//    /**
//     * @return Theatre[] Returns an array of Theatre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Theatre
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
