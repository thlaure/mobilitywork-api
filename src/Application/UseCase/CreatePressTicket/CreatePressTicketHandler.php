<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreatePressTicket;

use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;

class CreatePressTicketHandler
{
    public function __construct(
        private readonly TicketCreatorPort $ticketCreator,
    ) {
    }

    public function __invoke(CreatePressTicketCommand $command): void
    {
        $customFields = [];
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'press';
        $customFields[ZendeskCustomFields::CITY] = $command->request->city;
        $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $command->request->language->getName();

        $userId = $this->ticketCreator->createOrUpdateUser([
            'email' => $command->request->email,
            'name' => $command->request->firstName.' '.strtoupper($command->request->lastName),
            'phone' => $command->request->phoneNumber,
            'role' => 'end-user',
            'user_fields' => ['press_media' => $command->request->media],
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
