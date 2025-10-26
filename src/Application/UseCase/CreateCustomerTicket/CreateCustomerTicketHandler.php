<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateCustomerTicket;

use MobilityWork\Domain\Model\Entity\Reservation;
use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use MobilityWork\Infrastructure\Persistence\Doctrine\Repository\ReservationRepository;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;

class CreateCustomerTicketHandler
{
    public function __construct(
        private readonly TicketCreatorPort $ticketCreator,
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
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'customer';
        $customFields[ZendeskCustomFields::RESERVATION_NUMBER] = $command->request->reservationNumber;

        if (null !== $hotel) {
            $hotelContact = $hotel->getMainContact();
            $customFields[ZendeskCustomFields::HOTEL_CONTACT_EMAIL] = $hotelContact?->getEmail();
            $customFields[ZendeskCustomFields::HOTEL_NAME] = $hotel->getName();
            $customFields[ZendeskCustomFields::HOTEL_ADDRESS] = $hotel->getAddress();
        }

        if (null !== $reservation) {
            $roomName = $reservation->getRoom()->getName().' ('.$reservation->getRoom()->getType().')';
            $customFields[ZendeskCustomFields::ROOM_NAME] = $roomName;
            $customFields[ZendeskCustomFields::BOOKED_DATE] = $reservation->getBookedDate()->format('Y-m-d');
            $customFields[ZendeskCustomFields::ROOM_PRICE] = $reservation->getRoomPrice().' '.$reservation->getHotel()->getCurrency()->getCode();
            $customFields[ZendeskCustomFields::BOOKED_TIME] = $reservation->getBookedStartTime()->format('H:i').' - '.$reservation->getBookedEndTime()->format('H:i');
        }

        $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $command->request->language->getName();

        $userId = $this->ticketCreator->createOrUpdateUser([
            'email' => $command->request->email,
            'name' => $command->request->firstName.' '.strtoupper($command->request->lastName),
            'phone' => !empty($command->request->phoneNumber) ? $command->request->phoneNumber : (null !== $reservation ? $reservation->getCustomer()->getSimplePhoneNumber() : ''),
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
