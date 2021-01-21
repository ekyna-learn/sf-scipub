<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findByPublication(Publication $publication): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere($qb->expr()->eq('c.publication', ':publication'))
            ->andWhere($qb->expr()->eq('c.validated', ':validated'))
            ->getQuery()
            ->setParameters([
                'publication' => $publication,
                'validated'   => true,
            ])
            ->getResult();
    }

    /**
     * Returns the comments that needs moderation.
     *
     * @param int $limit
     *
     * @return Comment[]
     */
    public function findNotValidated(int $limit = 3): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere($qb->expr()->eq('c.validated', ':validated'))
            ->getQuery()
            ->setParameter('validated', false)
            ->setMaxResults($limit)
            ->getResult();
    }
}
