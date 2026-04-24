<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /** @return Page[] */
    public function findRootPages(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.parent IS NULL')
            ->orderBy('p.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Page[] */
    public function findWithBlocksAndChildren(int $id): ?Page
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.blocks', 'b')
            ->leftJoin('p.children', 'c')
            ->addSelect('b', 'c')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBySlug(string $slug): ?Page
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.blocks', 'b')
            ->addSelect('b')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('b.position', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
