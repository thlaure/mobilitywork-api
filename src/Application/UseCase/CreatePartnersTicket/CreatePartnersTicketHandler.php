<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreatePartnersTicket;

use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use MobilityWork\Repository\ReservationRepository;

class CreatePartnersTicketHandler
{
    public function __construct(
        private readonly TicketCreatorPort $ticketCreator,
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    public function __invoke(CreatePartnersTicketCommand $command): void
    {
        $customFields = [];
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'partner';
        $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $command->request->language->getName();

        $userId = $this->ticketCreator->createOrUpdateUser([
            'email' => $command->request->email,
            'name' => $command->request->firstName.' '.strtoupper($command->request->lastName),
            'phone' => $command->request->phoneNumber,
            'role' => 'end-user',
        ]);

        $this->ticketCreator->createTicket([
            'requester_id' => $userId,
            'subject' => 50 < strlen($command->request->message) ? substr($command->request->message, 0, 50).'...' : $command->request->message,
            'comment' => [
                'body' => $command->request->message,
            ],
            'priority' => 'normal',
            'type' => 'question',
            'status' => 'new',
            'custom_fields' => $customFields,
        ]);
    }
}
