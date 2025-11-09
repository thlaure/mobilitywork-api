<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreatePartnersTicket;

use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatePartnersTicketHandler
{
    public function __construct(
        private readonly TicketCreatorPort $ticketCreator,
        private readonly PartnersTicketDataBuilder $partnersTicketDataBuilder,
    ) {
    }

    public function __invoke(CreatePartnersTicketCommand $command): void
    {
        $ticketData = $this->partnersTicketDataBuilder->build($command);

        $userId = $this->ticketCreator->createOrUpdateUser($ticketData->user);

        $updatedTicket = $ticketData->ticket->withCustomField('requester_id', $userId);

        $this->ticketCreator->createTicket($updatedTicket);
    }
}
