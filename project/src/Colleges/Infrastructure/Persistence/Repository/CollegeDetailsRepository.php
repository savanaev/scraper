<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Persistence\Repository;

use App\Colleges\Domain\Entity\CollegeDetails;
use App\Colleges\Domain\Repository\CollegeDetailsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CollegeDetails>
 *
 * @method CollegeDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollegeDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollegeDetails[]    findAll()
 * @method CollegeDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeDetailsRepository extends ServiceEntityRepository implements CollegeDetailsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollegeDetails::class);
    }

}
