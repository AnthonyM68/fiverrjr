<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserAnonymizer
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function anonymizeAndDeleteUser(User $user): void
    {
        $user->setEmail(uniqid());
        $user->setPassword(uniqid());
        $user->setFirstName(uniqid());
        $user->setLastName(uniqid());
        $user->setphoneNumber(uniqid());
        $user->setUsername(uniqid());
        $user->setPicture(null);
        $user->setCity(uniqid());
        $user->setPortfolio(uniqid());
        $user->setBio(uniqid());
        $user->setVerified(uniqid());
        $user->setRoles(['ROLE_ANONYMOUS']);
        $user->setDateRegister(new \DateTime('1970-01-01 00:00:00'));
        $user->setPassword($this->passwordHasher->hashPassword($user, uniqid()));
        // Marquer l'utilisateur comme anonymisé (facultatif)
        // $user->setAnonymized(true);
        //$this->entityManager->flush();
        // Supprime l'utilisateur (si nécessaire)
        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }
}
