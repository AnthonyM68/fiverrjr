<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\ServiceItem;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    }
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('su')
            ->innerJoin('su.user', 'u')
            ->where('u = :user') // On compare directement l'entité User
            ->setParameter('user', $user) // On passe l'objet User en paramètre
            ->orderBy('su.createDate', 'DESC')
            ->getQuery()
            ->getResult();
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
