<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateCustomerTicket;

use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Entity\Hotel;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Model\Entity\Reservation;
use MobilityWork\Domain\Port\Out\HotelRepositoryPort;
use MobilityWork\Domain\Port\Out\LanguageRepositoryPort;
use MobilityWork\Domain\Port\Out\ReservationRepositoryPort;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;

final class CustomerTicketDataBuilder
{
    public function __construct(
        private readonly ReservationRepositoryPort $reservationRepository,
        private readonly HotelRepositoryPort $hotelRepository,
        private readonly LanguageRepositoryPort $languageRepository,
    ) {
    }

    public function build(CreateCustomerTicketCommand $command): TicketCreationDataDTO
    {
        /** @var ?Reservation $reservation */
        $reservation = $this->findReservation($command->request->reservationNumber);
        /** @var ?Hotel $hotel */
        $hotel = $this->findHotel($command->request->hotelId);
        /** @var ?Language $language */
        $language = $this->findLanguage($command->request->languageId);

        $customFields = $this->buildCustomFields($reservation, $hotel, $language);

        $user = new UserDTO(
            email: $command->request->email,
            name: $command->request->firstName.' '.strtoupper($command->request->lastName),
            phone: !empty($command->request->phoneNumber) ? $command->request->phoneNumber : (null !== $reservation ? $reservation->getCustomer()->getSimplePhoneNumber() : '')
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

    private function findLanguage(?int $languageId): ?Language
    {
        if (null === $languageId) {
            return null;
        }

        return $this->languageRepository->findOneById($languageId);
    }

    private function findHotel(?int $hotelId): ?Hotel
    {
        if (null === $hotelId) {
            return null;
        }

        return $this->hotelRepository->findOneById($hotelId);
    }

    private function findReservation(?string $reservationReference): ?Reservation
    {
        if (!empty($reservationReference)) {
            return null;
        }

        return $this->reservationRepository->getByRef($reservationReference);
    }

    /**
     * @return array<string,mixed>
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
            $roomName = $reservation->getRoom()->getName().' ('.$reservation->getRoom()->getType().')';
            $customFields[ZendeskCustomFields::ROOM_NAME] = $roomName;
            $customFields[ZendeskCustomFields::BOOKED_DATE] = $reservation->getBookedDate()->format('Y-m-d');
            $customFields[ZendeskCustomFields::ROOM_PRICE] = $reservation->getRoomPrice().' '.$reservation->getHotel()->getCurrency()->getCode();
            $customFields[ZendeskCustomFields::BOOKED_TIME] = $reservation->getBookedStartTime()->format('H:i').' - '.$reservation->getBookedEndTime()->format('H:i');
        }

        if (null !== $language) {
            $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $language->getName();
        }

        return $customFields;
    }
}
