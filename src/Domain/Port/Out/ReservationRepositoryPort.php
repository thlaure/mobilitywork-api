<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Port\Out;

use MobilityWork\Domain\Model\Entity\Reservation;

interface ReservationRepositoryPort
{
    public function getByRef(string $reference): ?Reservation;
}
