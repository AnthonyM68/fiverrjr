<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Theme>
 */
class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }
    // Requête pour rechercher un searchterm uniquement dans la table Theme
    public function findByTerm($searchterm)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.nameTheme  LIKE :term')
            ->setParameter('term', '%' . $searchterm . '%')
            ->getQuery()
            ->getResult();
    }
     // Requête pour rechercher un searchterm à partir de Theme->Category/Course/Service
    public function searchByTermAllChilds($searchTerm)
    {
        return $this->createQueryBuilder('theme')
        ->leftJoin('theme.categories', 'category')
        ->leftJoin('category.courses', 'course')
        ->leftJoin('course.services', 'service')
        ->addSelect('category', 'course', 'service')
        ->where('theme.nameTheme LIKE :searchTerm')
        ->orWhere('category.nameCategory LIKE :searchTerm')
        ->orWhere('course.nameCourse LIKE :searchTerm')
        ->orWhere('service.title LIKE :searchTerm')
        ->orWhere('service.description LIKE :searchTerm')
        ->setParameter('searchTerm', '%' . $searchTerm . '%')
        ->getQuery()
        ->getResult();
    }
    public function countAll()
{
    return $this->createQueryBuilder('t')
        ->select('count(t.id)')
        ->getQuery()
        ->getSingleScalarResult();
}
    
    //    /**
    //     * @return Theme[] Returns an array of Theme objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Theme
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
