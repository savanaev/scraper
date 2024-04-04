<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Persistence\Service;

use App\Colleges\Domain\Entity\CollegeDetails;
use App\Colleges\Domain\Repository\CollegeDetailsRepositoryInterface;
use App\Colleges\Domain\Service\CollegeDetailsPersistenceServiceInterface;

class CollegeDetailsPersistenceService implements CollegeDetailsPersistenceServiceInterface
{
    public function __construct(
        protected CollegeDetailsRepositoryInterface $collegeDetailsRepository
    )
    {}
    public function find(int $id): ?CollegeDetails
    {
        return $this->collegeDetailsRepository->find($id);
    }
}
