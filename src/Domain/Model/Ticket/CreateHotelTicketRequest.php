<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Ticket;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateHotelTicketRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $firstName,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $lastName,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $country,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 50)]
        public readonly string $phoneNumber,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        #[Assert\Email]
        public readonly string $email,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $city,
        public readonly string $website,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $hotelName,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $subject,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 5000)]
        public readonly string $message,
        #[Assert\Positive]
        public readonly ?int $languageId = null,
    ) {
    }
}
