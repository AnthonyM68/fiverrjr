<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    public function countAll()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
<<<<<<< HEAD

=======
    public function findUsersByRole(?string $role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%' . $role . '%');
    }
    public function findOneUserByRole(?string $role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%' . $role . '%')
            ->orderBy('u.dateRegister', 'DESC')
            ->setMaxResults(1);
    }
    public function searchByTerm(?string $searchTerm, ?string $userType)
    {
        return $this->createQueryBuilder('u')
        ->where('u.username LIKE :searchTerm')
        ->orWhere('u.firstName LIKE :searchTerm')
        ->orWhere('u.lastName LIKE :searchTerm')
        ->andWhere('u.roles LIKE :role')
        ->setParameter('searchTerm', '%' . $searchTerm . '%')
        ->setParameter('role', '%' . $userType . '%')
        ->orderBy('u.dateRegister', 'DESC')
        ->getQuery()
        ->getResult();
    }
    public function searchByTermFromCity(?string $searchTerm, ?string $userType)
    {
        return $this->createQueryBuilder('u')
        ->where('u.city LIKE :searchTerm')
        ->andWhere('u.roles LIKE :role')
        ->setParameter('searchTerm', '%' . $searchTerm . '%')
        ->setParameter('role', '%' . $userType . '%')
        ->orderBy('u.dateRegister', 'DESC')
        ->getQuery()
        ->getResult();
    }
>>>>>>> a5feb3db027be62ad942fe5c640558f052dbbba0
    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
