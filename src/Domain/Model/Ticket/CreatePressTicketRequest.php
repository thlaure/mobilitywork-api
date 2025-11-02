<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Ticket;

use Symfony\Component\Validator\Constraints as Assert;

final class CreatePressTicketRequest
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
        public readonly string $email,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $city,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $media,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $subject,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 5000)]
        public readonly string $message,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $reservationNumber,
        #[Assert\Positive]
        public readonly ?int $languageId = null,
    ) {
    }
}
