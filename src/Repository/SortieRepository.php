<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function mainSearch($name, $dateUn, $dateDeux, $campus, $userIdScales, $userIdHorns, $userIdHornsNR, $dateDuJour, $etat)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.users', 'u');


        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like('s.name', ':name'))
                ->setParameter('name', '%' . $name . '%');
        }

        if ($dateUn !== null && $dateDeux !== null) {
            $qb->andWhere('s.firstAirDate BETWEEN :dateUn AND :dateDeux')
                ->setParameter('dateUn', $dateUn)
                ->setParameter('dateDeux', $dateDeux);
        }

        if ($campus !== null) {
            $qb->andWhere('s.campus = :campus')
                ->setParameter('campus', $campus);
        }

        if (!empty($userIdScales)) {
            $qb->andWhere('s.user = :userIdScales')
                ->setParameter('userIdScales', $userIdScales);
        }

        if (!empty($userIdHorns)) {
            $qb->andWhere(':userIdHorns MEMBER OF s.users')
                ->setParameter('userIdHorns', $userIdHorns->getId());
        }

        if (!empty($userIdHornsNR)) {
            $qb->andWhere(':userIdHornsNR NOT MEMBER OF s.users')
                ->setParameter('userIdHornsNR', $userIdHornsNR->getId());
        }

        if (!empty($dateDuJour)) {
            $qb->andWhere('s.firstAirDate < :dateDuJour')
                ->setParameter('dateDuJour', $dateDuJour);
        }

        $qb->andWhere('s.etat!= :etat')
            ->setParameter('etat',$etat);

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function main($etatH, $etatC){

        $qb = $this->createQueryBuilder('s');
        $qb->andWhere('s.etat != :etatH')
        ->setParameter('etatH', $etatH);
        $qb->andWhere('s.etat != :etatC')
            ->setParameter('etatC', $etatC);

        $query = $qb->getQuery();
        return $query->getResult();
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
