<?php

namespace App\Service\Scraper\Common;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Базовый скрапер
 */
class BaseScraper implements CommonScraperInterface
{
    /**
     * @param HttpClientInterface $httpClient объект для работы с HTTP клиентом
     */
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * Получение контента из указанных источников.
     *
     * @param array $collegeUrls Список источников
     *
     * @return \Generator Генератор контента из источников
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function run(array $collegeUrls): \Generator
    {
        foreach ($collegeUrls as $collegeUrl) {
            $response = $this->httpClient->request('GET', $collegeUrl, ['timeout' => 10]);
            $content = $response->getContent();

            yield $collegeUrl => $content;
        }
    }
}
