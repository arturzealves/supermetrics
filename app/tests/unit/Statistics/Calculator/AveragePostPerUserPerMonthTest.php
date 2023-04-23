<?php

namespace Tests\Unit\Statistics\Calculator;

use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\AveragePostPerUserPerMonth;
use Statistics\Dto\ParamsTo;
use Statistics\Dto\StatisticsTo;
use Statistics\Enum\StatsEnum;

/**
 * Class AveragePostPerUserPerMonthTest
 *
 * @package Tests\Unit\Statistics\Calculator
 */
class AveragePostPerUserPerMonthTest extends TestCase
{
    const AUTHOR_ID_1 = 'author1';
    const AUTHOR_ID_2 = 'author2';
    const AUTHOR_ID_3 = 'author3';
    const STAT_NAME = 'statName';
    const UNITS = 'posts';

    /**
     * @var AveragePostPerUserPerMonth
     */
    private $calculator;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        $this->calculator = new AveragePostPerUserPerMonth();
        $paramsTo = (new ParamsTo())
            ->setStatName(StatsEnum::AVERAGE_POSTS_NUMBER_PER_USER_PER_MONTH);

        $this->calculator->setParameters($paramsTo);
    }

    public function testDoAccumulate(): void
    {
        $socialPostTo = (new SocialPostTo())->setAuthorId(self::AUTHOR_ID_1);

        $this->assertNull($this->calculator->accumulateData($socialPostTo));
    }

    /**
     * Test it returns zero when there are no SocialPostTo instances accumulated
     */
    public function testDoCalculateWithoutAccumulating(): void
    {
        $result = $this->calculator->calculate();

        $this->assertInstanceOf(StatisticsTo::class, $result);
        $this->assertEquals(0, $result->getValue());
    }

    /**
     * Test doCalculate with some valid accumulated SocialPostTo instances
     */
    public function testDoCalculate(): void
    {
        // Preparing the test data
        $janPost = (new SocialPostTo())->setDate(new \DateTime('01-01-2023'));
        $fevPost = (new SocialPostTo())->setDate(new \DateTime('02-02-2023'));
        $marPost = (new SocialPostTo())->setDate(new \DateTime('03-03-2023'));

        $expectedUserAverages = [];
        $this->calculator->accumulateData($janPost->setAuthorId(self::AUTHOR_ID_1));
        $expectedUserAverages[self::AUTHOR_ID_1] = 1 / 1;

        $this->calculator->accumulateData($janPost->setAuthorId(self::AUTHOR_ID_2));
        $this->calculator->accumulateData($fevPost->setAuthorId(self::AUTHOR_ID_2));
        $expectedUserAverages[self::AUTHOR_ID_2] = 2 / 2;

        $this->calculator->accumulateData($janPost->setAuthorId(self::AUTHOR_ID_3));
        $this->calculator->accumulateData($fevPost->setAuthorId(self::AUTHOR_ID_3));
        $this->calculator->accumulateData($marPost->setAuthorId(self::AUTHOR_ID_3));
        $expectedUserAverages[self::AUTHOR_ID_3] = 3 / 3;

        $expectedUserAverages = array_filter($expectedUserAverages);

        $expectedResult = round(array_sum($expectedUserAverages) / count($expectedUserAverages), 2);

        // Calls the function that is being tested
        $result = $this->calculator->calculate();

        // Assertions about the test results
        $this->assertInstanceOf(StatisticsTo::class, $result);
        $this->assertEquals($result->getValue(), $expectedResult);
        $this->assertEquals($result->getName(), StatsEnum::AVERAGE_POSTS_NUMBER_PER_USER_PER_MONTH);
        $this->assertEquals($result->getUnits(), self::UNITS);
    }
}
