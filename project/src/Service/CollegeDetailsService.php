<?php

namespace App\Service;

use App\Entity\CollegeDetails;
use App\Repository\CollegeDetailsRepository;

/**
 * Заполнение детальной информации о колледжах.
 */
class CollegeDetailsService
{
    public function __construct(private CollegeDetailsRepository $collegeDetailsRepository)
    {
    }

    /**
     * Добавление информации о колледжах.
     */
    public function add(array $collectCollegesDetails, array $collegeList): void
    {
        $colleges = $this->fillEntity($collectCollegesDetails, $collegeList);
        $this->collegeDetailsRepository->batchAdd($colleges);
    }

    /**
     * Заполнение объекта CollegeDetails.
     */
    private function fillEntity(array $collectCollegesDetails, array $collegesList): array
    {
        $colleges = [];
        foreach ($collectCollegesDetails as $collegeUrl => $collegeDetailsDTO) {
            $college = new CollegeDetails();
            $college->setName($collegeDetailsDTO->getName());
            $college->setAddress($collegeDetailsDTO->getAddress());
            $college->setPhone($collegeDetailsDTO->getPhone());
            $college->setWebsite($collegeDetailsDTO->getWebsite());
            $collegeList = $collegesList[$collegeUrl];
            $college->setCollegeList($collegeList);
            $colleges[] = $college;
        }

        return $colleges;
    }
}
