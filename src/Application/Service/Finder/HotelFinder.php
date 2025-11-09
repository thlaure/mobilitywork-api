<?php

declare(strict_types=1);

namespace MobilityWork\Application\Service\Finder;

use MobilityWork\Domain\Model\Entity\Hotel;
use MobilityWork\Domain\Port\Out\HotelRepositoryPort;

final class HotelFinder
{
    public function __construct(
        private readonly HotelRepositoryPort $hotelRepository,
    ) {
    }

    public function findById(?int $hotelId): ?Hotel
    {
        if (null === $hotelId) {
            return null;
        }

        return $this->hotelRepository->findOneById($hotelId);
    }
}
