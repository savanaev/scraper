<?php

declare(strict_types=1);

namespace App\Colleges\Domain\Service;

interface CollegeListScraperServiceInterface
{
    public function scrapeCollegesList(): array;
}
