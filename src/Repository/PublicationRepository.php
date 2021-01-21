<?php

namespace App\Repository;

use App\Entity\Publication;
use App\Entity\Science;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Publication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publication[]    findAll()
 * @method Publication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    /**
     * Returns the latest publication.
     *
     * @param int $limit To limit the results
     *
     * @return Publication[]
     */
    public function findLatest(int $limit = 3): array
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->addOrderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds publications by science.
     *
     * @param Science $science
     *
     * @return Publication[]
     */
    public function findByScience(Science $science): array
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->andWhere($qb->expr()->eq('p.science', ':science'))
            ->addOrderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->setParameter('science', $science)
            ->getResult();
    }

    /**
     * Returns the publications that needs moderation.
     *
     * @param int $limit
     *
     * @return Publication[]
     */
    public function findNotValidated(int $limit = 3): array
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->andWhere($qb->expr()->eq('p.validated', ':validated'))
            ->getQuery()
            ->setParameter('validated', false)
            ->setMaxResults($limit)
            ->getResult();
    }
}
