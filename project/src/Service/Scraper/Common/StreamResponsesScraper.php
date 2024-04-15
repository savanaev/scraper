<?php

namespace App\Service\Scraper\Common;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Скрапер для работы с результатами стрима запросов.
 */
class StreamResponsesScraper implements CommonScraperInterface
{
    /**
     * @param HttpClientInterface $httpClient объект для работы с HTTP клиентом
     */
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * Запуск скрапера.
     *
     * @param array $collegeUrls список испточников
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function run(array $collegeUrls): \Generator
    {
        $promises = [];
        foreach ($collegeUrls as $collegeUrl) {
            $promises[] = $this->httpClient->request('GET', $collegeUrl, ['timeout' => 10]);
        }

        foreach ($this->httpClient->stream($promises) as $response => $chunkStream) {
            if ($chunkStream->isLast()) {
                $pageUrl = $response->getInfo('original_url');
                $content = $response->getContent();

                yield $pageUrl => $content;
            }
        }
    }
}
