<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateHotelTicket;

use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use MobilityWork\Repository\ReservationRepository;
use MobilityWork\Service\ZendeskService;

class CreateHotelTicketHandler
{
    public function __construct(
        private readonly ZendeskService $zendeskService,
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    public function __invoke(CreateHotelTicketCommand $command): void
    {
        $customFields = [];
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'hotel';
        $customFields[ZendeskCustomFields::HOTEL_NAME] = $command->request->hotelName;
        $customFields[ZendeskCustomFields::CITY] = $command->request->city;
        $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $command->request->language->getName();

        $userId = $this->zendeskService->createOrUpdateUser([
            'email' => $command->request->email,
            'name' => $command->request->firstName.' '.strtoupper($command->request->lastName),
            'phone' => $command->request->phoneNumber,
            'role' => 'end-user',
            'user_fields' => ['website' => $command->request->website],
        ]);

        $this->zendeskService->createTicket([
            'requester_id' => $userId,
            'subject' => strlen($command->request->message) > 50 ? substr($command->request->message, 0, 50).'...' : $command->request->message,
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
