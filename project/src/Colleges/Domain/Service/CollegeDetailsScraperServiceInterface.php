<?php

declare(strict_types=1);

namespace App\Colleges\Domain\Service;

use App\Colleges\Domain\Entity\CollegeDetails;
use App\Colleges\Domain\Entity\CollegeList;

interface CollegeDetailsScraperServiceInterface
{
    public function scrapeCollegeDetails(CollegeList $college): ?CollegeDetails;
}
