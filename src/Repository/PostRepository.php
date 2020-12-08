<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findUserBookmarks($criteria, $orderBy = ['id' => 'DESC'], $limit = 10, $offset = 0)
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->join('p.bookmarks', 'b')
            ->join('b.user', 'u')
        ;

        foreach ($criteria as $property => $value) {
            if ($property == 'user') {
                $qb ->andWhere('u = :' . $property . '')
                    ->setParameter($property,$value)
                ;
            } else {
                $qb->andWhere('p.'. $property .' = :' . $property . '')
                    ->setParameter($property,$value)
                ;
            }
        }

        foreach ($orderBy as $key => $value) {
            $qb->orderBy('b.'.$key,$value);
        }

        $qb ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $criteria
     * @param string[] $orderBy
     * @param int $limit
     * @param int $offset
     * @return int|mixed|string
     */
    public function findPostsBy($criteria,$orderBy = ['publishedAt' => 'DESC'], $limit = 10, $offset = 0)
    {
        $qb = $this->createQueryBuilder('p');

        foreach ($criteria as $property => $value) {
            if ($property == 'category') {
                $qb ->join('p.categories', 'c')
                    ->andWhere('c = :' . $property . '')
                    ->setParameter($property,$value)
                ;
            } else {
                $qb->andWhere('p.'. $property .' = :' . $property . '')
                    ->setParameter($property,$value)
                ;
            }
        }

        foreach ($orderBy as $key => $value) {
            $qb->orderBy('p.'.$key,$value);
        }

        $qb ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
