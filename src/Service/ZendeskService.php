<?php

namespace MobilityWork\Service;

use MobilityWork\Domain\Model\Ticket\CreateCustomerTicketRequest;
use MobilityWork\Domain\Model\Ticket\CreateHotelTicketRequest;
use MobilityWork\Domain\Model\Ticket\CreatePartnersTicketRequest;
use MobilityWork\Domain\Model\Ticket\CreatePressTicketRequest;
use MobilityWork\Repository\ReservationRepository;
use Zendesk\API\HttpClient as ZendeskAPI;

class ZendeskService extends AbstractService
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    public function createCustomerTicket(CreateCustomerTicketRequest $request): bool
    {
        /** @var ?Reservation $reservation */
        $reservation = null;

        if (!empty($request->reservationNumber)) {
            $reservation = $this->reservationRepository->getByRef($request->reservationNumber);

            if (null !== $reservation) {
                $hotel = $request->hotel;
                if (null === $hotel) {
                    $hotel = $reservation->getHotel();
                }
            }
        }

        $customFields = [];
        $customFields['80924888'] = 'customer';
        $customFields['80531327'] = $request->reservationNumber;

        if (null !== $hotel) {
            $hotelContact = $this->getServiceManager()->get('service.hotel_contacts')->getMainHotelContact($hotel);
            $customFields['80531267'] = $hotelContact?->getEmail();
            $customFields['80918668'] = $hotel->getName();
            $customFields['80918648'] = $hotel->getAddress();
        }

        if (null !== $reservation) {
            $roomName = $reservation->getRoom()->getName().' ('.$reservation->getRoom()->getType().')';
            $customFields['80531287'] = $roomName;
            $customFields['80531307'] = $reservation->getBookedDate()->format('Y-m-d');
            $customFields['80924568'] = $reservation->getRoomPrice().' '.$reservation->getHotel()->getCurrency()->getCode();
            $customFields['80918728'] = $reservation->getBookedStartTime()->format('H:i').' - '.$reservation->getBookedEndTime()->format('H:i');
        }

        $customFields['80918708'] = $request->language->getName();

        $client = new ZendeskAPI($this->getServiceManager()->get('Config')['zendesk']['subdomain']);
        $client->setAuth(
            'basic',
            ['username' => $this->getServiceManager()->get('Config')['zendesk']['username'], 'token' => $this->getServiceManager()->get('Config')['zendesk']['token']]
        );

        $response = $client->users()->createOrUpdate(
            [
                'email' => $request->email,
                'name' => $request->firstName.' '.strtoupper($request->lastName),
                'phone' => !empty($request->phoneNumber) ? $request->phoneNumber : (null !== $reservation ? $reservation->getCustomer()->getSimplePhoneNumber() : ''),
                'role' => 'end-user',
            ]
        );

        $client->tickets()->create(
            [
                'requester_id' => $response->user->id,
                'subject' => 50 < strlen($request->message) ? substr($request->message, 0, 50).'...' : $request->message,
                'comment' => [
                    'body'  => $request->message,
                ],
                'priority'      => 'normal',
                'type'          => 'question',
                'status'        => 'new',
                'custom_fields' => $customFields,
            ]
        );

        return true;
    }

    public function createHotelTicket(CreateHotelTicketRequest $request): bool
    {
        $customFields = [];
        $customFields['80924888'] = 'hotel';
        $customFields['80918668'] = $request->hotelName;
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
                'user_fields' => ['website' => $request->website],
            ]
        );

        $client->tickets()->create(
            [
                'requester_id' => $response->user->id,
                'subject' => strlen($request->message) > 50 ? substr($request->message, 0, 50).'...' : $request->message,
                'comment' => [
                    'body' => $request->message,
                ],
                'priority' => 'normal',
                'type' => 'question',
                'status' => 'new',
                'custom_fields' => $customFields,
            ]
        );

        return true;
    }

    public function createPressTicket(CreatePressTicketRequest $request): bool
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

    public function createPartnersTicket(CreatePartnersTicketRequest $request): bool
    {
        $customFields = [];
        $customFields['80924888'] = 'partner';
        $customFields['80918708'] = $request->language->getName();

        $client = new ZendeskAPI($this->getServiceManager()->get('Config')['zendesk']['subdomain']);
        $client->setAuth(
            'basic',
            [
                'username' => $this->getServiceManager()->get('Config')['zendesk']['username'],
                'token' => $this->getServiceManager()->get('Config')['zendesk']['token'],
            ]
        );

        $response = $client->users()->createOrUpdate([
            'email' => $request->email,
            'name' => $request->firstName.' '.strtoupper($request->lastName),
            'phone' => $request->phoneNumber,
            'role' => 'end-user',
        ]);

        $client->tickets()->create([
            'requester_id' => $response->user->id,
            'subject' => 50 < strlen($request->message) ? substr($request->message, 0, 50).'...' : $request->message,
            'comment' => [
                'body' => $request->message,
            ],
            'priority' => 'normal',
            'type' => 'question',
            'status' => 'new',
            'custom_fields' => $customFields,
        ]);

        return true;
    }
}