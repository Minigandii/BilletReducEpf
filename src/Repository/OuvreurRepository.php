<?php

namespace App\Repository;

use App\Entity\Ouvreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ouvreur>
 *
 * @method Ouvreur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ouvreur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ouvreur[]    findAll()
 * @method Ouvreur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OuvreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ouvreur::class);
    }

    public function save(Ouvreur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ouvreur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByTheatreId($theatreId)
    {
        return $this->createQueryBuilder('o')
            ->join('o.theatre', 't')
            ->where('t.id = :theatreId')
            ->setParameter('theatreId', $theatreId)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Ouvreur[] Returns an array of Ouvreur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ouvreur
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
