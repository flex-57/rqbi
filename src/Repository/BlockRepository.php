<?php

namespace App\Repository;

use App\Entity\Block;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Block::class);
    }

    public function findMaxPositionForPage(int $pageId): int
    {
        $result = $this->createQueryBuilder('b')
            ->select('MAX(b.position)')
            ->where('b.page = :pageId')
            ->setParameter('pageId', $pageId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result;
    }
}
