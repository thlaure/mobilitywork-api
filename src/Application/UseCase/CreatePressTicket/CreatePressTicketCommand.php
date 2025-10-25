<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreatePressTicket;

use MobilityWork\Domain\Model\Ticket\CreatePressTicketRequest;

class CreatePressTicketCommand
{
    public function __construct(
        public readonly CreatePressTicketRequest $request,
    ) {
    }
}
