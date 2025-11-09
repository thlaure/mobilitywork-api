<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateHotelTicket;

use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateHotelTicketHandler
{
    public function __construct(
        private readonly TicketCreatorPort $ticketCreator,
        private readonly HotelTicketDataBuilder $hotelTicketDataBuilder,
    ) {
    }

    public function __invoke(CreateHotelTicketCommand $command): void
    {
        $ticketData = $this->hotelTicketDataBuilder->build($command);

        $userId = $this->ticketCreator->createOrUpdateUser($ticketData->user);

        $updatedTicket = $ticketData->ticket->withCustomField('requester_id', $userId);

        $this->ticketCreator->createTicket($updatedTicket);
    }
}
