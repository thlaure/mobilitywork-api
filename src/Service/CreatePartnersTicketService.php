<?php

declare(strict_types=1);

namespace MobilityWork\Service;

use MobilityWork\Domain\Model\Ticket\CreatePartnersTicketRequest;
use MobilityWork\Repository\ReservationRepository;

class CreatePartnersTicketService
{
    public function __construct(
        private readonly ZendeskService $zendeskService,
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    public function __invoke(CreatePartnersTicketRequest $request): void
    {
        $customFields = [];
        $customFields['80924888'] = 'partner';
        $customFields['80918708'] = $request->language->getName();

        $userId = $this->zendeskService->createOrUpdateUser([
            'email' => $request->email,
            'name' => $request->firstName.' '.strtoupper($request->lastName),
            'phone' => $request->phoneNumber,
            'role' => 'end-user',
        ]);

        $this->zendeskService->createTicket([
            'requester_id' => $userId,
            'subject' => 50 < strlen($request->message) ? substr($request->message, 0, 50).'...' : $request->message,
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
