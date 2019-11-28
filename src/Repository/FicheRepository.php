<?php

namespace App\Repository;

use App\Entity\Fiche;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Fiche|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fiche|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fiche[]    findAll()
 * @method Fiche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fiche::class);
    }

    public function findFicheWithDate($date1, $date2)
    {
        return $this->createQueryBuilder('a')
            ->where('a.date BETWEEN :date1 AND :date2')
            ->setParameter('date1', $date1)
            ->setParameter('date2', $date2)
            ->orderby('a.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
