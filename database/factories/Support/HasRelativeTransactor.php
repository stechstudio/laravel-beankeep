<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;

trait HasRelativeTransactor
{
    protected readonly RelativeDate $relativeDate;

    protected readonly RelativeTransactor $lastYear;

    protected readonly RelativeTransactor $thisYear;

    protected function lastYear(
        ?string $dateLookup = null,
    ): Transactor|CarbonImmutable {
        return $dateLookup
            ? $this->getLastYear()[$dateLookup]
            : $this->getLastYear();
    }

    protected function thisYear(
        ?string $dateLookup = null,
    ): Transactor|CarbonImmutable {
        return $dateLookup
            ? $this->getThisYear()[$dateLookup]
            : $this->getThisYear();
    }

    protected function lastYearRange(): CarbonPeriod
    {
        return $this->getRelativeDate()->lastYearRange();
    }

    protected function thisYearRange(): CarbonPeriod
    {
        return $this->getRelativeDate()->thisYearRange();
    }

    protected function getDate(
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): CarbonImmutable {
        if ($lastYear) {
            return $this->getRelativeDate()->lastYear[$lastYear];
        }

        if ($thisYear) {
            return $this->getRelativeDate()->thisYear[$thisYear];
        }

        return CarbonImmutable::now();
    }

    protected function getRelativeDate(): RelativeDate
    {
        return $this->relativeDate ??= new RelativeDate();
    }

    protected function getLastYear(): RelativeTransactor
    {
        return $this->lastYear ??= new RelativeTransactor(
            $this->getRelativeDate()->lastYear,
        );
    }

    protected function getThisYear(): RelativeTransactor
    {
        return $this->thisYear ??= new RelativeTransactor(
            $this->getRelativeDate()->thisYear,
        );
    }
}
