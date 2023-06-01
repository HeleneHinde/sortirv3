<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Sortie;
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

    public function mainSearch($name, $dateUn, $dateDeux,Campus $campus, $userIdScales, $userIdHorns, $userIdHornsNR, $dateDuJour)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.users', 'sortie_user');


        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like('s.name', ':name'))
                ->setParameter('name', '%' . $name . '%');
        }

        if ($dateUn !== null && $dateDeux !== null) {
            $qb->andWhere('s.first_air_date BETWEEN :dateUn AND :dateDeux')
                ->setParameter('dateUn', $dateUn)
                ->setParameter('dateDeux', $dateDeux);
        }

        if ($campus !== null) {
            $qb->andWhere('s.campus = :campus')
                ->setParameter('campus', $campus->getId());
        }

        if (!empty($userIdScales)) {
            $qb->andWhere('s.user_id = :userIdScales')
                ->setParameter('userIdScales', $userIdScales);
        }

        if (!empty($userIdHorns)) {
            $qb->andWhere('sortie_user.user_id = :userIdHorns')
                ->setParameter('userIdHorns', $userIdHorns);
        }

        if (!empty($userIdHornsNR)) {
            $qb->andWhere('sortie_user.user_id != :userIdHornsNR')
                ->setParameter('userIdHornsNR', $userIdHornsNR);
        }

        if (!empty($dateDuJour)) {
            $qb->andWhere('s.first_air_date < :dateDuJour')
                ->setParameter('dateDuJour', $dateDuJour);
        }

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
