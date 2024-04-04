<?php

declare(strict_types=1);

namespace App\Colleges\Domain\Service;

use App\Colleges\Domain\Entity\CollegeList;

interface HashGeneratorInterface
{
    public function generateHash(CollegeList $college): string;
}
