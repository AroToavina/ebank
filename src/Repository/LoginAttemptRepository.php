<?php

namespace App\Repository;

use App\Entity\LoginAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LoginAttempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginAttempt[]    findAll()
 * @method LoginAttempt[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
class LoginAttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginAttempt::class);
    }

    public function resetAttempts(string $email): void
    {
        $loginAttempt = $this->findOneBy(['email' => $email]);

        if ($loginAttempt) {
            $loginAttempt->setAttempts(0);
            $loginAttempt->setLockedUntil(null);
            $entityManager = $this->getEntityManager();
            $entityManager->persist($loginAttempt);
            $entityManager->flush();
        }
    }

    public function save(LoginAttempt $loginAttempt): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($loginAttempt);
        $entityManager->flush();
    }
}