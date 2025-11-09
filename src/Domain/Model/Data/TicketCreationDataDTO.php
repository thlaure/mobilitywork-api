<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Data;

final class TicketCreationDataDTO
{
    public function __construct(
        public readonly UserDTO $user,
        public readonly TicketDTO $ticket,
    ) {
    }
}
