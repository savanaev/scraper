<?php

namespace App\Service\Scraper\Page\Contract;

/**
 * Interface CollegeListInterface.
 */
interface CollegeListInterface extends ScrapePageInterface
{
    /**
     * @return array Массив с информацией о колледжах
     */
    public function getDetails(): array;
}
