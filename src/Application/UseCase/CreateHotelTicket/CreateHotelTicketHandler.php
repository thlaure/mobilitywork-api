<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreateHotelTicket;

use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Port\Out\LanguageRepositoryPort;
use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateHotelTicketHandler
{
    public function __construct(
        private readonly TicketCreatorPort $ticketCreator,
        private readonly LanguageRepositoryPort $languageRepository,
    ) {
    }

    public function __invoke(CreateHotelTicketCommand $command): void
    {
        /** @var ?Language $language */
        $language = null;

        if (null !== $command->request->languageId) {
            $language = $this->languageRepository->findOneById($command->request->languageId);
        }

        $customFields = [];
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'hotel';
        $customFields[ZendeskCustomFields::HOTEL_NAME] = $command->request->hotelName;
        $customFields[ZendeskCustomFields::CITY] = $command->request->city;

        if (null !== $language) {
            $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $language->getName();
        }

        $userId = $this->ticketCreator->createOrUpdateUser([
            'email' => $command->request->email,
            'name' => $command->request->firstName.' '.strtoupper($command->request->lastName),
            'phone' => $command->request->phoneNumber,
            'role' => 'end-user',
            'user_fields' => ['website' => $command->request->website],
        ]);

        $this->ticketCreator->createTicket([
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
