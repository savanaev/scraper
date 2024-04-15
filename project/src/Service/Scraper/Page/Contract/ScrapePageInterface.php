<?php

namespace App\Service\Scraper\Page\Contract;

/**
 * Interface ScrapePageInterface.
 */
interface ScrapePageInterface
{
    /**
     * Скрапинг.
     *
     * @param string $content HTML код страницы
     */
    public function scrape(string $content): self;

    /**
     * Детали скрапинга.
     */
    public function getDetails(): mixed;
}
