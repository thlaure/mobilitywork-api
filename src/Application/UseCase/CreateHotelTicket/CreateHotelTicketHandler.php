<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateHotelTicket;

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
        $customFields['80924888'] = 'hotel';
        $customFields['80918668'] = $command->request->hotelName;
        $customFields['80918648'] = $command->request->city;
        $customFields['80918708'] = $command->request->language->getName();

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
