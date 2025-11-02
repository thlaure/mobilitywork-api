<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreatePressTicket;

use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatePressTicketHandler
{
    public function __construct(
        private readonly TicketCreatorPort $ticketCreator,
        private readonly PressTicketDataBuilder $pressTicketDataBuilder,
    ) {
    }

    public function __invoke(CreatePressTicketCommand $command): void
    {
        $ticketData = $this->pressTicketDataBuilder->build($command);

        $userId = $this->ticketCreator->createOrUpdateUser($ticketData->user);

        $this->ticketCreator->createTicket($ticketData->ticket + ['requester_id' => $userId]);
    }
}
