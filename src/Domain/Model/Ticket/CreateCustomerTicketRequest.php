<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Ticket;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateCustomerTicketRequest
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
        #[OA\Property(example: '0606060606')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 50)]
        public readonly string $phoneNumber,
        #[OA\Property(example: 'john.doe@mail.com')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        #[Assert\Email]
        public readonly string $email,
        #[OA\Property(example: 'My message')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 5000)]
        public readonly string $message,
        #[OA\Property(example: 'RES001')]
        #[Assert\Length(min: 1, max: 255)]
        public readonly ?string $reservationNumber = null,
        #[OA\Property(example: 1)]
        #[Assert\Positive]
        public readonly ?int $hotelId = null,
        #[OA\Property(example: 1)]
        #[Assert\Positive]
        public readonly ?int $languageId = null,
    ) {
    }
}
