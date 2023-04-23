<?php

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class AveragePostPerUserPerMonth extends AbstractCalculator
{
    protected const UNITS = 'posts';

    private const MONTHS_PER_YEAR = 12;

    /**
     * @var array
     */
    private $userPostCount = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        if (null === $postTo->getAuthorId() || null === $postTo->getDate()) {
            return;
        }

        $authorId = $postTo->getAuthorId();
        $month = $postTo->getDate()->format('n');

        // Initialize the $userPostCount array with zeros for each month position
        if (!isset($this->userPostCount[$authorId])) {
            $this->userPostCount[$authorId] = array_fill(1, self::MONTHS_PER_YEAR, 0);
        }

        $this->userPostCount[$authorId][$month] += 1;
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        if (empty($this->userPostCount)) {
            return (new StatisticsTo())->setValue(0);
        }

        $userAverage = [];
        foreach ($this->userPostCount as $authorId => $userMonthlySum) {
            // Remove months with zero as value and count them
            $monthsCount = count(array_filter($userMonthlySum));

            // Calculate the average for each user
            $userAverage[$authorId] = $monthsCount === 0
                ? 0
                : array_sum($userMonthlySum) / $monthsCount;
        }

        // Return the average of all users
        return (new StatisticsTo())->setValue(
            round(array_sum($userAverage) / count($userAverage), 2)
        );
    }
}
