<?php

namespace App\Tests\Service\Scraper;

use App\Service\Scraper\Common\CommonScraperInterface;
use App\Service\Scraper\Scraper;
use Generator;
use PHPUnit\Framework\TestCase;

class ScraperTest extends TestCase
{
    public function testScrapeReturnsGenerator(): void
    {
        $commonScraper = $this->createMock(CommonScraperInterface::class);

        $commonScraper->method('run')->willReturnCallback(function ($urls) {
            foreach ($urls as $url) {
                yield $url => 'Scraped ' . $url;
            }
        });

        $scraper = new Scraper($commonScraper);

        $generator = $scraper->scrape(['url1', 'url2', 'url3']);

        $this->assertInstanceOf(Generator::class, $generator);

        $scrapedData = iterator_to_array($generator);
        $this->assertCount(3, $scrapedData);
        $this->assertArrayHasKey('url1', $scrapedData);
        $this->assertArrayHasKey('url2', $scrapedData);
        $this->assertArrayHasKey('url3', $scrapedData);
    }
}
