<?php

namespace App\Entity;

use App\Repository\AbsencePeriodRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class MonthlyPeriod implements Period
{
    private ?int $month;
    private ?int $year;

    public function __construct()
    {
        $this->month = date('n');
        $this->year = date('Y');
    }

    /**
     * @return int|null
     */
    public function getMonth(): int|null
    {
        return $this->month;
    }

    /**
     * @param int|null $month
     * @return MonthlyPeriod
     */
    public function setMonth(int|null $month): MonthlyPeriod
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getYear(): int|null
    {
        return $this->year;
    }

    /**
     * @param int|null $year
     * @return MonthlyPeriod
     */
    public function setYear(int|null $year): MonthlyPeriod
    {
        $this->year = $year;
        return $this;
    }
}
