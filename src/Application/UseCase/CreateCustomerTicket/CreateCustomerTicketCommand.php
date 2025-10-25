<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateCustomerTicket;

use MobilityWork\Domain\Model\Ticket\CreateCustomerTicketRequest;

class CreateCustomerTicketCommand
{
    public function __construct(
        public readonly CreateCustomerTicketRequest $request,
    ) {
    }
}
