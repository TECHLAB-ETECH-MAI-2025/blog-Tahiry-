<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Recherche pour DataTables
     */
    public function findForDataTable(int $start, int $length, ?string $search, ?array $order): array
    {
        $qb = $this->createQueryBuilder('a')
            ->setFirstResult($start)
            ->setMaxResults($length);

        if ($search) {
            $qb->where('a.title LIKE :search OR a.content LIKE :search')
               ->setParameter('search', '%'.$search.'%');
        }

        if ($order) {
            $column = $order['column'] ?? 0;
            $dir = $order['dir'] ?? 'asc';
            
            $mapping = [
                0 => 'a.id',
                1 => 'a.title',
                2 => 'a.createdAt',
                // Ajoutez d'autres colonnes si nécessaire
            ];
            
            if (isset($mapping[$column])) {
                $qb->orderBy($mapping[$column], $dir);
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte les résultats avec filtre
     */
    public function countSearch(string $search): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.title LIKE :search OR a.content LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte tous les articles
     */
    public function countAll(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}