<?php

namespace App\Repository;

use App\Entity\AbsencePeriod;
use App\Entity\MonthlyPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbsencePeriod>
 *
 * @method AbsencePeriod|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbsencePeriod|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbsencePeriod[]    findAll()
 * @method AbsencePeriod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbsencePeriodRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbsencePeriod::class);
    }

    /**
     * @param MonthlyPeriod $monthlyPeriod
     * @param int $limit
     * @return array|null
     */
    public function findAbsencesByMonthlyPeriod(MonthlyPeriod $monthlyPeriod, int $limit): ?array
    {
        $month = $monthlyPeriod->getMonth() === 12 ? 1 : $monthlyPeriod->getMonth() + 1;
        $year = $monthlyPeriod->getMonth() === 12 ? $monthlyPeriod->getYear() + 1 : $monthlyPeriod->getYear();

        return $this->createQueryBuilder('a')
            ->andWhere('a.startTime > :startDate')
            ->andWhere('a.startTime < :endDate')
            ->setParameter('startDate', $monthlyPeriod->getYear() . '-' . $monthlyPeriod->getMonth() . '-01')
            ->setParameter('endDate', $year . '-' . $month . '-01')
            ->orderBy('a.startTime', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
