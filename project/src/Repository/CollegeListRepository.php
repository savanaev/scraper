<?php

namespace App\Repository;

use App\Entity\CollegeList;
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
class CollegeListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollegeList::class);
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

    /**
     * Удаление колледжей по Urls.
     *
     * @param string[] $collegeUrls Массив Urls колледжей для удаления
     */
    public function deleteByUrls(array $collegeUrls): void
    {
        $qb = $this->createQueryBuilder('c');
        $qb->delete()
            ->where($qb->expr()->in('c.url', ':urls'))
            ->setParameter('urls', $collegeUrls)
            ->getQuery()
            ->execute();
    }

    /**
     * Получение общего количества записей колледжей.
     */
    public function countTotalColleges(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Получение названия первого колледжа.
     */
    public function getFirstCollegeName(): ?string
    {
        $firstCollege = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $firstCollege ? $firstCollege->getName() : null;
    }

    /**
     * Получение названия последнего колледжа.
     */
    public function getLastCollegeName(): ?string
    {
        $lastCollege = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $lastCollege ? $lastCollege->getName() : null;
    }

    /**
     * Находит колледж по URL.
     *
     * @param string $url URL колледжа
     */
    public function findByUrl(string $url): ?CollegeList
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.url = :url')
            ->setParameter('url', $url)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Очистка таблицы.
     */
    public function clearTable(): void
    {
        $this->createQueryBuilder('e')
        ->delete()
        ->getQuery()
        ->execute();
    }
}
