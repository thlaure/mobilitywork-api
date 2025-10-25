<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateHotelTicket;

use MobilityWork\Domain\Model\Ticket\CreateHotelTicketRequest;

class CreateHotelTicketCommand
{
    public function __construct(
        public readonly CreateHotelTicketRequest $request,
    ) {
    }
}
