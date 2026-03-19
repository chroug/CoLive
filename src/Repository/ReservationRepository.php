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
     * Cherche s'il existe une réservation ACTIVE (non annulée) qui chevauche les dates
     */
    /**
     * Cherche si un utilisateur spécifique a déjà une demande sur ces dates
     */
    public function findUserOverlappingReservations(Announce $announce, $user, \DateTimeInterface $start, ?\DateTimeInterface $end): array
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.announce = :announce')
            ->andWhere('r.locataire = :user')
            ->andWhere('r.statut != :cancelled')
            ->setParameter('announce', $announce)
            ->setParameter('user', $user)
            ->setParameter('cancelled', 'CANCELLED')
            ->setParameter('start', $start);

        $qb->andWhere('r.dateFin IS NULL OR r.dateFin > :start');

        if ($end !== null) {
            $qb->andWhere('r.dateDebut < :end')
                ->setParameter('end', $end);
        }

        return $qb->getQuery()->getResult();
    }
}
