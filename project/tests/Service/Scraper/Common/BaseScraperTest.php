<?php

namespace App\Tests\Service\Scraper\Common;

use App\Service\Scraper\Common\BaseScraper;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BaseScraperTest extends TestCase
{
    public function testRunReturnsGenerator(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);

        $baseScraper = new BaseScraper($httpClient);

        $urls = ['http://demo.loc/1', 'http://demo.loc/2', 'http://demo.loc/3'];

        $response = $this->createMock(ResponseInterface::class);

        $httpClient->method('request')->willReturn($response);

        $response->method('getContent')->willReturn('Content data');

        $generator = $baseScraper->run($urls);

        $this->assertInstanceOf(Generator::class, $generator);

        foreach ($generator as $url => $content) {

            $this->assertContains($url, $urls);
            $this->assertEquals('Content data', $content);
        }
    }
}
