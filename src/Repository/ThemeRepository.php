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
    // Requête pour rechercher un term à partir de Theme->Category/Course/Service
    // ( prévoir axe d'amélioration)
    public function searchByTermAllChilds($searchTerm)
    {
        return $this->createQueryBuilder('t')
        ->leftJoin('t.categories', 'c')
        ->leftJoin('c.courses', 'co')
        ->leftJoin('co.serviceItems', 'si')
        ->addSelect('c', 'co', 'si')
        ->where('t.nameTheme LIKE :searchTerm')
        ->orWhere('c.nameCategory LIKE :searchTerm')
        ->orWhere('co.nameCourse LIKE :searchTerm')
        ->orWhere('si.title LIKE :searchTerm')
        ->orWhere('si.description LIKE :searchTerm')
        ->setParameter('searchTerm', '%' . $searchTerm . '%')
        ->getQuery()
        ->getResult();
    }
    
    /*
    SELECT t.*, c.*, co.*, si.*
FROM theme t
LEFT JOIN category c ON t.id = c.theme_id
LEFT JOIN course co ON c.id = co.category_id
LEFT JOIN service_item si ON co.id = si.course_id
WHERE t.name_theme LIKE '%Développement%'
   OR c.name_category LIKE '%Développement%'
   OR co.name_course LIKE '%Développement%'
   OR si.title LIKE '%Développement%'
   OR si.description LIKE '%Développement%';

   */
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
