<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Ticket;

use MobilityWork\Domain\Model\Entity\DomainConfig;
use MobilityWork\Domain\Model\Entity\Hotel;
use MobilityWork\Domain\Model\Entity\Language;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCustomerTicketRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $firstName,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $lastName,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 50)]
        public readonly string $phoneNumber,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        #[Assert\Email]
        public readonly string $email,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 5000)]
        public readonly string $message,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $reservationNumber,
        #[Assert\NotNull]
        #[Assert\Valid]
        public readonly Hotel $hotel,
        #[Assert\NotNull]
        #[Assert\Valid]
        public readonly Language $language,
        public readonly DomainConfig $domainConfig,
    ) {
    }
}
