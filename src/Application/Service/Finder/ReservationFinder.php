<?php

declare(strict_types=1);

namespace MobilityWork\Application\Service\Finder;

use MobilityWork\Domain\Model\Entity\Reservation;
use MobilityWork\Domain\Port\Out\ReservationRepositoryPort;

class ReservationFinder
{
    public function __construct(
        private readonly ReservationRepositoryPort $reservationRepository,
    ) {
    }

    public function getByRef(?string $reservationReference): ?Reservation
    {
        if (empty($reservationReference)) {
            return null;
        }

        return $this->reservationRepository->getByRef($reservationReference);
    }
}
