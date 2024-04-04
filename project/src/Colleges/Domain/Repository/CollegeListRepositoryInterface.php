<?php

declare(strict_types=1);

namespace App\Colleges\Domain\Repository;

use App\Colleges\Domain\Entity\CollegeList;

interface CollegeListRepositoryInterface
{
    public function findOldestColleges(int $limit): array;

    public function clearAllColleges(): void;
}
