<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Ticket;

use MobilityWork\Domain\Model\Entity\DomainConfig;
use MobilityWork\Domain\Model\Entity\Hotel;
use MobilityWork\Domain\Model\Entity\Language;

class CreateCustomerTicketRequest
{
    public function __construct(
        public readonly string $gender,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $phoneNumber,
        public readonly string $email,
        public readonly string $message,
        public readonly string $reservationNumber,
        public readonly Hotel $hotel,
        public readonly Language $language,
        public readonly DomainConfig $domainConfig,
    ) {
    }
}
