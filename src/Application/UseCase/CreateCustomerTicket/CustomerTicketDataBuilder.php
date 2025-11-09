<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateCustomerTicket;

use MobilityWork\Application\Service\Finder\HotelFinder;
use MobilityWork\Application\Service\Finder\LanguageFinder;
use MobilityWork\Application\Service\Finder\ReservationFinder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Entity\Hotel;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Model\Entity\Reservation;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;

final class CustomerTicketDataBuilder
{
    public function __construct(
        private readonly ReservationFinder $reservationFinder,
        private readonly HotelFinder $hotelFinder,
        private readonly LanguageFinder $languageFinder,
    ) {
    }

    public function build(CreateCustomerTicketCommand $command): TicketCreationDataDTO
    {
        /** @var ?Reservation $reservation */
        $reservation = $this->reservationFinder->getByRef($command->request->reservationNumber);
        /** @var ?Hotel $hotel */
        $hotel = $this->hotelFinder->findById($command->request->hotelId);
        /** @var ?Language $language */
        $language = $this->languageFinder->findById($command->request->languageId);

        $customFields = $this->buildCustomFields($reservation, $hotel, $language);

        $user = new UserDTO(
            email: $command->request->email,
            name: $command->request->firstName.' '.strtoupper($command->request->lastName),
            phone: !empty($command->request->phoneNumber)
                ? $command->request->phoneNumber
                : $reservation?->getCustomer()?->getSimplePhoneNumber()
        );

        $ticket = new TicketDTO(
            subject: 50 < strlen($command->request->message) ? substr($command->request->message, 0, 50).'...' : $command->request->message,
            comment: [
                'body' => $command->request->message,
            ],
            customFields: $customFields
        );

        return new TicketCreationDataDTO($user, $ticket);
    }

    /**
     * @return array<string|int,mixed>
     */
    private function buildCustomFields(
        ?Reservation $reservation,
        ?Hotel $hotel,
        ?Language $language,
    ): array {
        $customFields = [];
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'customer';
        $customFields[ZendeskCustomFields::RESERVATION_NUMBER] = $reservation?->getReference();

        if (null !== $hotel) {
            $customFields[ZendeskCustomFields::HOTEL_CONTACT_EMAIL] = $hotel->getMainContact()?->getEmail();
            $customFields[ZendeskCustomFields::HOTEL_NAME] = $hotel->getName();
            $customFields[ZendeskCustomFields::HOTEL_ADDRESS] = $hotel->getAddress();
        }

        if (null !== $reservation) {
            $roomName = null === $reservation->getRoom() ? null : $reservation->getRoom()->getName().' ('.$reservation->getRoom()->getType().')';
            $customFields[ZendeskCustomFields::ROOM_NAME] = $roomName;
            $customFields[ZendeskCustomFields::BOOKED_DATE] = $reservation->getBookedDate()?->format('Y-m-d');
            $customFields[ZendeskCustomFields::ROOM_PRICE] = $reservation->getRoomPrice().' '.$reservation->getHotel()?->getCurrency()?->getCode();
            $customFields[ZendeskCustomFields::BOOKED_TIME] = $reservation->getBookedStartTime()?->format('H:i').' - '.$reservation->getBookedEndTime()?->format('H:i');
        }

        if (null !== $language) {
            $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $language->getName();
        }

        return $customFields;
    }
}
