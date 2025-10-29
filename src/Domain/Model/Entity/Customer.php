<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore-line

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 50)]
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
