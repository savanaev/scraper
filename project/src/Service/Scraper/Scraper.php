<?php

namespace App\Service\Scraper;

use App\Service\Scraper\Common\CommonScraperInterface;

class Scraper
{
    public function __construct(private readonly CommonScraperInterface $scraper)
    {
    }

    /**
     * Получение данных о колледжах.
     *
     * @param array $urls массив ссылок на страницы с колледжами
     */
    public function scrape(array $urls): \Generator
    {
        yield from $this->scraper->run($urls);
    }
}
