<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateHotelTicket;

use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Port\Out\LanguageRepositoryPort;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;

final class HotelTicketDataBuilder
{
    public function __construct(
        private readonly LanguageRepositoryPort $languageRepository,
    ) {
    }

    public function build(CreateHotelTicketCommand $command): TicketCreationDataDTO
    {
        /** @var ?Language $language */
        $language = $this->findLanguage($command->request->languageId);

        $customFields = [];
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'hotel';
        $customFields[ZendeskCustomFields::HOTEL_NAME] = $command->request->hotelName;
        $customFields[ZendeskCustomFields::CITY] = $command->request->city;

        if (null !== $language) {
            $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $language->getName();
        }

        $user = new UserDTO(
            email: $command->request->email,
            name: $command->request->firstName.' '.strtoupper($command->request->lastName),
            phone: $command->request->phoneNumber,
            userFields: [
                'website' => $command->request->website,
            ]
        );

        $ticket = new TicketDTO(
            subject: strlen($command->request->message) > 50 ? substr($command->request->message, 0, 50).'...' : $command->request->message,
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
}
