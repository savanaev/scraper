<?php

namespace App\Service\Scraper\Page\Contract;

use App\DTO\CollegeDetailsDTO;

/**
 * Interface CollegeDetailsInterface.
 */
interface CollegeDetailsInterface extends ScrapePageInterface
{
    /**
     * @return CollegeDetailsDTO информация о колледже
     */
    public function getDetails(): CollegeDetailsDTO;
}
