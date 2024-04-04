<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Persistence\Service;

use App\Colleges\Domain\Entity\CollegeList;
use App\Colleges\Domain\Repository\CollegeListRepositoryInterface;
use App\Colleges\Domain\Service\CollegeListPersistenceServiceInterface;
use App\Colleges\Domain\Service\HashGeneratorInterface;
use DateTimeImmutable;

class CollegeListPersistenceService implements CollegeListPersistenceServiceInterface
{
    public function __construct(
        protected HashGeneratorInterface $hashGenerator,
        protected CollegeListRepositoryInterface $collegeListRepository
    )
    {}

    public function findByHash(string $hash): ?CollegeList
    {
        return $this->collegeListRepository->findOneBy(['hash' => $hash]);
    }

    public function saveColleges(array $collegeListItems): void
    {
        foreach ($collegeListItems as $collegeData) {

            $collegeListItem = $collegeData['college'];
            $college = new CollegeList();
            $collegeHash = $this->hashGenerator->generateHash($collegeListItem);
            $college->setImageUrl($collegeListItem->getImageUrl());
            $college->setName($collegeListItem->getName());
            $college->setCity($collegeListItem->getCity());
            $college->setState($collegeListItem->getState());
            $college->setUrl($collegeListItem->getUrl());
            $college->setHash($collegeHash);
            $college->setCreatedAt(new DateTimeImmutable());

            $collegeDetails = $collegeData['college_details'];
            $college->setCollegeDetails($collegeDetails);

            $this->collegeListRepository->persist($college);
        }

        $this->collegeListRepository->flush();
    }

    public function findAll(): CollegeList|array
    {
        return $this->collegeListRepository->findAll();
    }

    public function removeOldColleges(int $count): void
    {
        $oldestColleges = $this->collegeListRepository->findOldestColleges($count);

        foreach ($oldestColleges as $college) {
            $this->collegeListRepository->remove($college);
        }

        $this->collegeListRepository->flush();
    }

    public function clearAllColleges(): void
    {
        $this->collegeListRepository->clearAllColleges();
    }
}
