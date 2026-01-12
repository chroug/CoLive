<?php

namespace App\Repository;

use App\Entity\Announce;
use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Cette fonction cherche s'il existe DÉJÀ une réservation
     * qui chevauche les dates choisies par l'utilisateur.
     * * @return Reservation[]
     */
    public function findOverlappingReservations(Announce $announce, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.announce = :announce')

            ->andWhere('r.statut != :cancelled')

            ->andWhere('r.dateDebut < :end')
            ->andWhere('r.dateFin > :start')

            // 4. On injecte les valeurs
            ->setParameter('announce', $announce)
            ->setParameter('cancelled', 'CANCELLED')
            ->setParameter('start', $start)
            ->setParameter('end', $end)

            ->getQuery()
            ->getResult();
    }
}
