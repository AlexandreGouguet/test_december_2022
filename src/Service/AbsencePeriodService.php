<?php

namespace App\Service;

use App\Entity\AbsencePeriod;
use App\Entity\MonthlyPeriod;
use App\Entity\Period;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AbsencePeriodService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * Get absence filter by period : none or monthly
     * @throws Exception
     */
    public function getAbscencePeriodByPeriod(Period $period = null, $limit = null): array
    {
        if ($period instanceof MonthlyPeriod) {
            $undividedPeriods = $this->em->getRepository(AbsencePeriod::class)->findAbsencesByMonthlyPeriod($period, $limit);
            $allMonthAbsences = [];
            /** @var AbsencePeriod $undividedPeriod */
            foreach ($undividedPeriods as $undividedPeriod) {
                while ($undividedPeriod->getEndTime()->format('Y-m') !== $undividedPeriod->getStartTime()->format('Y-m')) {
                    // Get a new absence with the same startTime but endTime is the last day of month
                    $absenceByMonth = new AbsencePeriod();
                    $absenceByMonth->setStartTime($undividedPeriod->getStartTime());
                    $absenceByMonth->setEndTime(new DateTime(date("Y-m-t", $undividedPeriod->getStartTime()->getTimestamp())));
                    $allMonthAbsences[] = $absenceByMonth;
                    // Change startTime to first day of next month
                    $undividedPeriod->setStartTime($this->getNextMonthFirstDayDateTime($undividedPeriod->getStartTime()));
                }
                $allMonthAbsences[] = $undividedPeriod;
            }
            return $allMonthAbsences;
        } else {
            return $this->em->getRepository(AbsencePeriod::class)->findBy([], ['startTime' => 'DESC'], $limit);
        }
    }

    /**
     * @param DateTime $previousMonthDateTime
     * @return DateTime
     * @throws Exception
     */
    private function getNextMonthFirstDayDateTime(DateTime $previousMonthDateTime): DateTime
    {
        $nextMonth = intval(date('n', $previousMonthDateTime->getTimestamp())) + 1;
        return new DateTime(date('Y-' . $nextMonth . '-01'));
    }
}
