<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Ticket;

use MobilityWork\Entity\DomainConfig;
use MobilityWork\Entity\Language;

class CreatePartnersTicketRequest
{
    public function __construct(
        public readonly string $gender,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $phoneNumber,
        public readonly string $email,
        public readonly string $message,
        public readonly Language $language,
        public readonly DomainConfig $domainConfig,
    ) {
    }
}
