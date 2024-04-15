<?php

namespace App\Tests\Service\Scraper\Common;

use App\Service\Scraper\Common\StreamResponsesScraper;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CachingHttpClientScraperTest extends TestCase
{
    public function testRunReturnsGenerator(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $store = $this->createMock(StoreInterface::class);

        $cachingHttpClient = $this->getMockBuilder(CachingHttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $streamScraper = new StreamResponsesScraper($cachingHttpClient);

        $urls = ['http://demo.loc/1', 'http://demo.loc/2', 'http://demo.loc/3'];

        $response = $this->createMock(ResponseInterface::class);

        $httpClient->method('request')->willReturn($response);

        $response->method('getInfo')->willReturn('http://demo.loc/1');
        $response->method('getContent')->willReturn('Content data');

        $generator = $streamScraper->run($urls);

        $this->assertInstanceOf(Generator::class, $generator);

        foreach ($generator as $url => $content) {
            $this->assertContains($url, $urls);
            $this->assertEquals('Content data', $content);
        }
    }
}
