<?php

declare(strict_types=1);

namespace App\Colleges\Domain\Service;

use App\Colleges\Domain\Entity\CollegeDetails;

interface CollegeDetailsPersistenceServiceInterface
{
    public function find(int $id): ?CollegeDetails;
}
