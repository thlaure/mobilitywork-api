<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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
