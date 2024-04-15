<?php

namespace App\Repository;

use App\Entity\CollegeDetails;
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
class CollegeDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollegeDetails::class);
    }

    /**
     * Добавление колледжей.
     *
     * @param array $colleges массив сущностей колледжей
     */
    public function batchAdd(array $colleges): void
    {
        $entityManager = $this->getEntityManager();

        foreach ($colleges as $college) {
            $entityManager->persist($college);
        }

        $entityManager->flush();
    }
}
