<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreatePartnersTicket;

use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use MobilityWork\Repository\ReservationRepository;
use MobilityWork\Service\ZendeskService;

class CreatePartnersTicketHandler
{
    public function __construct(
        private readonly ZendeskService $zendeskService,
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    public function __invoke(CreatePartnersTicketCommand $command): void
    {
        $customFields = [];
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'partner';
        $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $command->request->language->getName();

        $userId = $this->zendeskService->createOrUpdateUser([
            'email' => $command->request->email,
            'name' => $command->request->firstName.' '.strtoupper($command->request->lastName),
            'phone' => $command->request->phoneNumber,
            'role' => 'end-user',
        ]);

        $this->zendeskService->createTicket([
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
