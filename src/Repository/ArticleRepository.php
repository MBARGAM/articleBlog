<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineExtensions\Query\Mysql\Year;

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

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /* requete preparee
    & 1-  a represente une alias qui est la table
       2- c est la condition
      3- les parametre dela conditions y sont definies
      4- les 2 dernieres fonctions servent a l execution des requetes
    */
    public function findByWord($value): array
    {
        return $this->createQueryBuilder('a')
            ->Where('a.description LIKE  :val ')
            ->setParameter('val', "%".$value."%")
            ->orderBy('a.title', 'ASC')
           ->getQuery()
           ->getResult()
      ;
   }
    public function findById($value): array
    {
        return $this->createQueryBuilder('a')
            ->Where('a.id =:val ')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
            ;
    }

    /* requete preparee
    & 1-  a represente une alias qui est la table
       2- c est la condition
      3- les parametre dela conditions y sont definies
      4- les 2 dernieres fonctions servent a l execution des requetes
    */

    public function findByDate($value): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM Article a
            INNER JOIN Category on a.category_id = Category.id
            WHERE YEAR(a.date_creation) = '.$value.'
            ORDER BY a.title ASC
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

   /* function permettant de recuperer les années de creation de la table article*/

    public function findYearlist(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT DISTINCT(YEAR(`date_creation`)) AS annee FROM Article a
            ORDER BY annee ASC
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    // recherche des articles par categorie
    public function findByCategory($value): array
    {
        return $this->createQueryBuilder('a')
            ->Where('a.category =:val ')
            ->setParameter('val', $value)
            ->orderBy('a.title')
            ->getQuery()
            ->getResult()
            ;
    }


//SELECT DISTINCT(YEAR(`date_creation`)) AS annee FROM `article` ORDER BY annee
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
}
