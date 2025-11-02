<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreatePressTicket;

use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Port\Out\LanguageRepositoryPort;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;

final class PressTicketDataBuilder
{
    public function __construct(
        private readonly LanguageRepositoryPort $languageRepository,
    ) {
    }

    public function build(CreatePressTicketCommand $command): PressTicketDataDTO
    {
        /** @var ?Language $language */
        $language = null;
        if (null !== $command->request->languageId) {
            $language = $this->languageRepository->findOneById($command->request->languageId);
        }

        $customFields = [];
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'press';
        $customFields[ZendeskCustomFields::CITY] = $command->request->city;

        if (null !== $language) {
            $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $language->getName();
        }

        $user = [
            'email' => $command->request->email,
            'name' => $command->request->firstName.' '.strtoupper($command->request->lastName),
            'phone' => $command->request->phoneNumber,
            'role' => 'end-user',
            'user_fields' => ['press_media' => $command->request->media],
        ];

        $ticket = [
            'subject' => 50 < strlen($command->request->message) ? substr($command->request->message, 0, 50).'...' : $command->request->message,
            'comment' => [
                'body' => $command->request->message,
            ],
            'priority' => 'normal',
            'type' => 'question',
            'status' => 'new',
            'custom_fields' => $customFields,
        ];

        return new PressTicketDataDTO($user, $ticket);
    }
}
