<?php

declare(strict_types=1);

namespace MobilityWork\Entity;

use Doctrine\ORM\Mapping as ORM;
use MobilityWork\Repository\CustomerRepository;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore-line

    #[ORM\Column(length: 255)]
    private ?string $simplePhoneNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSimplePhoneNumber(): ?string
    {
        return $this->simplePhoneNumber;
    }

    public function setSimplePhoneNumber(string $simplePhoneNumber): static
    {
        $this->simplePhoneNumber = $simplePhoneNumber;

        return $this;
    }
}
