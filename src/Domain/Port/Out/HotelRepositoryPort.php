<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Port\Out;

use MobilityWork\Domain\Model\Entity\Hotel;

interface HotelRepositoryPort
{
    public function findOneById(int $id): ?Hotel;
}
