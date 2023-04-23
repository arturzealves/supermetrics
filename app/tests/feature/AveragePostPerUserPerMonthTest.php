<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use DateTime;
use Statistics\Enum\StatsEnum;
use SocialPost\Hydrator\FictionalPostHydrator;
use Statistics\Service\Factory\StatisticsServiceFactory;
use Statistics\Extractor\StatisticsToExtractor;
use Statistics\Dto\ParamsTo;
use Statistics\Service\StatisticsService;
use Traversable;

/**
 * Class AveragePostPerUserTest
 *
 * @package Tests\Feature
 */
class AveragePostPerUserPerMonthTest extends TestCase
{
    private const STAT_LABELS = [
        StatsEnum::TOTAL_POSTS_PER_WEEK                    => 'Total posts split by week',
        StatsEnum::AVERAGE_POSTS_NUMBER_PER_USER_PER_MONTH => 'Average number of posts per user per month',
        StatsEnum::AVERAGE_POST_LENGTH                     => 'Average character length/post in a given date range',
        StatsEnum::MAX_POST_LENGTH                         => 'Longest post by character length in a given date range',
    ];

    /**
     * @var Traversable
     */
    private $hydratedPosts;

    /**
     * @var StatisticsService
     */
    private $statisticsService;

    protected function setUp(): void
    {
        // Get the test data from file and convert it to an array
        $fileContents = file_get_contents('./tests/data/social-posts-response.json');
        $data = json_decode($fileContents, true);

        // Hydrate the posts array
        $this->hydratedPosts = $this->hydratePosts($data['data']['posts'] ?? []);

        // Create the statistics service
        $this->statisticsService = (new StatisticsServiceFactory())->create();
    }

    /**
     * @test average post per user per month
     */
    public function testAveragePostPerUserPerMonth(): void
    {
        // Prepare the calculation parameters
        $date = DateTime::createFromFormat('F, Y', 'August, 2018');
        $startDate = (clone $date)->modify('first day of this month');
        $endDate = (clone $date)->modify('last day of this month');

        $parameters = [
            (new ParamsTo())
                ->setStatName(StatsEnum::AVERAGE_POSTS_NUMBER_PER_USER_PER_MONTH)
                ->setStartDate($startDate)
                ->setEndDate($endDate),
        ];

        // Get the calculated statistics
        $statistics = $this->statisticsService->calculateStats(
            $this->hydratedPosts,
            $parameters
        );

        // Extracts the statistics
        $response = (new StatisticsToExtractor())->extract($statistics, self::STAT_LABELS);

        // Loops through the statistics and asserts the value of the average of posts per user per month
        foreach ($response['children'] as $statistic) {
            if ($statistic['name'] !== StatsEnum::AVERAGE_POSTS_NUMBER_PER_USER_PER_MONTH) {
                continue;
            }

            $this->assertEquals(1, $statistic['value']);

            break;
        }
    }

    /**
     * @param array $posts
     */
    private function hydratePosts(array $posts): Traversable
    {
        $hydrator = new FictionalPostHydrator();
        foreach ($posts as $postData) {
            yield $hydrator->hydrate($postData);
        }
    }
}
