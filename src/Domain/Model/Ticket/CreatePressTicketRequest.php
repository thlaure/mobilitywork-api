<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Ticket;

class CreatePressTicketRequest
{
    public function __construct(
        public readonly string $gender,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $country,
        public readonly string $phoneNumber,
        public readonly string $email,
        public readonly string $city,
        public readonly string $media,
        public readonly string $subject,
        public readonly string $message,
        public readonly string $reservationNumber,
        public readonly Language $language,
        public readonly DomainConfig $domainConfig,
    ) {
    }
}