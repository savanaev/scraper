<?php

namespace App\Service\Scraper\Common;

use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Скрапер использующий кэш адаптер для HttpClient.
 */
class CachingHttpClientScraper extends StreamResponsesScraper implements CommonScraperInterface
{
    /**
     * @param HttpClientInterface $httpClient объект для работы с HTTP клиентом
     * @param StoreInterface      $store      Кэш адаптер
     */
    public function __construct(HttpClientInterface $httpClient, StoreInterface $store)
    {
        $httpClient = $this->adapter($httpClient, $store);
        parent::__construct($httpClient);
    }

    /**
     * Кэш адаптер для HttpClient.
     *
     * @param HttpClientInterface $httpClient объект для работы с HTTP клиентом
     * @param StoreInterface      $cache      адаптер
     */
    private function adapter(HttpClientInterface $httpClient, StoreInterface $cache): HttpClientInterface
    {
        return new CachingHttpClient($httpClient, $cache);
    }
}
