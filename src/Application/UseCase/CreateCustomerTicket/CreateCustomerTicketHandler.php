<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateCustomerTicket;

use MobilityWork\Repository\ReservationRepository;
use MobilityWork\Service\ZendeskService;

class CreateCustomerTicketHandler
{
    public function __construct(
        private readonly ZendeskService $zendeskService,
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    public function __invoke(CreateCustomerTicketCommand $command): void
    {
        /** @var ?Reservation $reservation */
        $reservation = null;

        if (!empty($command->request->reservationNumber)) {
            $reservation = $this->reservationRepository->getByRef($command->request->reservationNumber);

            if (null !== $reservation) {
                $hotel = $command->request->hotel;
                if (null === $hotel) {
                    $hotel = $reservation->getHotel();
                }
            }
        }

        $customFields = [];
        $customFields['80924888'] = 'customer';
        $customFields['80531327'] = $command->request->reservationNumber;

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

        $customFields['80918708'] = $command->request->language->getName();

        $userId = $this->zendeskService->createOrUpdateUser([
            'email' => $command->request->email,
            'name' => $command->request->firstName.' '.strtoupper($command->request->lastName),
            'phone' => !empty($command->request->phoneNumber) ? $command->request->phoneNumber : (null !== $reservation ? $reservation->getCustomer()->getSimplePhoneNumber() : ''),
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
