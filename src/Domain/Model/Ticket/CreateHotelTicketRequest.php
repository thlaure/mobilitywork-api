<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Ticket;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateHotelTicketRequest
{
    public function __construct(
        #[OA\Property(example: 'John')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $firstName,
        #[OA\Property(example: 'Doe')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $lastName,
        #[OA\Property(example: 'France')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $country,
        #[OA\Property(example: '0606060606')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 50)]
        public readonly string $phoneNumber,
        #[OA\Property(example: 'john.doe@mail.com')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        #[Assert\Email]
        public readonly string $email,
        #[OA\Property(example: 'Paris')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $city,
        #[OA\Property(example: 'https://www.google.com')]
        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $website,
        #[OA\Property(example: 'My Hotel')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $hotelName,
        #[OA\Property(example: 'My subject')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $subject,
        #[OA\Property(example: 'My message')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 5000)]
        public readonly string $message,
        #[OA\Property(example: 1)]
        #[Assert\Positive]
        public readonly ?int $languageId = null,
    ) {
    }
}
