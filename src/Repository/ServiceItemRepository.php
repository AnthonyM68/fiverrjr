<?php

namespace App\Repository;

use App\Entity\ServiceItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 */
class ServiceItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceItem::class);
    }
    public function findByTerm($term)
    {
        // Crée un QueryBuilder pour la classe Service, aliasée en 's'
        $qb = $this->createQueryBuilder('si')
            // Ajoute une clause WHERE pour filtrer les services dont le titre ou la description contient le terme de recherche
            ->where('si.title LIKE :searchTerm OR si.description LIKE :searchTerm')
            // Définit le paramètre 'searchTerm' pour la requête, avec des jokers (%) pour une recherche partielle
            ->setParameter('searchTerm', '%' . $term . '%');

        // Retourne le QueryBuilder au lieu d'exécuter immédiatement la requête
        return $qb;

        // Ancienne méthode qui exécutait immédiatement la requête et retournait les résultats
        // Nous avons commenté cette partie car nous voulons maintenant retourner un QueryBuilder
        // pour pouvoir appliquer des filtres supplémentaires avant d'exécuter la requête.
        /*
    return $this->createQueryBuilder('s')
        ->andWhere('s.title LIKE :term OR s.description LIKE :term')
        ->setParameter('term', '%' . $term . '%')
        ->getQuery()
        ->getResult();  
    */
    }
    public function countAll()
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    //    /**
    //     * @return Service[] Returns an array of Service objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Service
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
