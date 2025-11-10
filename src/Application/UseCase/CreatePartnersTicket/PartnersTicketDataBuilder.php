<?php

declare(strict_types=1);

namespace MobilityWork\Application\UseCase\CreatePartnersTicket;

use MobilityWork\Application\Service\Finder\LanguageFinder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;

class PartnersTicketDataBuilder
{
    public function __construct(
        private readonly LanguageFinder $languageFinder,
    ) {
    }

    public function build(CreatePartnersTicketCommand $command): TicketCreationDataDTO
    {
        /** @var ?Language $language */
        $language = $this->languageFinder->findById($command->request->languageId);

        $customFields = [];
        $customFields[ZendeskCustomFields::TICKET_TYPE] = 'partner';

        if (null !== $language) {
            $customFields[ZendeskCustomFields::LANGUAGE_NAME] = $language->getName();
        }

        $user = new UserDTO(
            email: $command->request->email,
            name: $command->request->firstName.' '.strtoupper($command->request->lastName),
            phone: $command->request->phoneNumber
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
}
