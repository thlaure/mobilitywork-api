<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Ticket;

use MobilityWork\Domain\Model\Entity\Language;

class CreateHotelTicketRequest
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $country,
        public readonly string $phoneNumber,
        public readonly string $email,
        public readonly string $city,
        public readonly string $website,
        public readonly string $hotelName,
        public readonly string $subject,
        public readonly string $message,
        public readonly Language $language,
    ) {
    }
}
