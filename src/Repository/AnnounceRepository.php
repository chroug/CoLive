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

    public function findByFilters(?string $location, ?string $type, ?string $dateStart, ?string $dateEnd): array
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
        $qb->orderBy('a.dateCreation', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
