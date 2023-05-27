<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
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

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createAllPublishedOrderedByNewestQuery(?Tag $tag): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.tags', 't')
            ->addSelect('t')
            ->orderBy('p.publishedAt', 'DESC')
        ;

        if ($tag) {
            $queryBuilder->andWhere(':tag MEMBER OF p.tags')
                ->setParameter('tag', $tag);
        }

        return $queryBuilder->getQuery();
    }

    public function findOneByPublishDateAndSlug(string $date, string $slug): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('DATE(p.publishedAt) = :date')
            ->andWhere('p.slug = :slug')
            ->setParameters([
                'date' => $date,
                'slug' => $slug,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findPreviousPost(Post $post): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt < :postPublishedAt')
            ->setParameter('postPublishedAt', $post->getPublishedAt())
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findNextPost(Post $post): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt > :postPublishedAt')
            ->setParameter('postPublishedAt', $post->getPublishedAt())
            ->orderBy('p.publishedAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Post[] returns an array of Post objects similar with the given post
     */
    public function findSimilar(Post $post, int $maxResults = 4): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.tags', 't')
            ->addSelect('COUNT(t) AS HIDDEN numberOfTags')
            ->andWhere('t IN (:tags)')
            ->andWhere('p != :post')
            ->setParameters([
                'tags' => $post->getTags(),
                'post' => $post,
            ])
            ->groupBy('p')
            ->addOrderBy('numberOfTags', 'DESC')
            ->addOrderBy('p.publishedAt', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMostCommented(int $maxResults): array
    {
        return $this->createQueryBuilder('p')
           ->join('p.comments', 'c')
           ->addSelect('COUNT(c) AS HIDDEN numberOfComments')
           ->andWhere('c.isActive = true')
           ->groupBy('p')
           ->orderBy('numberOfComments', 'DESC')
           ->addOrderBy('p.publishedAt', 'DESC')
           ->setMaxResults($maxResults)
           ->getQuery()
           ->getResult()
        ;
    }

    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('LOWER(p.title) LIKE :searchTerm OR LOWER(p.body) LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.mb_strtolower($searchTerm).'%')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
