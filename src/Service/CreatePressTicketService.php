<?php

declare(strict_types=1);

namespace MobilityWork\Service;

use MobilityWork\Domain\Model\Ticket\CreatePressTicketRequest;
use MobilityWork\Repository\ReservationRepository;

class CreatePressTicketService
{
    public function __construct(
        private readonly ZendeskService $zendeskService,
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    public function __invoke(CreatePressTicketRequest $request): bool
    {
        $customFields = [];
        $customFields['80924888'] = 'press';
        $customFields['80918648'] = $request->city;
        $customFields['80918708'] = $request->language->getName();

        $client = new ZendeskAPI($this->getServiceManager()->get('Config')['zendesk']['subdomain']);
        $client->setAuth(
            'basic',
            [
                'username' => $this->getServiceManager()->get('Config')['zendesk']['username'],
                'token' => $this->getServiceManager()->get('Config')['zendesk']['token'],
            ]
        );

        $response = $client->users()->createOrUpdate(
            [
                'email' => $request->email,
                'name' => $request->firstName.' '.strtoupper($request->lastName),
                'phone' => $request->phoneNumber,
                'role' => 'end-user',
                'user_fields' => ['press_media' => $request->media],
            ]
        );

        try {
            $client->tickets()->create(
                [
                    'requester_id' => $response->user->id,
                    'subject' => 50 < strlen($request->message) ? substr($request->message, 0, 50).'...' : $request->message,
                    'comment' => [
                        'body' => $request->message,
                    ],
                    'priority' => 'normal',
                    'type' => 'question',
                    'status' => 'new',
                    'custom_fields' => $customFields,
                ]
            );
        } catch (\Exception $e) {
            $this->getLogger()->addError(var_export($response->user->id, true));
        }

        return true;
    }
}
