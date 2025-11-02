<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateHotelTicket;

final class HotelTicketDataDTO
{
    /**
     * @param array<string, mixed> $user
     * @param array<string, mixed> $ticket
     */
    public function __construct(
        public readonly array $user,
        public readonly array $ticket,
    ) {
    }
}
