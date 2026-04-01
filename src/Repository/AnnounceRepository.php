<?php

namespace App\Repository;

use App\Entity\Announce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Announce>
 */
class AnnounceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announce::class);
    }

    public function findByFilters(?string $location, ?string $type, ?string $dateStart, ?string $dateEnd, ?\App\Entity\User $user = null): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->andWhere('a.isValidated = :valid')
            ->setParameter('valid', true);

        if ($location) {
            $qb->andWhere('a.ville LIKE :location OR a.titre LIKE :location')
                ->setParameter('location', '%' . $location . '%');
        }
        if ($type && $type !== 'all') {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }
        if ($dateStart) {
            $qb->andWhere('a.disponibilite_debut <= :dateStart')
                ->setParameter('dateStart', $dateStart);
        }
        if ($dateEnd) {
            $qb->andWhere('a.disponibilite_fin >= :dateEnd')
                ->setParameter('dateEnd', $dateEnd);
        }

        if (empty($location) && $user && method_exists($user, 'getVille') && $user->getVille()) {

            $qb->addSelect('(CASE WHEN a.ville LIKE :userCity THEN 0 ELSE 1 END) AS HIDDEN sortPriority')
                ->setParameter('userCity', '%' . $user->getVille() . '%')
                ->orderBy('sortPriority', 'ASC')
                ->addOrderBy('a.dateCreation', 'DESC');

        } else {
            $qb->orderBy('a.dateCreation', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }
}
