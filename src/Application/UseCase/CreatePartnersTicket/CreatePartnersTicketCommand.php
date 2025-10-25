<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreatePartnersTicket;

use MobilityWork\Domain\Model\Ticket\CreatePartnersTicketRequest;

class CreatePartnersTicketCommand
{
    public function __construct(public readonly CreatePartnersTicketRequest $request)
    {
    }
}
