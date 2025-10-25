<?php

declare(strict_types=1);

namespace MobilityWork\Service;

use MobilityWork\Domain\Model\Ticket\CreateHotelTicketRequest;
use MobilityWork\Repository\ReservationRepository;

class CreateHotelTicketService
{
    public function __construct(
        private readonly ZendeskService $zendeskService,
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    public function __invoke(CreateHotelTicketRequest $request): void
    {
        $customFields = [];
        $customFields['80924888'] = 'hotel';
        $customFields['80918668'] = $request->hotelName;
        $customFields['80918648'] = $request->city;
        $customFields['80918708'] = $request->language->getName();

        $userId = $this->zendeskService->createOrUpdateUser([
            'email' => $request->email,
            'name' => $request->firstName.' '.strtoupper($request->lastName),
            'phone' => $request->phoneNumber,
            'role' => 'end-user',
            'user_fields' => ['website' => $request->website],
        ]);

        $this->zendeskService->createTicket([
            'requester_id' => $userId,
            'subject' => strlen($request->message) > 50 ? substr($request->message, 0, 50).'...' : $request->message,
            'comment' => [
                'body' => $request->message,
            ],
            'priority' => 'normal',
            'type' => 'question',
            'status' => 'new',
            'custom_fields' => $customFields,
        ]);
    }
}
