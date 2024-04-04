<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Persistence\Service;

use App\Colleges\Domain\Entity\CollegeList;
use App\Colleges\Domain\Service\HashGeneratorInterface;

class HashGenerator implements HashGeneratorInterface
{
    public function generateHash(CollegeList $college): string
    {
        $hashInput = $college->getName() . $college->getCity() . $college->getState();

        return sha1($hashInput);
    }
}
