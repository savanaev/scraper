<?php

declare(strict_types=1);

namespace App\Colleges\Domain\Service;

use App\Colleges\Domain\Entity\CollegeList;

interface CollegeListPersistenceServiceInterface
{
    public function findByHash(string $hash): ?CollegeList;

    public function saveColleges(array $collegeListItems): void;

    public function findAll(): CollegeList|array;

    public function removeOldColleges(int $count): void;

    public function clearAllColleges(): void;
}
