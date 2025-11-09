<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateCustomerTicket;

use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;

final class CustomerTicketDataDTO
{
    public function __construct(
        public readonly UserDTO $user,
        public readonly TicketDTO $ticket,
    ) {
    }
}
