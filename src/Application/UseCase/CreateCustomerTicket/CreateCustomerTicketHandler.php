<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateCustomerTicket;

use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateCustomerTicketHandler
{
    public function __construct(
        private readonly TicketCreatorPort $ticketCreator,
        private readonly CustomerTicketDataBuilder $customerTicketDataBuilder,
    ) {
    }

    public function __invoke(CreateCustomerTicketCommand $command): void
    {
        $ticketData = $this->customerTicketDataBuilder->build($command);

        $userId = $this->ticketCreator->createOrUpdateUser($ticketData->user);

        $updatedTicket = $ticketData->ticket->withCustomField('requester_id', $userId);

        $this->ticketCreator->createTicket($updatedTicket);
    }
}
