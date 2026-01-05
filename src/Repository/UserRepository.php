<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    // C'est ici que la magie opère :
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    public function findAllExcept(User $user): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.id != :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getResult();
    }
}
