<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Persistence\Repository;

use App\Colleges\Domain\Entity\CollegeList;
use App\Colleges\Domain\Repository\CollegeListRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CollegeList>
 *
 * @method CollegeList|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollegeList|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollegeList[]    findAll()
 * @method CollegeList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeListRepository extends ServiceEntityRepository implements CollegeListRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollegeList::class);
    }

    public function findOldestColleges(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function persist($college): void
    {
        $this->getEntityManager()->persist($college);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    private function remove($college): void
    {
        $this->getEntityManager()->remove($college);
    }

    public function clearAllColleges(): void
    {
        $this->createQueryBuilder('c')
            ->delete(CollegeList::class, 'c')
            ->getQuery()
            ->execute();
    }
}
